<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Models\Pengguna;

class PenggunaController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|unique:pengguna,username',
            'no_hp' => 'nullable|string|max:20|unique:pengguna,no_hp', // Menambahkan validasi unik untuk no_hp
            'password' => 'required|string|min:8|confirmed',
            'level' => 'required|string|in:Owner,Kepala Produksi,Customer Service',
        ], [
            'no_hp.unique' => 'Nomor HP sudah terdaftar.' // Pesan error khusus untuk nomor HP
        ]);

        // Create new user
        $pengguna = Pengguna::create([
            'id_pengguna' => Pengguna::generateId(),
            'nama' => $request->nama,
            'username' => $request->username,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password),
            'level' => $request->level,
            'otp_verified_at' => now(),
            'otp_created_at' => now(), // Ensure this field exists
        ]);

        // Generate OTP and send it
        $otp = rand(100000, 999999);
        $pengguna->otp = $otp;
        $pengguna->save();

        $response = Http::withHeaders([
            'Authorization' => 'A5!VQAYa3UigYG9kpPpw',
        ])->post('https://api.fonnte.com/send', [
            'target' => $request->no_hp,
            'message' => "Your OTP: " . $otp,
        ]);

        if ($response->successful()) {
            return redirect()->route('otp.verify.form')->with('success', 'OTP telah dikirim. Silakan periksa WhatsApp Anda.');
        } else {
            return redirect()->route('register')->with('error', 'Terjadi kesalahan saat mengirim OTP.');
        }
    }

    public function showOtpForm()
    {
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|integer',
        ]);

        Log::info('OTP verification attempt', ['otp' => $request->otp]);

        $pengguna = Pengguna::where('otp', $request->otp)->first();

        if ($pengguna && $pengguna->otp_verified_at === null) {
            $otpCreatedAt = $pengguna->otp_created_at;
            if (now()->diffInMinutes($otpCreatedAt) <= 1) {
                $pengguna->otp_verified_at = now();
                $pengguna->save();

                Auth::login($pengguna);
                Log::info('OTP verified successfully', ['id_pengguna' => $pengguna->id]);

                return redirect()->route('login.form')->with('success', 'OTP benar, silakan login.');
            } else {
                Log::info('OTP expired', ['otp' => $request->otp]);
                return redirect()->route('otp.verify.form')->with('otp_expired', true);
            }
        } else {
            Log::info('OTP verification failed', ['otp' => $request->otp]);
            return redirect()->route('otp.verify.form')->with('error', 'OTP yang Anda masukkan salah.');
        }
    }

    public function requestNewOtp(Request $request)
    {
        // Find the user based on the session or another method to identify them
        $user = Auth::user();
        
        if ($user) {
            $otp = rand(100000, 999999);
            $user->otp = $otp;
            $user->otp_created_at = now();
            $user->otp_verified_at = null; // Reset the verification status
            $user->save();

            // Send OTP via the service
            $response = Http::withHeaders([
                'Authorization' => 'A5!VQAYa3UigYG9kpPpw',
            ])->post('https://api.fonnte.com/send', [
                'target' => $user->no_hp,
                'message' => "Your OTP: " . $otp,
            ]);

            if ($response->successful()) {
                return redirect()->route('otp.verify.form')->with('success', 'OTP baru telah dikirim. Silakan periksa WhatsApp Anda.');
            } else {
                return redirect()->route('otp.verify.form')->with('error', 'Terjadi kesalahan saat mengirim OTP.');
            }
        } else {
            return redirect()->route('otp.verify.form')->with('error', 'Pengguna tidak ditemukan.');
        }
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');
        $username = $request->input('username');

       if (Cache::has('login_attempts_' . $username)) {
    $blockedUntil = Cache::get('login_attempts_' . $username);

    // Jika blockedUntil adalah string, konversi menjadi Carbon instance
    if (is_string($blockedUntil)) {
        $blockedUntil = \Carbon\Carbon::parse($blockedUntil);
    }

    // Pastikan blockedUntil adalah Carbon instance
    if ($blockedUntil instanceof \Carbon\Carbon && now()->lessThan($blockedUntil)) {
        $remainingTime = $blockedUntil->diffInMinutes(now());
        $remainingSeconds = $blockedUntil->diffInSeconds(now()) % 60;
        throw ValidationException::withMessages([
            'username' => "Terlalu banyak upaya. Silakan coba lagi dalam $remainingTime menit $remainingSeconds detik.",
        ]);
    }
}

if (Auth::attempt($credentials)) {
    Cache::forget('login_attempts_' . $username);

    // Periksa apakah OTP sudah diverifikasi
    $user = Auth::user();
    if ($user->otp_verified_at === null) {
        Auth::logout();
        return redirect()->route('otp.verify.form')->with('info', 'Silakan verifikasi OTP sebelum mengakses aplikasi.');
    }

    return redirect()->route('spk.form')->with('success', 'Anda berhasil login.');
} else {
    $attempts = Cache::get('login_attempts_' . $username, 0);
    $attempts++;
    if ($attempts >= 3) {
        $blockedUntil = now()->addMinutes(1);
        Cache::put('login_attempts_' . $username, $blockedUntil, $blockedUntil);
        throw ValidationException::withMessages([
            'username' => 'Terlalu banyak upaya login. Silakan coba lagi dalam 1 menit.',
        ]);
    } else {
        Cache::put('login_attempts_' . $username, $attempts, now()->addMinutes(1));
        throw ValidationException::withMessages([
            'username' => 'Username atau password salah.',
        ]);
    }
}


    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
