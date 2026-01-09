<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TeamController::class, 'index'])->name('home');
Route::get('/dashboard', function () {
    return redirect()->route('home'); // Of: return view('dashboard');
})->name('dashboard');

Route::get('/toernooien', [TournamentController::class, 'index'])->name('toernooien');
Route::get('/tournaments/{tournament}', [TournamentController::class, 'show'])->name('tournaments.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::resource('teams', TeamController::class)->except(['index','store']);
    
    // Team registration for tournaments
    Route::post('/tournaments/{tournament}/register', [TournamentController::class, 'registerTeam'])->name('tournaments.register');
    Route::post('/tournaments/{tournament}/unregister', [TournamentController::class, 'unregisterTeam'])->name('tournaments.unregister');
    
    Route::middleware('admin')->group(function () {
        Route::get('/admin', function () {
            return view('admin.index');
        });
        
        // Tournament management (admin only)
        Route::get('/tournaments/create', [TournamentController::class, 'create'])->name('tournaments.create');
        Route::post('/tournaments', [TournamentController::class, 'store'])->name('tournaments.store');
    });
});

require __DIR__.'/auth.php';
