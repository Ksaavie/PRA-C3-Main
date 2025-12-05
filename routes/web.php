<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TeamController::class, 'index'])->name('home');
Route::get('/dashboard', function () {
    return redirect()->route('home'); // Of: return view('dashboard');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::resource('teams', TeamController::class)->except(['index','store']);
    
    Route::middleware('admin')->group(function () {
        Route::get('/admin', function () {
            return view('admin.index');
        });
    });
});

require __DIR__.'/auth.php';
