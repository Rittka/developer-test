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
        'comment_achievement_id',
        'lesson_achievement_id',
        'badge_id'

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

    function getTotalUnlockedAchievement(){
        if($this->earnedAchievementsCount('COMMENT_WRITTEN') > 0){
            $temp = $this->getUnlockedAchievement('COMMENT_WRITTEN',$this->comments()->count());
            if($this->earnedAchievementsCount('LESSON_WATCHED') > 0){
                $total = $this->getUnlockedAchievement('LESSON_WATCHED',$this->watched()->count())->union($temp)->get();
            }else{
                $total = $temp->get();
            }

        }else{
            if($this->earnedAchievementsCount('LESSON_WATCHED') > 0){
                $temp = $this->getUnlockedAchievement('LESSON_WATCHED',$this->watched()->count());
                $total = $temp->get();

            }else{
                $total = [];
            }
        }

        return $total;
    }

    function currentCommentAchievement(){
        return $this->belongsTo(Achievement::class  , 'comment_achievement_id');
    }

    function currentLessonAchievement(){

        return $this->belongsTo(Achievement::class  , 'lesson_achievement_id');
    }


    function isCommentWritten($type){
        return ($type == 'COMMENT_WRITTEN') ? true : false ;
    }

    function getUnlockedAchievement($type , $goal){
        $achievements =  Achievement::where('type', $type)->where('goal' , '<=' , $goal);
        return $achievements;
    }

    function earnedAchievementsCount($achievement_type){
        if($this->isCommentWritten($achievement_type)){
            if(isset($this->comment_achievement_id)){
                $written = $this->comments()->count();
                $count = $this->getUnlockedAchievement($achievement_type , $written)->count();
            }else{
                $count = 0;
            }
        }else{
            if(isset($this->lesson_achievement_id)){
                $watched = $this->watched()->count();
                $count = $this->getUnlockedAchievement($achievement_type , $watched)->count();
            }else{
                $count = 0;
            }

        }

        return $count;

    }


    public function badge(){
        return $this->belongsTo(Badge::class , 'badge_id');
    }

    function nextBadge(){
        $curren_badge = $this->badge;
        if($curren_badge->title == 'Master'){
            return null ;
        }else{

            return Badge::where('goal' , '>' ,$curren_badge->goal )->orderBy('goal')->first();
        }


    }



    public function totalEarnedAchievementsCount(){
        $lesson_achievements = $this->earnedAchievementsCount('LESSON_WATCHED');
        $comment_achievements = $this->earnedAchievementsCount('COMMENT_WRITTEN');

        $total = $lesson_achievements + $comment_achievements;

        return $total;
    }

    function nextAchievement($type){

        if($this->isFirstAchievement($type)){
           return Achievement::where('goal' , 1 )
                                ->where('type' , $type)->first();

        }else{

            if($this->isCommentWritten($type)){
                $curren_achievement = $this->currentCommentAchievement;
            }else{
                $curren_achievement = $this->currentLessonAchievement;
            }



            return Achievement::where('goal' , '>' ,$curren_achievement->goal )
                                ->where('type' , $curren_achievement->type)->orderBy('goal')->first();
        }
    }

    function isFirstAchievement($type){
        if($this->isCommentWritten($type)){
            if(isset($this->comment_achievement_id)){
                return false;
            }else{
                return true;
            }
        }else{
            if(isset($this->lesson_achievement_id)){
                return false;
            }else{
                return true;
            }

        }
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
