<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class CheckSession
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            // Ambil waktu terakhir aktivitas pengguna
            $lastActivity = Session::get('last_activity_time');
            $now = Carbon::now();

            if ($lastActivity) {
                $lastActivityTime = Carbon::parse($lastActivity);
                $inactiveMinutes = $now->diffInMinutes($lastActivityTime);

                // Jika pengguna tidak aktif selama lebih dari 1 menit
                if ($inactiveMinutes > 1) {
                    Auth::logout();
                    Session::flush();
                    // Tandai bahwa logout disebabkan oleh ketidakaktifan
                    session()->put('auto_logout', true);
                    return redirect()->route('login.form')->with('error', 'Anda telah logout karena tidak aktif.');
                }
            }

            // Perbarui waktu aktivitas terakhir
            Session::put('last_activity_time', $now->toDateTimeString());
        }

        return $next($request);
    }
}
