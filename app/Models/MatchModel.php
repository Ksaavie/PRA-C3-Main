<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchModel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'matches';

    protected $fillable = [
        'team1_id',
        'team2_id',
        'team1_score',
        'team2_score',
        'field',
        'referee_id',
        'time',
    ];

    protected $casts = [
        'team1_score' => 'integer',
        'team2_score' => 'integer',
    ];

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    public function referee()
    {
        return $this->belongsTo(User::class, 'referee_id');
    }

    public function goals()
    {
        return $this->hasMany(Goal::class, 'match_id');
    }
}

