<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeamController extends Controller
{
    /**
     * Toon teams overzicht.
     */
    public function index(): View
    {
        $teams = Team::orderBy('name')->get();

        $teams->transform(function ($team) {
            $team->players_count = is_array($team->players) ? count($team->players) : 0;
            return $team;
        });

        return view('voetbal.index', compact('teams'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->only(['name', 'description', 'players']);

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'players' => 'nullable|array',
            'players.*' => 'integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $team = new Team();
        $team->name = $data['name'];
        $team->description = $data['description'] ?? null;
        $team->players = $data['players'] ?? [];
        $team->creator_id = auth()->id() ?? null;
        $team->save();

        return redirect()->route('home')->with('success', 'Team aangemaakt.');
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        $data = $request->only(['name', 'description', 'players']);

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'players' => 'nullable|array',
            'players.*' => 'integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $team->name = $data['name'];
        $team->description = $data['description'] ?? null;
        $team->players = $data['players'] ?? [];
        $team->save();

        return redirect()->route('home')->with('success', 'Team bijgewerkt.');
    }

    public function destroy(Team $team): RedirectResponse
    {
        $team->delete();
        return redirect()->route('home')->with('success', 'Team verwijderd.');
    }

    public function generateSchedule(Request $request)
    {
        $request->validate([
            'fields' => 'required|integer|min:1',
        ]);

        $fields = (int) $request->input('fields');
        $teams = Team::orderBy('name')->get()->toArray();

        if (count($teams) < 2) {
            return redirect()->back()->with('error', 'Minstens 2 teams zijn nodig om een schema te genereren.');
        }

        $teamIds = array_map(fn($t) => $t['id'], $teams);
        $teamNamesById = array_column($teams, 'name', 'id');

        $schedule = $this->roundRobinSchedule($teamIds, $fields);

        foreach ($schedule as &$round) {
            foreach ($round['timeslots'] as &$timeslot) {
                foreach ($timeslot['matches'] as &$m) {
                    $m['home_name'] = $m['home'] ? ($teamNamesById[$m['home']] ?? 'BYE') : 'BYE';
                    $m['away_name'] = $m['away'] ? ($teamNamesById[$m['away']] ?? 'BYE') : 'BYE';
                }
            }
        }

        return view('wedstrijdschema.index', [
            'schedule' => $schedule,
            'fields' => $fields,
        ]);
    }

    protected function roundRobinSchedule(array $teams, int $fields): array
    {
        $n = count($teams);
        if ($n % 2 !== 0) $teams[] = null; // BYE
        $n = count($teams);

        $rounds = $n - 1;
        $half = $n / 2;
        $teamsArr = $teams;
        $schedule = [];

        for ($round = 0; $round < $rounds; $round++) {
            $matches = [];
            for ($i = 0; $i < $half; $i++) {
                $t1 = $teamsArr[$i];
                $t2 = $teamsArr[$n - 1 - $i];

                if ($t1 === null || $t2 === null) {
                    $matches[] = ['home' => $t1, 'away' => $t2];
                } else {
                    $matches[] = ['home' => $t1, 'away' => $t2];
                }
            }

            $timeslots = array_map(fn($chunk) => ['matches' => $chunk], array_chunk($matches, $fields));
            foreach ($timeslots as $ti => &$tsMatches) {
                foreach ($tsMatches['matches'] as $mi => &$m) {
                    $m['field'] = ($mi % $fields) + 1;
                    $m['round'] = $round + 1;
                    $m['timeslot'] = $ti + 1;
                }
            }

            $schedule[] = [
                'round_number' => $round + 1,
                'timeslots' => $timeslots,
            ];

            $fixed = $teamsArr[0];
            $rest = array_slice($teamsArr, 1);
            array_unshift($rest, array_pop($rest));
            $teamsArr = array_merge([$fixed], $rest);
        }

        return $schedule;
    }

    public function generateBracket()
    {
        $teams = Team::orderBy('name')->get()->toArray();
        $teamIds = array_column($teams, 'id');
        $teamNames = array_column($teams, 'name', 'id');

        $bracket = $this->buildKnockoutBracket($teamIds);

        foreach ($bracket as &$round) {
            foreach ($round['matches'] as &$m) {
                $m['home_name'] = $m['home'] ? ($teamNames[$m['home']] ?? 'TBD') : 'TBD';
                $m['away_name'] = $m['away'] ? ($teamNames[$m['away']] ?? 'TBD') : 'TBD';
            }
        }

        return view('tournaments.bracket', compact('bracket')); // <-- fix view naam
    }

    protected function buildKnockoutBracket(array $teams): array
    {
        $count = count($teams);
        $nextPow2 = 1;
        while ($nextPow2 < $count) $nextPow2 <<= 1;
        $byesToAdd = $nextPow2 - $count;
        for ($i = 0; $i < $byesToAdd; $i++) $teams[] = null;

        $rounds = [];
        $current = $teams;
        while (count($current) > 1) {
            $matches = [];
            for ($i = 0; $i < count($current); $i += 2) {
                $matches[] = ['home' => $current[$i], 'away' => $current[$i+1] ?? null];
            }
            $rounds[] = ['matches' => $matches];
            $current = array_fill(0, ceil(count($matches)), null);
        }

        return $rounds;
    }

    public function tournamentsView(): View
    {
        $teams = Team::orderBy('name')->get();
        $bracket = $this->buildKnockoutBracket(array_column($teams->toArray(), 'id'));

        $teamNames = array_column($teams->toArray(), 'name', 'id');
        foreach ($bracket as &$round) {
            foreach ($round['matches'] as &$m) {
                $m['home_name'] = $m['home'] ? ($teamNames[$m['home']] ?? 'TBD') : 'TBD';
                $m['away_name'] = $m['away'] ? ($teamNames[$m['away']] ?? 'TBD') : 'TBD';
            }
        }

        return view('tournaments.bracket', compact('bracket')); // <-- fix view naam consistent
    }
}
