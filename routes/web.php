<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TelegramController;

Route::get('/cek-session', function () {
    return session()->all();
});

Route::get('/reset-password-form', function (Illuminate\Http\Request $request) {
    return view('auth.reset-password', [
        'token' => $request->token,
        'email' => $request->email,
    ]);
})->name('password.reset');

Route::post('/reset-password-post', [AuthController::class, 'resetPassword'])->name('password.update');

Route::post('/telegram/webhook/{secret}', [TelegramController::class, 'webhook'])
    ->name('telegram.webhook');

Route::fallback(function () {
    return view('welcome');
});
