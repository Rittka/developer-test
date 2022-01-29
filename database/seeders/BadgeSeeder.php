<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Badge;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Badge::create([
            'title' => 'Beginner',
            'goal'  => 0 ,
        ]);
        Badge::create([
            'title' => 'Intermediate',
            'goal'  => 4 ,
        ]);
        Badge::create([
            'title' => 'Advanced',
            'goal'  => 8 ,
        ]);
        Badge::create([
            'title' => 'Master',
            'goal'  => 10 ,
        ]);
    }
}
