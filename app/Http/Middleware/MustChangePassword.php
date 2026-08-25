<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware MustChangePassword
 * Memaksa wali kelas untuk mengganti password default saat pertama login.
 * Berlaku untuk semua route kecuali halaman ganti password itu sendiri.
 */
class MustChangePassword
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Lewati jika belum login
        if (! $user) {
            return $next($request);
        }

        // Jika user harus ganti password dan belum di halaman ganti password
        if (
            $user->must_change_password &&
            ! $request->routeIs('password.change') &&
            ! $request->routeIs('password.change.update') &&
            ! $request->routeIs('logout')
        ) {
            return redirect()
                ->route('password.change')
                ->with('warning', 'Demi keamanan, silakan ganti password default Anda sebelum melanjutkan.');
        }

        return $next($request);
    }
}
