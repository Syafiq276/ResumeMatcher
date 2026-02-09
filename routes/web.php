<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [UploadController::class, 'index'])->name('dashboard');
    Route::post('/analyze', [UploadController::class, 'analyze'])->name('analyze');
    Route::get('/history', [UploadController::class, 'history'])->name('history');
    Route::get('/history/{result}', [UploadController::class, 'show'])->name('history.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
