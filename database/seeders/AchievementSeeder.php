<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Achievement;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Achievement::create([
            'title' => 'First Lesson Watched',
            'goal'  => 1 ,
            'type'  => 'LESSON_WATCHED'
        ]);
        Achievement::create([
            'title' => '5 Lessons Watched',
            'goal'  => 5 ,
            'type'  => 'LESSON_WATCHED'
        ]);
        Achievement::create([
            'title' => '10 Lessons Watched',
            'goal'  => 10 ,
            'type'  => 'LESSON_WATCHED'
        ]);
        Achievement::create([
            'title' => '25 Lessons Watched',
            'goal'  => 25 ,
            'type'  => 'LESSON_WATCHED'
        ]);
        Achievement::create([
            'title' => '50 Lessons Watched',
            'goal'  => 50 ,
            'type'  => 'LESSON_WATCHED'
        ]);

        Achievement::create([
            'title' => 'First Comment Written',
            'goal'  => 1 ,
            'type'  => 'COMMENT_WRITTEN'
        ]);

        Achievement::create([
            'title' => '3 Comments Written',
            'goal'  => 3 ,
            'type'  => 'COMMENT_WRITTEN'
        ]);

        Achievement::create([
            'title' => '5 Comments Written',
            'goal'  => 5 ,
            'type'  => 'COMMENT_WRITTEN'
        ]);

        Achievement::create([
            'title' => '10 Comments Written',
            'goal'  => 10 ,
            'type'  => 'COMMENT_WRITTEN'
        ]);

        Achievement::create([
            'title' => '20 Comments Written',
            'goal'  => 20 ,
            'type'  => 'COMMENT_WRITTEN'
        ]);
    }
}
