<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ChatController;

Route::get('/', [ChatController::class, 'index'])->name('home');
Route::post('/import', [ChatController::class, 'import'])->name('import');
Route::post('/ask', [ChatController::class, 'ask'])->name('ask');
