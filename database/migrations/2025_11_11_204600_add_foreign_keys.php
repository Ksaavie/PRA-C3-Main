<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->foreign('creator_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('team_id')
                ->references('id')
                ->on('teams')
                ->onUpdate('cascade');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->foreign('team1_id')
                ->references('id')
                ->on('teams')
                ->onUpdate('cascade');
            
            $table->foreign('team2_id')
                ->references('id')
                ->on('teams')
                ->onUpdate('cascade');
            
            $table->foreign('referee_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
        });

        Schema::table('goals', function (Blueprint $table) {
            $table->foreign('player_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
            
            $table->foreign('match_id')
                ->references('id')
                ->on('matches')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
            $table->dropForeign(['match_id']);
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['team1_id']);
            $table->dropForeign(['team2_id']);
            $table->dropForeign(['referee_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['creator_id']);
        });
    }
};

