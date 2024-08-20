<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PenggunaApiController extends Controller
{
    /**
     * Proses pendaftaran pengguna baru
     */
    public function register(Request $request)
    {
        // Validasi input pendaftaran
        $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|unique:pengguna,username',
            'no_hp' => 'nullable|string|max:20|unique:pengguna,no_hp',
            'password' => 'required|string|min:8|confirmed',
            'level' => 'required|string|in:Owner,Kepala Produksi,Customer Service',
        ], [
            'no_hp.unique' => 'Nomor HP sudah terdaftar.'
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

        // Generate OTP dan simpan ke pengguna
        $otp = rand(100000, 999999);
        $pengguna->otp = $otp;
        $pengguna->otp_created_at = now(); // Set waktu OTP dibuat
        $pengguna->save();

        // Kirim OTP ke nomor yang didaftarkan melalui API Fonnte
        try {
            $response = Http::withHeaders([
                'Authorization' => 'A5!VQAYa3UigYG9kpPpw',
            ])->post('https://api.fonnte.com/send', [
                'target' => $pengguna->no_hp,
                'message' => "Your OTP: " . $otp,
            ]);

            if ($response->successful()) {
                return response()->json([
                    'message' => 'OTP telah dikirim. Silakan periksa WhatsApp Anda.'
                ], 200);
            } else {
                Log::error('Failed to send OTP:', $response->body());
                return response()->json([
                    'error' => 'Terjadi kesalahan saat mengirim OTP.'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Exception occurred:', ['exception' => $e->getMessage()]);
            return response()->json([
                'error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifikasi OTP yang dikirimkan
     */
    public function verifyOtp(Request $request)
    {
        // Validasi input OTP
        $request->validate([
            'otp' => 'required|integer',
        ]);

        // Temukan pengguna berdasarkan OTP
        $pengguna = Pengguna::where('otp', $request->otp)->first();

        if ($pengguna && $pengguna->otp_verified_at === null) {
            $otpCreatedAt = $pengguna->otp_created_at;
            // Verifikasi OTP hanya berlaku 1 menit
            if (now()->diffInMinutes($otpCreatedAt) <= 1) {
                $pengguna->otp_verified_at = now();
                $pengguna->save();

                Auth::login($pengguna);

                return response()->json([
                    'message' => 'OTP benar, Anda berhasil login.'
                ], 200);
            } else {
                return response()->json([
                    'error' => 'OTP telah kadaluarsa.'
                ], 400);
            }
        } else {
            return response()->json([
                'error' => 'OTP yang Anda masukkan salah.'
            ], 400);
        }
    }

    /**
     * Proses login pengguna
     */
    public function login(Request $request)
    {
        // Validasi input login
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        // Temukan pengguna berdasarkan username
        $user = Pengguna::where('username', $username)->first();

        if (!$user) {
            return response()->json(['message' => 'Gagal Masuk: Username tidak ditemukan.'], 401);
        }

        // Verifikasi password
        if (!Hash::check($password, $user->password)) {
            return response()->json(['message' => 'Gagal Masuk: Password salah.'], 401);
        }

        // Kembalikan respons JSON dengan data pengguna
        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user
        ], 200);
    }

    /**
     * Minta OTP baru jika OTP sebelumnya kadaluarsa atau belum diterima
     */
    public function requestNewOtp(Request $request)
    {
        // Validasi input username
        $request->validate([
            'username' => 'required|string',
        ]);

        $user = Pengguna::where('username', $request->username)->first();

        if (!$user) {
            return response()->json([
                'error' => 'Username tidak ditemukan.'
            ], 404);
        }

        // Generate OTP baru dan simpan ke pengguna
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_created_at = now();
        $user->otp_verified_at = null;
        $user->save();

        // Kirim OTP baru ke nomor pengguna melalui API Fonnte
        try {
            $response = Http::post('https://api.fonnte.com/send', [
                'target' => $user->no_hp,
                'message' => "Your OTP: " . $otp,
                'Authorization' => 'A5!VQAYa3UigYG9kpPpw',
            ]);

            if ($response->successful()) {
                return response()->json([
                    'message' => 'OTP baru telah dikirim.'
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Gagal mengirim OTP.'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout pengguna
     */
    public function logout(Request $request)
    {
        return response()->json([
            'message' => 'Logout berhasil.'
        ], 200);
    }
}
