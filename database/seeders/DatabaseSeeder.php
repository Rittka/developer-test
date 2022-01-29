<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\Badge;
use Database\Seeders\AchievementSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $lessons = Lesson::factory()
            ->count(20)
            ->create();
        $user = User::factory()->count(10)->create();

        $this->call([
            BadgeSeeder::class,
            AchievementSeeder::class,
        ]);
    }
}
