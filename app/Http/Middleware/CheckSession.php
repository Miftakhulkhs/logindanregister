<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckSession
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $lastActivity = session('last_activity');

            // Tambahkan log
            Log::info('Last Activity: ' . ($lastActivity ? $lastActivity : 'None'));
            Log::info('Current Time: ' . now());

            // Cek apakah sesi telah kadaluarsa
            if ($lastActivity && now()->diffInMinutes($lastActivity) > 2) {
                // Logout pengguna dan hapus sesi
                Log::info('Session expired, logging out user.');
                Auth::logout();
                session()->flush();
                return redirect()->route('login.form')->with('error', 'Sesi telah kedaluwarsa. Silakan login kembali.');
            }

            // Perbarui waktu aktivitas terakhir
            session(['last_activity' => now()]);

            // Periksa apakah OTP sudah diverifikasi
            if ($user->otp_verified_at === null) {
                Log::info('OTP not verified, redirecting to OTP verification.');
                return redirect()->route('otp.verify.form')->with('info', 'Silakan verifikasi OTP sebelum melanjutkan.');
            }
        }

        return $next($request);
    }
}
