<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TugasController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink']);

Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Route::middleware('auth:sanctum')->post('/Create_Tugas', [TugasController::class, 'CreateTugas']);
Route::middleware('auth:sanctum')->group(function () {

    Route::put('update-profile', [ProfileController::class, 'updateProfile']);
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::post('/profile', [ProfileController::class, 'updateProfile']);
    // tugas
   Route::get('/get-Tugas', [TugasController::class, 'getTugas']);
    Route::post('/Create_Tugas', [TugasController::class, 'CreateTugas']);
    Route::get('/get-Tugas/{id}', [TugasController::class, 'show']); // Untuk Show Satuan
    Route::put('/Update_Tugas/{id}', [TugasController::class, 'update']); // Untuk Edit
    Route::delete('/Delete_Tugas/{id}', [TugasController::class, 'destroy']); // Untuk Hapus
    Route::patch('/update-status-tugas/{id}', [TugasController::class, 'updateStatus']);

    // kategori
    Route::get('/get-kategori', [KategoriController::class, 'getKategori']);
    Route::post('/kategori', [KategoriController::class, 'store']);
    Route::get('/kategori/{id}', [KategoriController::class, 'show']);    // Untuk Show Satuan
    Route::put('/kategori/{id}', [KategoriController::class, 'update']);  // Untuk Edit/Update
    Route::delete('/kategori/{id}', [KategoriController::class, 'destroy']); // Untuk Hapus
});