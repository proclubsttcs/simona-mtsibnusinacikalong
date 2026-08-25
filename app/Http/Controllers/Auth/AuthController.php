<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLogin(): View|RedirectResponse
    {
        // Jika sudah login, redirect ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        // Coba autentikasi dengan remember me
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Cek apakah akun aktif
            if (! $user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.',
                ])->onlyInput('email');
            }

            // Jika wali kelas dan harus ganti password
            if ($user->must_change_password) {
                return redirect()->route('password.change')
                    ->with('warning', 'Silakan ganti password default Anda terlebih dahulu.');
            }

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        return back()
            ->withErrors(['email' => 'Email atau password yang Anda masukkan salah.'])
            ->onlyInput('email');
    }

    /**
     * Logout
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda berhasil keluar dari sistem.');
    }

    /**
     * Tampilkan form ganti password (untuk first-time login)
     */
    public function showChangePassword(): View
    {
        return view('auth.change-password');
    }

    /**
     * Proses ganti password
     */
    public function updatePassword(ChangePasswordRequest $request): RedirectResponse
    {
        try {
            $user = Auth::user();

            // Validasi password lama (hanya untuk user yang sudah pernah login sebelumnya)
            if (! $user->must_change_password) {
                if (! Hash::check($request->password_lama, $user->password)) {
                    return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
                }
            }

            // Update password dan reset flag must_change_password
            $user->update([
                'password'             => Hash::make($request->password_baru),
                'must_change_password' => false,
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Password berhasil diperbarui. Selamat datang di SiMON!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui password. Silakan coba lagi.');
        }
    }
}
