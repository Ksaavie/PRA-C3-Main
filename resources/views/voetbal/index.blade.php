@extends('layouts.app')
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    @auth
        <section style="padding: 1rem; border: 1px solid #e5e7eb; border-radius: .5rem;">
            @if (session('status'))
                <div style="margin-bottom:.5rem; color: #065f46;">{{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('teams.store') }}" id="team-form" style="display:flex; gap:.5rem; align-items:flex-end; flex-wrap:wrap; flex-direction:column; align-items:flex-start;">
                @csrf
                <div style="display:flex; gap:.5rem; width:100%;">
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Teamnaam"
                        style="padding:.5rem; border:1px solid #d1d5db; border-radius:.375rem; flex: 1 1 auto;"
                        required
                    />
                    <input
                        type="number"
                        name="points"
                        value="{{ old('points', 0) }}"
                        min="0"
                        placeholder="Punten"
                        style="padding:.5rem; border:1px solid #d1d5db; border-radius:.375rem; width:120px;"
                    />
                </div>
                <div style="margin-top:.75rem; width:100%;">
                    <label><b>Spelers:</b></label>
                    <div id="players-fields" style="display:flex; flex-direction:column; gap:.25rem;">
                        <input type="text" name="players[]" value="{{ old('players.0') }}" placeholder="Speler 1" style="padding:.45rem; border:1px solid #d1d5db; border-radius:.375rem;" />
                        <input type="text" name="players[]" value="{{ old('players.1') }}" placeholder="Speler 2 (optioneel)" style="padding:.45rem; border:1px solid #d1d5db; border-radius:.375rem;" />
                        <input type="text" name="players[]" value="{{ old('players.2') }}" placeholder="Speler 3 (optioneel)" style="padding:.45rem; border:1px solid #d1d5db; border-radius:.375rem;" />
                    </div>
                    <button type="button" style="margin-top:0.5rem;padding:.35rem .7rem;border:1px solid #d1d5db;border-radius:.375rem;background:#f3f4f6;color:#111827;font-weight:500;cursor:pointer;" onclick="addPlayerField()">+ Speler toevoegen</button>
                </div>
                <button type="submit" style="margin-top:1rem; padding:.5rem .75rem; border:0; background:#111827; color:#fff; border-radius:.375rem;">Team toevoegen</button>
            </form>
            @error('name')
                <div style="color:#b91c1c; margin-top:.25rem;">{{ $message }}</div>
            @enderror
            @error('points')
                <div style="color:#b91c1c; margin-top:.25rem;">{{ $message }}</div>
            @enderror
        </section>
    @endauth

    <section aria-label="Teams" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: .75rem;">
        @forelse(($teams ?? []) as $team)
            <div style="border:1px solid #e5e7eb; border-radius:.5rem; padding:.75rem; position:relative;">
                <a href="{{ route('teams.show', $team) }}" style="text-decoration:none; color:inherit; display:block; font-weight:600;">{{ $team->name }}</a>
                <div style="margin-top:.35rem; font-size:.9rem;">Punten: <strong>{{ $team->points }}</strong></div>
                <div style="font-size:.8rem; color:#6b7280; margin-top:.35rem;">Maker: {{ $team->creator->name ?? 'Onbekend' }}</div>
                <div style="font-size:.8rem; color:#6b7280;">Spelers: {{ $team->players_count }}</div>
                @auth
                    @if (auth()->user()->id === $team->creator_id || auth()->user()->admin)
                        <div style="position:absolute; top:.5rem; right:.5rem; display:flex; gap:.5rem;">
                            <a href="{{ route('teams.edit', $team) }}" style="color:#3b82f6;">Bewerk</a>
                            <form method="POST" action="{{ route('teams.destroy', $team) }}" onsubmit="return confirm('Weet je zeker dat je dit team wilt verwijderen?');">
                                @csrf
                                @method('DELETE')
                                <button style="color:#ef4444; background:none; border:none; cursor:pointer;" type="submit">Verwijder</button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
        @empty
            <div style="color:#6b7280;">Nog geen teams aangemaakt.</div>
        @endforelse
    </section>
</div>

<script>
function addPlayerField() {
    var fields = document.getElementById('players-fields');
    var count = fields.querySelectorAll('input').length + 1;
    var input = document.createElement('input');
    input.type = 'text';
    input.name = 'players[]';
    input.placeholder = 'Speler ' + count + ' (optioneel)';
    input.style = 'padding:.45rem; border:1px solid #d1d5db; border-radius:.375rem; margin-top:.15rem;';
    fields.appendChild(input);
}
</script>
@endsection
