<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'player_id',
        'match_id',
        'minute',
    ];

    protected $casts = [
        'minute' => 'integer',
    ];

    public function match()
    {
        return $this->belongsTo(MatchModel::class, 'match_id');
    }

    public function player()
    {
        return $this->belongsTo(User::class, 'player_id');
    }
}

