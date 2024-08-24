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
use Carbon\Carbon;

class PenggunaController extends Controller
{
    // Menampilkan formulir pendaftaran
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Proses pendaftaran pengguna
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|unique:pengguna,username',
            'no_hp' => 'nullable|string|max:20|unique:pengguna,no_hp',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
            'level' => 'required|string|in:Owner,Kepala Produksi,Customer Service',
        ]);

        // Membuat pengguna baru
        $pengguna = Pengguna::create([
            'id_pengguna' => Pengguna::generateId(),
            'nama' => $request->nama,
            'username' => $request->username,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password),
            'level' => $request->level,
        ]);

        // Generate OTP dan kirim
        $otp = rand(100000, 999999);
        $pengguna->otp = $otp;
        $pengguna->otp_created_at = now(); // Set waktu OTP dibuat
        $pengguna->save();

        // Kirim OTP ke nomor yang didaftarkan
        $response = Http::withHeaders([
            'Authorization' => 'A5!VQAYa3UigYG9kpPpw',
        ])->post('https://api.fonnte.com/send', [
            'target' => $pengguna->no_hp, // Kirim OTP ke nomor HP yang didaftarkan
            'message' => "Your OTP: " . $otp,
        ]);

        if ($response->successful()) {
            // Arahkan ke halaman verifikasi OTP
            return redirect()->route('otp.verify.form')->with('info', 'OTP telah dikirim. Silakan periksa WhatsApp Anda.');
        } else {
            return redirect()->route('register')->with('error', 'Terjadi kesalahan saat mengirim OTP.');
        }
    }

    // Menampilkan formulir verifikasi OTP
    public function showOtpForm()
    {
        return view('auth.verify-otp');
    }

    // Verifikasi OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|integer',
        ]);

        Log::info('OTP verification attempt', ['otp' => $request->otp]);

        $pengguna = Pengguna::where('otp', $request->otp)->first();

        if ($pengguna && $pengguna->otp_verified_at === null) {
            $otpCreatedAt = $pengguna->otp_created_at;
            // Verifikasi OTP hanya berlaku 1 menit
            if (now()->diffInMinutes($otpCreatedAt) <= 1) {
                $pengguna->otp_verified_at = now();
                $pengguna->save();

                Auth::login($pengguna);
                Log::info('OTP verified successfully', ['id_pengguna' => $pengguna->id]);

                // Hapus flag OTP setelah verifikasi
                session(['requires_otp' => false]);

                // Arahkan ke halaman SPK setelah verifikasi OTP
                return redirect()->route('spk.form')->with('success', 'OTP benar, Anda berhasil login.');
            } else {
                Log::info('OTP expired', ['otp' => $request->otp]);
                return redirect()->route('otp.verify.form')->with('otp_expired', true);
            }
        } else {
            Log::info('OTP verification failed', ['otp' => $request->otp]);
            return redirect()->route('otp.verify.form')->with('error', 'OTP yang Anda masukkan salah.');
        }
    }

    // Minta OTP Baru
    public function requestNewOtp(Request $request)
{
    // Mendapatkan inputan pengguna (username atau nomor HP)
    $username = $request->input('username');
    $no_hp = $request->input('no_hp');

    // Cari pengguna berdasarkan username atau nomor HP
    $user = Pengguna::when($username, function($query, $username) {
        return $query->where('username', $username);
    })->when($no_hp, function($query, $no_hp) {
        return $query->where('no_hp', $no_hp);
    })->first();

    if (!$user) {
        return redirect()->route('otp.verify.form')->with('error', 'Pengguna tidak ditemukan.');
    }

    // Debug: Tampilkan nomor telepon pengguna
    Log::info('Pengguna Ditemukan untuk OTP', [
        'id' => $user->id,
        'no_hp' => $user->no_hp
    ]);

    // Generate OTP baru
    $otp = rand(100000, 999999);
    $user->otp = $otp;
    $user->otp_created_at = now();
    $user->otp_verified_at = null; 
    $user->save();

    // Kirim OTP via layanan
    $response = Http::withHeaders([
        'Authorization' => 'A5!VQAYa3UigYG9kpPpw',
    ])->post('https://api.fonnte.com/send', [
        'target' => $user->no_hp,
        'message' => "Your OTP: " . $otp,
    ]);

    if ($response->successful()) {
        return redirect()->route('otp.verify.form')->with('info', 'OTP baru telah dikirim. Silakan periksa WhatsApp Anda.');
    } else {
        return redirect()->route('otp.verify.form')->with('error', 'Terjadi kesalahan saat mengirim OTP.');
    }
}


    // Menampilkan formulir login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Menangani login
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        $username = $request->username;

        // Mengecek apakah username terdaftar
        if (!Pengguna::where('username', $username)->exists()) {
            throw ValidationException::withMessages([
                'username' => 'Username belum terdaftar. Silakan daftar terlebih dahulu.',
            ]);
        }

        // Memeriksa apakah ada upaya login yang diblokir
        if (Cache::has('login_attempts_' . $username)) {
            $blockedUntil = Cache::get('login_attempts_' . $username);
            if (is_string($blockedUntil)) {
                $blockedUntil = \Carbon\Carbon::parse($blockedUntil);
            }
            if ($blockedUntil instanceof \Carbon\Carbon && now()->lessThan($blockedUntil)) {
                $remainingTime = $blockedUntil->diffInMinutes(now());
                $remainingSeconds = $blockedUntil->diffInSeconds(now()) % 60;
                throw ValidationException::withMessages([
                    'username' => "Terlalu banyak upaya. Silakan coba lagi dalam $remainingTime menit $remainingSeconds detik.",
                ]);
            }
        }

        // Menangani upaya login
        if (Auth::attempt($credentials)) {
            // Menghapus data pemblokiran setelah login berhasil
            Cache::forget('login_attempts_' . $username);

            $user = Auth::user();

            // Log status OTP
            Log::info('User login attempt', ['user_id' => $user->id, 'otp_verified_at' => $user->otp_verified_at]);

            // Cek jika pengguna harus diverifikasi dengan OTP
            if (session('auto_logout', false)) {
                // Logout pengguna dan kirim OTP
                Auth::logout();
                session()->forget('auto_logout'); // Hapus flag auto-logout

                // Generate OTP dan reset status
                $otp = rand(100000, 999999);
                $user->otp = $otp;
                $user->otp_created_at = now();
                $user->otp_verified_at = null; // Reset status verifikasi
                $user->save();

                // Kirim OTP via layanan
                $response = Http::withHeaders([
                    'Authorization' => 'A5!VQAYa3UigYG9kpPpw',
                ])->post('https://api.fonnte.com/send', [
                    'target' => $user->no_hp, // Pastikan ini nomor telepon yang benar
                    'message' => "Your OTP: " . $otp,
                ]);

                if ($response->successful()) {
                    return redirect()->route('otp.verify.form')->with('info', 'OTP telah dikirim. Silakan periksa WhatsApp Anda.');
                } else {
                    return redirect()->route('login.form')->with('error', 'Terjadi kesalahan saat mengirim OTP.');
                }
            } else {
                // Arahkan ke halaman SPK jika OTP sudah diverifikasi
                if ($user->otp_verified_at) {
                    return redirect()->route('spk.form')->with('success', 'Anda berhasil login.');
                } else {
                    return redirect()->route('otp.verify.form')->with('info', 'Silakan verifikasi OTP.');
                }
            }
        } else {
            // Penanganan kesalahan login
            $attempts = Cache::get('login_attempts_' . $username, 0);
            $attempts++;
            if ($attempts >= 3) {
                $blockedUntil = now()->addMinutes(10)->toDateTimeString();
                Cache::put('login_attempts_' . $username, $blockedUntil, now()->addMinutes(10));
                throw ValidationException::withMessages([
                    'username' => 'Terlalu banyak upaya login. Silakan coba lagi dalam 10 menit.',
                ]);
            } else {
                Cache::put('login_attempts_' . $username, $attempts);
                throw ValidationException::withMessages([
                    'username' => 'Username atau password salah.',
                ]);
            }
        }
    }

    // Logout pengguna
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form');
    }
}


