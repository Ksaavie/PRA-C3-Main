<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'points',
        'creator_id',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function players()
    {
        return $this->hasMany(User::class);
    }

    public function homeMatches()
    {
        return $this->hasMany(MatchModel::class, 'team1_id');
    }

    public function awayMatches()
    {
        return $this->hasMany(MatchModel::class, 'team2_id');
    }
}


