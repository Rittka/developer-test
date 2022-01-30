<?php

namespace App\Models;

use App\Models\Comment;
use App\Models\Achievement;
use App\Models\Badge;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;


use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'total_achievement'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $unLockedAchievement = [];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The comments that belong to the user.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The lessons that a user has access to.
     */
    public function lessons()
    {
        return $this->belongsToMany(Lesson::class);
    }

    /**
     * The lessons that a user has watched.
     */
    public function watched()
    {
        return $this->belongsToMany(Lesson::class)->wherePivot('watched', true);
    }

    function currentAchievement($type){
        if($this->isCommentWritten($type))
            return $this->belongsTo(Achievement::class , 'comment_achievement_id')->where('type' , 'COMMENT_WRITTEN');
        else{
            return $this->belongsTo(Achievement::class , 'lesson_achievement_id')->where('type' , 'LESSON_WATCHED');
        }
    }

    function isCommentWritten($type){
        return ($type == 'COMMENT_WRITTEN') ? true : false ;
    }

    function earnedAchievementsCount($achievement_type){
        if($this->isCommentWritten($achievement_type)){
            if(isset($this->comment_achievement_id)){
                $written = $this->comments()->count();
                $count = Achievement::where('type', 'COMMENT_WRITTEN')
                ->where('goal' , '<=' , $written)
                ->count();
            }else{
                $count = 0;
            }
        }else{
            if(isset($this->lesson_achievement_id)){
                $watched = $this->watched()->count();
                $count = Achievement::where('type', 'LESSON_WATCHED')
                ->where('goal' , '<=' , $watched)
                ->count();
            }else{
                $count = 0;
            }

        }

    }


    public function badge(){
        return $this->belongsTo(Badge::class , 'badge_id');
    }

    function nextBadge(){
        $curren_badge = $this->badge();
        return Badge::where('goal' , '>' ,$curren_badge->goal )->orderBy('goal')->first();
    }



    public function totalEarnedAchievementsCount(){
        $lesson_achievements = earnedAchievementsCount('LESSON_WATCHED');
        $comment_achievements = earnedAchievementsCount('COMMENT_WRITTEN');

        $total = $lesson_achievements + $comment_achievements;

        return $total;
    }

    function nextAchievement($type){
        $curren_achievement = $this->currentAchievement($type);
        return Achievement::where('goal' , '>' ,$curren_achievement->goal )
                            ->where('type' , $curren_achievement->type)->orderBy('goal')->first();
    }


    function isNewAchievement($type){
        if($this->isCommentWritten($type)){
            $count = $this->comments->count();
        }else{
            $count = $this->watched()->count();
        }
        $achievement = $this->currentAchievement($type);
        if($count > $achievement->goal){
            $next_achievement = $this->nextAchievement($type);
            if($count >= $next_achievement->goal){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }


    }






}
