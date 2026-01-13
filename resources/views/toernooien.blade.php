@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">Toernooien</h1>
                
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

                @if($tournaments->isEmpty())
                    <p class="text-gray-500">Er zijn nog geen toernooien beschikbaar.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($tournaments as $tournament)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 p-6 h-full flex flex-col">
                                <a href="{{ route('tournaments.show', $tournament) }}" class="flex-grow">
                                    <div class="flex-grow">
                                        <h2 class="text-xl font-semibold text-gray-800 mb-2 hover:text-indigo-600">
                                            {{ $tournament->name }}
                                        </h2>
                                        @if($tournament->description)
                                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                                {{ $tournament->description }}
                                            </p>
                                        @endif
                                        @if($tournament->start_date)
                                            <p class="text-gray-500 text-sm">
                                                <strong>Start:</strong> {{ $tournament->start_date->format('d-m-Y') }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <p class="text-gray-700 font-medium">
                                            Team count: {{ $tournament->teams_count }}
                                        </p>
                                    </div>
                                </a>
                                
                                @auth
                                    @if($userTeams->isNotEmpty())
                                        <div class="mt-4 pt-4 border-t border-gray-200">
                                            <form method="POST" action="{{ route('tournaments.register-team', $tournament) }}" class="flex gap-2">
                                                @csrf
                                                <select name="team_id" class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required>
                                                    <option value="">Selecteer team...</option>
                                                    @foreach($userTeams as $team)
                                                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                                                    @endforeach
                                                </select>
                                                <x-primary-button type="submit" class="text-xs px-3 py-2">
                                                    Inschrijven
                                                </x-primary-button>
                                            </form>
                                        </div>
                                    @endif
                                @endauth
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
