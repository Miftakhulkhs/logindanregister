<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenggunaApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Pendaftaran Pengguna
Route::post('/register', [PenggunaApiController::class, 'register'])->name('api.register');

// Verifikasi OTP
Route::post('/otp/verify', [PenggunaApiController::class, 'verifyOtp'])->name('api.otp.verify');

// Minta OTP Baru
Route::post('/request-new-otp', [PenggunaApiController::class, 'requestNewOtp'])->name('api.requestNewOtp');

// Login Pengguna
Route::post('/login', [PenggunaApiController::class, 'login']);

// Logout Pengguna
Route::post('/logout', [PenggunaApiController::class, 'logout'])->name('api.logout');
