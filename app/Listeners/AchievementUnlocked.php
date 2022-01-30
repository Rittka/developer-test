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
        $count = $user->comments->count();



    }
    public function onLessonWatched($lesson , $user) {

    }


    public function subscribe($events)
    {
        return [
            CommentWritten::class   => 'onCommentWritten',
            LessonWatched::class    =>'onLessonWatched'
        ];

    }
}
