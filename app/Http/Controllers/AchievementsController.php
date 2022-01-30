<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    public function index(User $user)
    {   $unlocked_achievements = $this->getUnlockedAchievementsArray($user->getTotalUnlockedAchievement());;
        $next_available_achievements = $this->getNextAvailableAchievementArray($user);
        $current_badge_title = $user->badge->title;

        $next_badge = $user->nextBadge();
        $next_badge_title = $next_badge->title;
        $remaing_to_unlock_next_badge = $next_badge->goal - $user->totalEarnedAchievementsCount();

        return response()->json([
            'unlocked_achievements' => $unlocked_achievements,
            'next_available_achievements' => $next_available_achievements,
            'current_badge' => $current_badge_title,
            'next_badge' => $next_badge_title,
            'remaing_to_unlock_next_badge' => $remaing_to_unlock_next_badge
        ]);
    }

    function getNextAvailableAchievementArray($user){
        $next_available_achievements_array = [];
        $next_comment_achievement = $user->nextAchievement('COMMENT_WRITTEN');
        if(isset($next_comment_achievement)){
            $next_available_achievements_array[] = $next_comment_achievement->title ;
        }

        $next_lesson_achievement = $user->nextAchievement('LESSON_WATCHED');
        if(isset($next_lesson_achievement)){
            $next_available_achievements_array[] = $next_lesson_achievement->title ;
        }

        return $next_available_achievements_array;

    }

    function getUnlockedAchievementsArray($achievements){
        $title_array = [];
        foreach($achievements as $achievement){
            $title_array[] = $achievement->title;
        }

        return $title_array;
    }


}
