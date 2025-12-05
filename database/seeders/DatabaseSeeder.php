<?php

namespace Database\Seeders;

use App\Models\Goal;
use App\Models\MatchModel;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $dummyTeam = Team::create([
            'name' => 'Dummy',
            'points' => 0,
            'creator_id' => 1,
        ]);
        
        $admin = User::create([
            'name' => 'Scheids Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'admin' => true,
            'team_id' => $dummyTeam->id,
        ]);

        $teamAlpha = Team::create([
            'name' => 'Team Alpha',
            'points' => 6,
            'creator_id' => $admin->id,
        ]);

        $teamBeta = Team::create([
            'name' => 'Team Beta',
            'points' => 4,
            'creator_id' => $admin->id,
        ]);

        $admin->team_id = $teamAlpha->id;
        $admin->save();
        
        $dummyTeam->creator_id = $admin->id;
        $dummyTeam->save();

        $player = User::create([
            'name' => 'Aanvaller 1',
            'email' => 'aanvaller1@example.com',
            'password' => Hash::make('password'),
            'admin' => false,
            'team_id' => $teamAlpha->id,
        ]);
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $match = MatchModel::create([
            'team1_id' => $teamAlpha->id,
            'team2_id' => $teamBeta->id,
            'team1_score' => 2,
            'team2_score' => 1,
            'field' => 'Stadion 1',
            'referee_id' => $admin->id,
            'time' => '2025-11-12 19:00',
        ]);

        Goal::create([
            'player_id' => $player->id,
            'match_id' => $match->id,
            'minute' => 23,
        ]);
    }
}
