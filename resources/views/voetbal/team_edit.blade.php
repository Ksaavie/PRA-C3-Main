@extends('layouts.app')
@section('content')
<div style="max-width:420px;margin:auto;">
    <h2 style="font-size:1.4rem;font-weight:700;">Bewerk team: {{ $team->name }}</h2>
    <form method="POST" action="{{ route('teams.update', $team) }}" id="edit-team-form" style="display:flex; flex-direction:column; gap:1rem; margin-top:1.2rem;">
        @csrf
        @method('PUT')
        <label>Teamnaam:
            <input type="text" name="name" value="{{ old('name', $team->name) }}" required style="width:100%;padding:.5rem;"/>
        </label>
        <label>Punten:
            <input type="number" name="points" value="{{ old('points', $team->points) }}" min="0" style="width:100%;padding:.5rem;"/>
        </label>
        <div style="margin:0.5rem 0;">
            <label><b>Spelers:</b></label>
            <div id="edit-players-fields" style="display:flex; flex-direction:column; gap:.25rem;">
                @php $oldPlayers = old('players', $team->players->pluck('name')->all()); @endphp
                @foreach($oldPlayers as $i => $spelerNaam)
                    <div style="display:flex; gap:.5rem; align-items:center;">
                        <input type="text" name="players[]" value="{{ $spelerNaam }}" placeholder="Speler" style="padding:.45rem; border:1px solid #d1d5db; border-radius:.375rem; flex:1 1 auto;"/>
                        <button type="button" onclick="removeEditPlayerField(this)" style="background: #fee2e2; color: #b91c1c; border: none; border-radius:.3rem; padding: .25rem .7rem; cursor:pointer;">×</button>
                    </div>
                @endforeach
            </div>
            <button type="button" onclick="addEditPlayerField()" style="margin-top:0.5rem;padding:.35rem .7rem;border:1px solid #d1d5db;border-radius:.375rem;background:#f3f4f6;color:#111827;font-weight:500;cursor:pointer;">+ Speler toevoegen</button>
        </div>
        <button type="submit" style="background:#2563eb;color:#fff;padding:.6rem 1rem; border:none; border-radius:.375rem;font-weight:500;">Opslaan</button>
    </form>
    <script>
        function addEditPlayerField() {
            var fields = document.getElementById('edit-players-fields');
            var input = document.createElement('input');
            input.type = 'text';
            input.name = 'players[]';
            input.placeholder = 'Speler';
            input.style = 'padding:.45rem; border:1px solid #d1d5db; border-radius:.375rem; margin-top:.15rem; flex:1 1 auto;';
            var wrapper = document.createElement('div');
            wrapper.style = 'display:flex; gap:.5rem; align-items:center;';
            var rm = document.createElement('button');
            rm.type = 'button';
            rm.innerText = '×';
            rm.onclick = function() { removeEditPlayerField(rm); };
            rm.style = 'background: #fee2e2; color: #b91c1c; border: none; border-radius:.3rem; padding: .25rem .7rem; cursor:pointer;';
            wrapper.appendChild(input);
            wrapper.appendChild(rm);
            fields.appendChild(wrapper);
        }
        function removeEditPlayerField(btn) {
            btn.parentNode.remove();
        }
    </script>
    @if ($errors->any())
        <div style="color:#b91c1c;margin-top:.75rem;">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif
    <a href="{{ route('teams.show', $team) }}" style="display:inline-block;margin-top:2rem;">← Terug naar team</a>
</div>
@endsection
