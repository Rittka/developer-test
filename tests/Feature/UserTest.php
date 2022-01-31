<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Listeners\AchievementUnlocked;
use Illuminate\Support\Facades\Event;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Comment;

class UserTest extends TestCase
{

    public function test_example()
    {
        $this->assertTrue(true);


    }

    public function test_init_user_data(){
        $user = User::factory()->hasAttached(Lesson::factory()->count(1),
                                            ['watched' => 0])->create();

        $this->assertTrue($user->isFirstAchievement('COMMENT_WRITTEN'));
        $this->assertTrue($user->isFirstAchievement('LESSON_WATCHED'));
        $this->assertEquals(0 ,$user->watched()->count());
        $this->assertEquals(0 ,$user->comments()->count());
        $this->assertEquals(0 ,count($user->getTotalUnlockedAchievement()));
        $this->assertEquals(0 ,$user->earnedAchievementsCount('COMMENT_WRITTEN'));
        $this->assertEquals(0 ,$user->earnedAchievementsCount('LESSON_WATCHED'));
        $this->assertEquals('Beginner' ,$user->badge->title);
        $this->assertEquals('Intermediate' ,$user->nextBadge()->title);
        $this->assertEquals('First Lesson Watched' ,$user->nextAchievement('LESSON_WATCHED')->title);
        $this->assertEquals('First Comment Written' ,$user->nextAchievement('COMMENT_WRITTEN')->title);


    }

    public function test_write_comment_event(){
        Event::fake();
        $comment = Comment::factory()->create();
        CommentWritten::dispatch($comment);
        Event::assertDispatched(CommentWritten::class);
        Event::assertListening(CommentWritten::class ,AchievementUnlocked::class );
    }

    public function test_watch_lesson_event(){
        Event::fake();
        $lesson = Lesson::factory()->create();
        $user = User::factory()->create();
        LessonWatched::dispatch($lesson , $user);
        Event::assertDispatched(LessonWatched::class);
        Event::assertListening(LessonWatched::class ,AchievementUnlocked::class );
    }
}
