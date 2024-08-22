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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rute untuk menampilkan daftar pengguna
Route::get('/pengguna', [PenggunaApiController::class, 'index']);

// Rute untuk menampilkan detail pengguna berdasarkan ID
Route::get('/pengguna/{id}', [PenggunaApiController::class, 'show']);

// Rute untuk menambahkan pengguna baru
Route::post('/pengguna', [PenggunaApiController::class, 'store']);

// Rute untuk memperbarui data pengguna
Route::put('/pengguna/{id_pengguna}', [PenggunaApiController::class, 'update']);

// Rute untuk menghapus pengguna
Route::delete('/pengguna/{id_pengguna}', [PenggunaApiController::class, 'destroy']);