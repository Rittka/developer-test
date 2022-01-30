<?php

namespace App\Models;

use App\Models\Comment;
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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
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
        return $this->belongsToMany(Lesson::class , 'lesson_user');
    }

    /**
     * The lessons that a user has watched.
     */
    public function watched()
    {
        return $this->belongsToMany(Lesson::class)->wherePivot('watched', true);
    }

    public function lastLessonWatchedAchievement(){
        return $this->belongsTo(Achievement::class)->where('type' , 'LESSON_WATCHED');
    }
    public function LastCommentWrittenAchievement(){
        return $this->belongsTo(Achievement::class)->where('type' , 'COMMENT_WRITTEN');
    }

    public function lessonsWatchedAchievementsCount(){
        $watched = $this->watched()->count();
        $achievements = Achievement::where('type', 'LESSON_WATCHED')
                                    ->where('goal' , '<=' , $watched)
                                    ->count();
        return $achievements;
    }

    public function commentsWrittenAchievementsCount(){
        $written = $this->comments()->count();
        $achievements = Achievement::where('type', 'COMMENT_WRITTEN')
                                    ->where('goal' , '<=' , $written)
                                    ->count();
        return $achievements;
    }




}
