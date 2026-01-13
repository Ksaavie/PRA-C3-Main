<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TournamentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $tournaments = Tournament::withCount('teams')
            ->orderBy('start_date', 'desc')
            ->orderBy('name')
            ->get();

        $userTeams = Auth::check() ? Auth::user()->teams : collect();

        return view('toernooien', compact('tournaments', 'userTeams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tournaments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'max_teams' => ['nullable', 'integer', 'min:1'],
        ]);

        Tournament::create($validated);

        return redirect()->route('tournaments.create')->with('status', 'Toernooi aangemaakt.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tournament $tournament): View
    {
        $tournament->load('teams.creator', 'matches.homeTeam', 'matches.awayTeam');
        $userTeam = Auth::check() ? Auth::user()->team : null;
        $isRegistered = $userTeam && $tournament->teams->contains($userTeam);

        return view('tournaments.show', compact('tournament', 'userTeam', 'isRegistered'));
    }

    /**
     * Register team for tournament
     */
    public function registerTeam(Request $request, Tournament $tournament): RedirectResponse
    {
        $user = Auth::user();

        if (!$user->team) {
            return redirect()->route('tournaments.show', $tournament)
                ->with('error', 'Je hebt geen team. Maak eerst een team aan.');
        }

        $team = $user->team;

        // Check if already registered
        if ($tournament->teams->contains($team)) {
            return redirect()->route('tournaments.show', $tournament)
                ->with('error', 'Je team is al ingeschreven voor dit toernooi.');
        }

        // Check max teams limit
        if ($tournament->max_teams && $tournament->teams()->count() >= $tournament->max_teams) {
            return redirect()->route('tournaments.show', $tournament)
                ->with('error', 'Dit toernooi heeft het maximum aantal teams bereikt.');
        }

        $tournament->teams()->attach($team);

        return redirect()->route('tournaments.show', $tournament)
            ->with('status', 'Team succesvol ingeschreven voor dit toernooi.');
    }

    /**
     * Unregister team from tournament
     */
    public function unregisterTeam(Request $request, Tournament $tournament): RedirectResponse
    {
        $user = Auth::user();

        if (!$user->team) {
            return redirect()->route('tournaments.show', $tournament)
                ->with('error', 'Je hebt geen team.');
        }

        $team = $user->team;

        if (!$tournament->teams->contains($team)) {
            return redirect()->route('tournaments.show', $tournament)
                ->with('error', 'Je team is niet ingeschreven voor dit toernooi.');
        }

        $tournament->teams()->detach($team);

        return redirect()->route('tournaments.show', $tournament)
            ->with('status', 'Team uitgeschreven van dit toernooi.');
    }

    /**
     * Register a specific team for tournament (from teams list)
     */
    public function registerTeamFromList(Request $request, Tournament $tournament): RedirectResponse
    {
        $validated = $request->validate([
            'team_id' => ['required', 'exists:teams,id'],
        ]);

        $user = Auth::user();
        $team = Team::findOrFail($validated['team_id']);

        // Check if user is the creator of this team
        if ($team->creator_id !== $user->id) {
            return redirect()->route('toernooien')
                ->with('error', 'Je kunt alleen teams inschrijven die je zelf hebt aangemaakt.');
        }

        // Check if already registered
        if ($tournament->teams->contains($team)) {
            return redirect()->route('toernooien')
                ->with('error', 'Dit team is al ingeschreven voor dit toernooi.');
        }

        // Check max teams limit
        if ($tournament->max_teams && $tournament->teams()->count() >= $tournament->max_teams) {
            return redirect()->route('toernooien')
                ->with('error', 'Dit toernooi heeft het maximum aantal teams bereikt.');
        }

        $tournament->teams()->attach($team);

        return redirect()->route('toernooien')
            ->with('status', "Team '{$team->name}' succesvol ingeschreven voor '{$tournament->name}'.");
    }

    /**
     * Generate tournament schedule
     */
    public function generateSchedule(Request $request, Tournament $tournament): RedirectResponse
    {
        $validated = $request->validate([
            'num_fields' => ['required', 'integer', 'min:1'],
        ]);

        $teams = $tournament->teams()->get();
        $numTeams = $teams->count();

        if ($numTeams < 2) {
            return redirect()->route('tournaments.show', $tournament)
                ->with('error', 'Er zijn minimaal 2 teams nodig om een schema te genereren.');
        }

        // Check if schedule already exists
        if ($tournament->matches()->exists()) {
            return redirect()->route('tournaments.show', $tournament)
                ->with('error', 'Er bestaat al een schema voor dit toernooi.');
        }

        $matches = [];
        $numFields = $validated['num_fields'];

        // Generate round-robin schedule (each team plays every other team once)
        $allMatches = [];
        for ($i = 0; $i < $numTeams; $i++) {
            for ($j = $i + 1; $j < $numTeams; $j++) {
                $allMatches[] = [
                    'team1_id' => $teams[$i]->id,
                    'team2_id' => $teams[$j]->id,
                ];
            }
        }

        // Schedule matches in time slots based on number of fields
        $timeSlot = 1;
        $matchIndex = 0;
        while ($matchIndex < count($allMatches)) {
            $matchesInSlot = min($numFields, count($allMatches) - $matchIndex);

            for ($field = 1; $field <= $matchesInSlot; $field++) {
                $match = $allMatches[$matchIndex];
                $matches[] = [
                    'team1_id' => $match['team1_id'],
                    'team2_id' => $match['team2_id'],
                    'field' => 'Veld ' . $field,
                    'time' => 'Tijdslot ' . $timeSlot,
                    'referee_id' => null,
                ];
                $matchIndex++;
            }
            $timeSlot++;
        }

        // Create matches in database
        foreach ($matches as $matchData) {
            $tournament->matches()->create($matchData);
        }

        return redirect()->route('tournaments.show', $tournament)
            ->with('status', 'Schema succesvol gegenereerd.');
    }
}
