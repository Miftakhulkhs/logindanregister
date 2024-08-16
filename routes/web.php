<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\SpkController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
     return redirect()->route('login.form');
});

// Rute pendaftaran pengguna
Route::get('/register', [PenggunaController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [PenggunaController::class, 'register']);
// Rute verifikasi OTP
Route::get('/otp-verify', [PenggunaController::class, 'showOtpForm'])->name('otp.verify.form');
Route::post('/otp-verify', [PenggunaController::class, 'verifyOtp']);

// Rute untuk meminta ulang OTP
Route::post('/otp-request', [PenggunaController::class, 'requestNewOtp'])->name('otp.request');

// Rute login dan logout
Route::get('/login', [PenggunaController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [PenggunaController::class, 'login']);
Route::post('/logout', [PenggunaController::class, 'logout'])->name('logout');

// Rute SPK yang memerlukan autentikasi dan sesi valid
Route::middleware(['auth', 'check.session'])->group(function () {
    Route::get('/spk/form', [SpkController::class, 'showSpkForm'])->name('spk.form');
    Route::post('/spk/store', [SpkController::class, 'store'])->name('spk.store');
});
