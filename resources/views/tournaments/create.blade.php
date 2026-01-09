@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">Maak toernooi aan</h1>

                <form method="POST" action="{{ route('tournaments.store') }}" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Naam')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Description -->
                    <div>
                        <x-input-label for="description" :value="__('Beschrijving')" />
                        <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <!-- Start Date -->
                    <div>
                        <x-input-label for="start_date" :value="__('Startdatum (optioneel)')" />
                        <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date')" />
                        <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                    </div>

                    <!-- End Date -->
                    <div>
                        <x-input-label for="end_date" :value="__('Einddatum (optioneel)')" />
                        <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="old('end_date')" />
                        <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                    </div>

                    <!-- Max Teams -->
                    <div>
                        <x-input-label for="max_teams" :value="__('Maximum aantal teams (optioneel)')" />
                        <x-text-input id="max_teams" class="block mt-1 w-full" type="number" name="max_teams" :value="old('max_teams')" min="1" />
                        <x-input-error :messages="$errors->get('max_teams')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>
                            {{ __('Toernooi aanmaken') }}
                        </x-primary-button>

                        <a href="{{ route('toernooien') }}" class="text-gray-600 hover:text-gray-800">
                            Annuleren
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
