<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::with('creator')
            ->withCount('players')
            ->orderBy('name')
            ->get();

        return view('voetbal.index', compact('teams'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'unique:teams,name'],
            'points' => ['nullable', 'integer', 'min:0'],
            'players' => ['array'],
            'players.*' => ['nullable', 'string', 'max:255'],
        ]);

        $team = Team::create([
            'name' => $validated['name'],
            'points' => $validated['points'] ?? 0,
            'creator_id' => Auth::id(),
        ]);

        // Spelers als User toevoegen/koppelen (alleen als er een naam is ingevuld)
        foreach ($validated['players'] ?? [] as $playerName) {
            if ($playerName) {
                // Uniek, geldig emailadres genereren obv naam + random string
                $uniquePart = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $playerName)) . uniqid();
                $email = $uniquePart . '@team.local';

                // Standaard wachtwoord, gehashed
                $password = bcrypt('password');

                $team->players()->create([
                    'name' => $playerName,
                    'email' => $email,
                    'password' => $password,
                    'admin' => false,
                    'team_id' => $team->id,
                ]);
            }
        }
        return redirect()->route('home')->with('status', 'Team aangemaakt.');
    }

    public function show(Team $team)
    {
        $team->load('creator', 'players');
        return view('voetbal.team', compact('team'));
    }

    public function edit(Team $team)
    {
        $team->load('creator', 'players');
        return view('voetbal.team_edit', compact('team'));
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'unique:teams,name,' . $team->id],
            'points' => ['nullable', 'integer', 'min:0'],
            'players' => ['array'],
            'players.*' => ['nullable', 'string', 'max:255'],
        ]);
        $team->update([
            'name' => $validated['name'],
            'points' => $validated['points'] ?? 0,
        ]);

        $newPlayers = collect($validated['players'] ?? [])->filter(fn($name) => !empty($name))->values();
        $currentPlayers = $team->players()->get();

        // Update bestaande, verwijder ontbrekende, voeg toe wat nieuw is
        // 1. Verwijder spelers die niet meer in de lijst staan
        $namesForUpdate = $newPlayers->all();
        foreach ($currentPlayers as $existingUser) {
            if (!in_array($existingUser->name, $namesForUpdate)) {
                $existingUser->delete();
            }
        }
        // 2. Voeg nieuwe toe die nog niet bestaan (zelfde naam = zelfde speler, simpele naamkoppeling)
        $currentNames = $currentPlayers->pluck('name')->all();
        foreach ($newPlayers as $playerName) {
            if (!in_array($playerName, $currentNames)) {
                $uniquePart = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $playerName)) . uniqid();
                $email = $uniquePart . '@team.local';
                $password = bcrypt('password');
                $team->players()->create([
                    'name' => $playerName,
                    'email' => $email,
                    'password' => $password,
                    'admin' => false,
                    'team_id' => $team->id,
                ]);
            }
        }
        // 3. Update bestaande namen (optioneel): N.v.t., want naamaanpassing betekent nieuw user record, tenzij je matching op id wilt.
        return redirect()->route('teams.show', $team)->with('status', 'Team en spelers bijgewerkt.');
    }

    public function destroy(Team $team): RedirectResponse
    {
        $team->delete();
        return redirect()->route('home')->with('status', 'Team verwijderd.');
    }
}


