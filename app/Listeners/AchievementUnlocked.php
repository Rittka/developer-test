<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Models\Achievement;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Comment;

class AchievementUnlocked
{
    function __invoke(){

    }
    public function onCommentWritten(Comment $comment) {
        $user_id = $comment->user_id ;
        $user = User::find($user_id);
        $type = 'COMMENT_WRITTEN';
        $commentAchievement = $user->currentCommentAchievement;

        if(isset($commentAchievement)){
           $newAchievement = $user->isNewAchievement($type);
           if($newAchievement){
               $this->unloackAchievement($user , $type);
            }

        }else{
            $earned_achievement = $this->getFirstAchivement($type);
            $this->updateUser($user , $earned_achievement);

        }

    }

    public function onLessonWatched(Lesson $lesson , User $user) {

        $user->lessons()->updateExistingPivot($lesson->id, ['watched' => true]);
        $type = 'LESSON_WATCHED';
        $lesson_achievement = $user->currentLessonAchievement;

        if(isset($lessonAchievement)){
           $newAchievement = $user->isNewAchievement($type);
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

        $earned_achievement = $user->nextAchievement($type);
        $this->updateUser($user , $earned_achievement);
    }

    function updateUser(User $user , Achievement $achievement){
        if($achievement->type == 'COMMENT_WRITTEN'){
            $user->comment_achievement_id   = $earned_achievement->id ;
        }else{
            $user->lesson_achievement_id   = $earned_achievement->id ;
        }
        $user->save();

        $next_badge = $user->nextBadge();
        $user_unlocked_achievement_count = $user->totalEarnedAchievementsCount();

        if($this->CanUnlockBadge($user_unlocked_achievement_count , $next_badge->goal)){
            $this->unlockBadg($user , $next_badge->id);
        }

    }

    function CanUnlockBadge($unlocked_achievement_count ,  $goal){
        if($unlocked_achievement_count >= $goal){
            return true;
        }else{
            return false;
        }
    }

    function unlockBadg(User $user , $new_badge_id){
        $user->badge_id = $new_badge_id;
        $user->save();
    }






    public function subscribe($events)
    {
        return [
            CommentWritten::class   => 'onCommentWritten',
            LessonWatched::class    =>'onLessonWatched'
        ];

    }
}
