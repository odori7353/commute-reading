<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReadingSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ログイン後は記録画面を最初に開く（Breeze の「dashboard」行き先を記録画面へ転送）
Route::get('/dashboard', function () {
    return redirect()->route('record');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // 読書の記録
    Route::get('/record', [ReadingSessionController::class, 'index'])->name('record');
    Route::post('/record/start', [ReadingSessionController::class, 'start'])->name('record.start');
    Route::post('/record/finish', [ReadingSessionController::class, 'finish'])->name('record.finish');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
