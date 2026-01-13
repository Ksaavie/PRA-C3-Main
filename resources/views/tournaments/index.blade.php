@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-xl font-bold mb-4">Toernooien</h1>

    <p>Aantal teams: {{ $teams->count() }}</p>

    <ul class="list-disc pl-6 mt-4">
        @foreach ($teams as $team)
            <li>
                {{ $team->name }}
                ({{ count($team->players ?? []) }} spelers)

            </li>


        @endforeach
    </ul>
                <hr class="my-6">

<h2 class="text-lg font-semibold mb-4">Bracket (knock-out)</h2>

<a href="{{ route('bracket.generate') }}" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded">
    Genereer bracket
</a>
</div>
@endsection
