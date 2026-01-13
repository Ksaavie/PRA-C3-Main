<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = 'teams';

    protected $fillable = [
        'name',
        'description',
        'creator_id',
        'players',
    ];

    // players opgeslagen als JSON, cast naar array
    protected $casts = [
        'players' => 'array',
    ];

    /**
     * Optionele helper: get players count attribute
     */
    public function getPlayersCountAttribute()
    {
        return is_array($this->players) ? count($this->players) : 0;
    }

    // Relaties (bv. creator)
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'creator_id');
    }
}
