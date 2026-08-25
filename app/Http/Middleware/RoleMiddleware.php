<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware RoleMiddleware
 * Membatasi akses route berdasarkan role user yang sedang login.
 *
 * Penggunaan di routes:
 *   ->middleware('role:admin')
 *   ->middleware('role:wali_kelas')
 *   ->middleware('role:admin,wali_kelas')  // multi-role
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Pastikan user sudah login
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // Cek apakah role user ada di daftar yang diizinkan
        if (! in_array($user->role, $roles)) {
            // Redirect ke dashboard dengan pesan error
            return redirect()
                ->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        // Cek apakah akun user aktif
        if (! $user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.']);
        }

        return $next($request);
    }
}
