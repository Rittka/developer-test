<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('comment_achievement_id')->nullable();
            $table->unsignedBigInteger('lesson_achievement_id')->nullable();
            $table->unsignedBigInteger('badge_id')->default(1);



        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['comment_achievement_id', 'lesson_achievement_id', 'last_badge_id']);

        });
    }
}
