<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\LessonWatched;
use App\Models\User;

class LessonAchievementUnlocked
{

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(LessonWatched $event)
    {
        $this->unlockLessonAchievement($event->user , $event->lesson);
    }

    function unlockLessonAchievement($user , $lesson){


    }
}
