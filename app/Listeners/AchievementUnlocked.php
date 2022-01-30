<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Models\Achievement;
use App\Models\User;

class AchievementUnlocked
{
    public function onCommentWritten($comment) {
        $user_id = $comment->user_id ;
        $user = User::find($user_id);
        $type = 'COMMENT_WRITTEN';

        $commentAchievement = $user->lastAchievement($type);
        $comments_count = $user->comments()->count();
        if(isset($commentAchievement)){
           $newAchievement = $this->isNewAchievement($comments_count , $commentAchievement);
           if($newAchievement){
               $this->unloackAchievement($user , $type);
            }

        }else{
            $earned_achievement = $this->getFirstAchivement($type);
            $this->updateUser($user , $earned_achievement);

        }

    }

    public function onLessonWatched($lesson , $user) {

        $user->lessons()->updateExistingPivot($lesson->id, ['watched' => true]);
        $type = 'LESSON_WATCHED';
        $lesson_achievement = $user->lastAchievement($type);
        $watched_count = $user->watched()->count();

        if(isset($lessonAchievement)){
           $newAchievement = $this->isNewAchievement($watched_count , $lesson_achievement);
           if($newAchievement){
               $this->unloackAchievement($user , $type);
            }else{
                $earned_achievement = $this->getFirstAchivement($type);
                $this->updateUser($user , $earned_achievement);
            }

        }


    }

    function getFirstAchivement($type){
        return Achievement::where('goal' , 1)->where('type' , $type)->first();
    }

    function unloackAchievement(User $user , $type){
        $last_achivement = $user->lastAchievement($type);
        $earned_achievement = $this->nextAchievement($last_achivement);
        $this->updateUser($user , $earned_achievement);
    }

    function updateUser(User $user , Achievement $achievement){
        if($achievement->type == 'COMMENT_WRITTEN'){
            $user->comment_achievement_id   = $earned_achievement->id ;
        }else{
            $user->lesson_achievement_id   = $earned_achievement->id ;
        }
        $user->save();

    }

    function isNewAchievement($count , Achievement $achievement){
        if($count > $achievement->goal){
            $next_achievement = $this->nextAchievement($achievement);
            if($comments_count >= $next_achievement->goal){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }

    function nextAchievement(Achievement $achievement){
        return Achievement::where('goal' , '>' ,$achievement->goal )
                            ->where('type' , $achievement->type)->first();
    }




    public function subscribe($events)
    {
        return [
            CommentWritten::class   => 'onCommentWritten',
            LessonWatched::class    =>'onLessonWatched'
        ];

    }
}
