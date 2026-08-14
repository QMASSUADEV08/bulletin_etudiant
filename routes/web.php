<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/import', [ImportController::class, 'formulaire'])->name('import.formulaire');
    Route::post('/import/apercu', [ImportController::class, 'apercu'])->name('import.apercu');
    Route::post('/import/confirmer', [ImportController::class, 'confirmer'])->name('import.confirmer');
});

require __DIR__.'/auth.php';