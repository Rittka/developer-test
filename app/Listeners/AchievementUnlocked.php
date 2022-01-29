<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\CommentWritten;
use App\Events\LessonWatched;

class AchievementUnlocked
{
    public function onCommentWritten($event) {

    }
    public function onLessonWatched($event) {

    }


    public function subscribe($events)
    {
        $events->listen(
            'App\Events\CommentWritten',
            'App\Listeners\AchievementUnlocked@onCommentWritten'
        );

        $events->listen(
            'App\Events\LessonWatched',
            'App\Listeners\AchievementUnlocked@onLessonWatched'
        );
    }
}
