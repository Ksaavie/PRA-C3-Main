<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tournament extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'max_teams',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'max_teams' => 'integer',
    ];

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'tournament_team');
    }
}
