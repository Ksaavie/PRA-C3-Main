@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-xl font-bold mb-6">Toernooi bracket</h1>

    <div class="flex gap-8 overflow-x-auto">
        @foreach ($bracket as $roundIndex => $round)
            <div>
                <h2 class="font-semibold mb-3">Ronde {{ $roundIndex + 1 }}</h2>

                <ul class="space-y-3">
                    @foreach ($round['matches'] as $match)
                        <li class="border p-2 rounded bg-gray-50">
                            <div>{{ $match['home_name'] ?? 'TBD' }}</div>
                            <div class="text-center text-sm text-gray-500">vs</div>
                            <div>{{ $match['away_name'] ?? 'TBD' }}</div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</div>
@endsection
