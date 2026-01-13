@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-4">
                    <a href="{{ route('toernooien') }}" class="text-indigo-600 hover:text-indigo-800">
                        ← Terug naar toernooien
                    </a>
                </div>

                <h1 class="text-3xl font-bold mb-4">{{ $tournament->name }}</h1>

                @if(session('status'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('status') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        @if($tournament->description)
                            <h2 class="text-xl font-semibold mb-2">Beschrijving</h2>
                            <p class="text-gray-700">{{ $tournament->description }}</p>
                        @endif
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold mb-2">Informatie</h2>
                        <ul class="space-y-2 text-gray-700">
                            @if($tournament->start_date)
                                <li><strong>Startdatum:</strong> {{ $tournament->start_date->format('d-m-Y') }}</li>
                            @endif
                            @if($tournament->end_date)
                                <li><strong>Einddatum:</strong> {{ $tournament->end_date->format('d-m-Y') }}</li>
                            @endif
                            <li><strong>Aantal teams:</strong> {{ $tournament->teams->count() }}</li>
                            @if($tournament->max_teams)
                                <li><strong>Maximum teams:</strong> {{ $tournament->max_teams }}</li>
                            @endif
                        </ul>
                    </div>
                </div>

                @auth
                    @if(Auth::user()->admin && $tournament->matches->isEmpty())
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <h2 class="text-xl font-semibold mb-4">Schema genereren</h2>
                            <form method="POST" action="{{ route('tournaments.generate-schedule', $tournament) }}" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="num_fields" class="block text-sm font-medium text-gray-700">Aantal velden</label>
                                    <input type="number" id="num_fields" name="num_fields" min="1" required
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <x-primary-button type="submit">
                                    Schema genereren
                                </x-primary-button>
                            </form>
                        </div>
                    @endif

                    @if($userTeam)
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h2 class="text-xl font-semibold mb-4">Team inschrijving</h2>
                            <p class="mb-2"><strong>Jouw team:</strong> {{ $userTeam->name }}</p>

                            @if($isRegistered)
                                <p class="text-green-600 mb-4">✓ Je team is ingeschreven voor dit toernooi.</p>
                                <form method="POST" action="{{ route('tournaments.unregister', $tournament) }}" class="inline">
                                    @csrf
                                    <x-danger-button type="submit">
                                        Uitschrijven
                                    </x-danger-button>
                                </form>
                            @else
                                @if($tournament->max_teams && $tournament->teams->count() >= $tournament->max_teams)
                                    <p class="text-red-600 mb-4">Dit toernooi heeft het maximum aantal teams bereikt.</p>
                                @else
                                    <form method="POST" action="{{ route('tournaments.register', $tournament) }}" class="inline">
                                        @csrf
                                        <x-primary-button type="submit">
                                            Inschrijven voor dit toernooi
                                        </x-primary-button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    @else
                        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-yellow-800">Je hebt nog geen team. Maak eerst een team aan om je in te schrijven voor dit toernooi.</p>
                        </div>
                    @endif
                @else
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-blue-800">Log in om je team in te schrijven voor dit toernooi.</p>
                    </div>
                @endauth

                <div>
                    <h2 class="text-xl font-semibold mb-4">Ingeschreven teams ({{ $tournament->teams->count() }})</h2>
                    @if($tournament->teams->isEmpty())
                        <p class="text-gray-500">Nog geen teams ingeschreven.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($tournament->teams as $team)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h3 class="font-semibold text-lg mb-2">
                                        <a href="{{ route('teams.show', $team) }}" class="text-indigo-600 hover:text-indigo-800">
                                            {{ $team->name }}
                                        </a>
                                    </h3>
                                    @if($team->creator)
                                        <p class="text-sm text-gray-600">Maker: {{ $team->creator->name }}</p>
                                    @endif
                                    <p class="text-sm text-gray-600">Punten: {{ $team->points }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if($tournament->matches->isNotEmpty())
                    <div class="mt-8">
                        <h2 class="text-xl font-semibold mb-4">Wedstrijdschema - Bracket Systeem</h2>

                        @php
                            $timeSlots = $tournament->matches->groupBy('time');
                        @endphp

                        @foreach($timeSlots as $timeSlot => $matches)
                            <div class="mb-8">
                                <h3 class="text-lg font-semibold mb-4 bg-blue-100 p-3 rounded">{{ $timeSlot }}</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($matches as $match)
                                        <div class="bg-white border-2 border-gray-200 rounded-lg p-4 shadow-sm">
                                            <div class="text-center mb-3">
                                                <span class="text-sm text-gray-600">{{ $match->field }}</span>
                                            </div>
                                            <div class="space-y-2">
                                                <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                                    <span class="font-medium">{{ $match->homeTeam->name }}</span>
                                                    <span class="text-sm">
                                                        @if($match->team1_score !== null)
                                                            {{ $match->team1_score }}
                                                        @else
                                                            -
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="text-center text-gray-400">vs</div>
                                                <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                                    <span class="font-medium">{{ $match->awayTeam->name }}</span>
                                                    <span class="text-sm">
                                                        @if($match->team2_score !== null)
                                                            {{ $match->team2_score }}
                                                        @else
                                                            -
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
