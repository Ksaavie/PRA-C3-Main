<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\MatchModel;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// 501 API in PHP - Planned Matches
Route::get('/matches/planned', function () {
    $matches = MatchModel::with(['homeTeam', 'awayTeam'])
        ->whereNull('team1_score')
        ->whereNull('team2_score')
        ->get();

    return response()->json($matches->map(function ($match) {
        return [
            'match_id' => $match->id,
            'team1_id' => $match->team1_id,
            'team1_name' => $match->homeTeam ? $match->homeTeam->name : 'Unknown',
            'team2_id' => $match->team2_id,
            'team2_name' => $match->awayTeam ? $match->awayTeam->name : 'Unknown',
            // User only asked for match-id, team-ids, team-names for planned matches
            // but adding time might be useful. Sticking to requirements strictly first,
            // but user said "ac: API toont wedstrijd-id, team-id's en team-namen".
        ];
    }));
});

// 502 API in PHP - Played Matches
Route::get('/matches/played', function () {
    $matches = MatchModel::with(['homeTeam', 'awayTeam'])
        ->whereNotNull('team1_score')
        ->whereNotNull('team2_score')
        ->get();

    return response()->json($matches->map(function ($match) {
        return [
            'match_id' => $match->id,
            'team1_id' => $match->team1_id,
            'team1_name' => $match->homeTeam ? $match->homeTeam->name : 'Unknown',
            'team2_id' => $match->team2_id,
            'team2_name' => $match->awayTeam ? $match->awayTeam->name : 'Unknown',
            'team1_score' => $match->team1_score,
            'team2_score' => $match->team2_score,
        ];
    }));
});
