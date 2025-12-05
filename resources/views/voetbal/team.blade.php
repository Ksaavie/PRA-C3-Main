@extends('layouts.app')
@section('content')
<div style="margin-top:1rem;max-width:480px;text-align:center;margin:auto;">
    @if(session('status'))<div style="color:#065f46;">{{ session('status') }}</div>@endif
    <h2 style="font-size:1.6rem;font-weight:700;">Team: {{ $team->name }}</h2>
    <div>Punten: <strong>{{ $team->points }}</strong></div>
    <div>Maker: {{ $team->creator->name ?? 'Onbekend' }}</div>
    <h4 style="margin-top:1rem; font-weight:600;">Spelers:</h4>
    <ul style="padding-left:1em;">
        @forelse($team->players as $speler)
            <li>{{ $speler->name }}</li>
        @empty
            <li><span style="color:#777;">Geen spelers gekoppeld</span></li>
        @endforelse
    </ul>
    <div style="text-align:center;justify-content:center;margin-top:1.5rem;display:flex;gap:1rem;">
        @auth
            @if (auth()->user()->id === $team->creator_id || auth()->user()->admin)
                <a href="{{ route('teams.edit', $team) }}" style="color:#2563eb;">Bewerk team</a>
                <form method="POST" action="{{ route('teams.destroy', $team) }}" onsubmit="return confirm('Weet je zeker dat je dit team wilt verwijderen?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="color:#b91c1c; background:none;border:none;cursor:pointer;">Verwijder team</button>
                </form>
            @endif
        @endauth
    </div>
    <a href="{{ route('home') }}" style="display:inline-block;margin-top:2rem;">← Terug naar overzicht</a>
</div>
@endsection
