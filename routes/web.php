<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ChatController;

Route::get('/', [ChatController::class, 'index'])->name('home');
Route::post('/ask', [ChatController::class, 'ask'])->name('ask');
Route::get('/chat-response/{id}', [ChatController::class, 'getChatResponse']);
