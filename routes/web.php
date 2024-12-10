<?php

use App\Http\Controllers\DoorprizeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ScanController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Route::get('/', [ScanController::class, 'showRegister'])->name('register');
// Route::post('/', [ScanController::class, 'scanRegister'])->name('register.post');
// Route::post('/cek-karyawan', [ScanController::class, 'checkEmployee']);

// Route::get('/lunch', [ScanController::class, 'showLunch'])->name('lunch');
// Route::post('/lunch', [ScanController::class, 'scanLunch'])->name('lunch.post');

// Route::get('/report', [ScanController::class, 'report'])->name('report');

// Route::get('/doorprize', [DoorprizeController::class, 'index'])->name('doorprize');
// Route::get('/participants', [DoorprizeController::class, 'getParticipants']);
// Route::post('/draw', [DoorprizeController::class, 'draw']);


Route::middleware('auth')->group(function () {
    Route::get('/', [ScanController::class, 'showRegister'])->name('register');
    Route::post('/', [ScanController::class, 'scanRegister'])->name('register.post');
    Route::post('/cek-karyawan', [ScanController::class, 'checkEmployee']);

    Route::get('/lunch', [ScanController::class, 'showLunch'])->name('lunch');
    Route::post('/lunch', [ScanController::class, 'scanLunch'])->name('lunch.post');

    Route::get('/report', [ScanController::class, 'report'])->name('report');

    Route::get('/doorprize', [DoorprizeController::class, 'index'])->name('doorprize');
    Route::get('/participants', [DoorprizeController::class, 'getParticipants']);
    Route::post('/draw', [DoorprizeController::class, 'draw']);
});
