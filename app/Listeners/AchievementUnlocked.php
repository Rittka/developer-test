<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Models\Achievement;

class AchievementUnlocked
{
    public function onCommentWritten($comment) {
        $user_id = $comment->user_id ;
        $user = User::find($user_id);

        $commentAchievement = $user->lastCommentWrittenAchievement();
        $comments_count = $user->comments()->count();
        if(isset($commentAchievement)){

            if($comments_count > $commentAchievement->goal){
                $next_achievement = Achievement::where('goal' , '>' ,$commentAchievement->goal )
                                                ->where('type' , 'COMMENT_WRITTEN')->first();
                if($comments_count >= $next_achievement->goal){
                    $user->comment_achievement_id = $next_achievement->id ;
                    $user->save();
                    $user->unLockedAchievement[] = $next_achievement->title;
                }
            }
        }else{
            $earned_achievement = Achievement::where('goal' , $comments_count )
            ->where('type' , 'COMMENT_WRITTEN')->first();

            $user->comment_achievement_id   = $earned_achievement->id ;
            $user->unLockedAchievement[]    = $earned_achievement->title;
        }



    }

    public function onLessonWatched($lesson , $user) {
        $user->lessons()->updateExistingPivot($lesson->id, ['watched' => true]);


    }


    public function subscribe($events)
    {
        return [
            CommentWritten::class   => 'onCommentWritten',
            LessonWatched::class    =>'onLessonWatched'
        ];

    }
}
