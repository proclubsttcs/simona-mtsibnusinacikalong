<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Controller untuk manajemen akun wali kelas oleh admin
 */
class UserController extends Controller
{
    /**
     * Daftar semua user (wali kelas dan admin)
     */
    public function index(Request $request): View
    {
        $query = User::orderBy('role')
                     ->orderBy('kelas');

        // Filter berdasarkan role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Pencarian nama/email
        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('name', 'like', "%{$cari}%")
                  ->orWhere('email', 'like', "%{$cari}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Form tambah user baru
     */
    public function create(): View
    {
        // Daftar kelas yang tersedia untuk wali kelas
        $kelasList = $this->getKelasList();

        return view('admin.users.create', compact('kelasList'));
    }

    /**
     * Simpan user baru
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();

            // Hash password
            $data['password'] = Hash::make($data['password']);

            // Wali kelas wajib ganti password saat pertama login
            $data['must_change_password'] = ($data['role'] === 'wali_kelas');

            // Upload foto jika ada
            if ($request->hasFile('foto')) {
                $data['foto'] = $request->file('foto')->store('foto-user', 'public');
            }

            User::create($data);

            return redirect()->route('admin.users.index')
                ->with('success', 'Akun ' . $data['name'] . ' berhasil dibuat.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal membuat akun. Silakan coba lagi.');
        }
    }

    /**
     * Detail user
     */
    public function show(User $user): View
    {
        $user->load('siswa.rekapPoin');

        // Statistik jika wali kelas
        $stats = [];
        if ($user->isWaliKelas()) {
            $stats = [
                'total_siswa'    => $user->siswa()->aktif()->count(),
                'siswa_sp'       => $user->siswa()->whereHas('rekapPoin', fn($q) => $q->where('status_sp', '!=', 'aman'))->count(),
                'pelanggaran_bln' => $user->pelanggaranDiinput()->bulanIni()->count(),
            ];
        }

        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Form edit user
     */
    public function edit(User $user): View
    {
        $kelasList = $this->getKelasList();

        return view('admin.users.edit', compact('user', 'kelasList'));
    }

    /**
     * Update data user
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {
            $data = $request->validated();

            // Hanya update password jika diisi
            if (! empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
                // Jika password direset, wajib ganti password lagi
                if ($user->isWaliKelas()) {
                    $data['must_change_password'] = true;
                }
            } else {
                unset($data['password']);
            }

            // Upload foto baru jika ada
            if ($request->hasFile('foto')) {
                // Hapus foto lama
                if ($user->foto) {
                    Storage::disk('public')->delete($user->foto);
                }
                $data['foto'] = $request->file('foto')->store('foto-user', 'public');
            }

            $user->update($data);

            return redirect()->route('admin.users.index')
                ->with('success', 'Akun ' . $user->name . ' berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui akun. Silakan coba lagi.');
        }
    }

    /**
     * Hapus user dari database secara permanen
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            // Admin tidak bisa menghapus dirinya sendiri
            if ($user->id === auth()->id()) {
                return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            }

            // Cek jika wali kelas masih punya siswa aktif
            if ($user->isWaliKelas() && $user->siswa()->aktif()->exists()) {
                return back()->with('error',
                    'Wali kelas ' . $user->name . ' masih memiliki siswa aktif. Pindahkan siswa terlebih dahulu.');
            }

            $user->forceDelete();

            return back()->with('success', 'Akun ' . $user->name . ' berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus akun. Silakan coba lagi.');
        }
    }

    /**
     * Daftar kelas yang tersedia
     */
    private function getKelasList(): array
    {
        return [
            'VII-A', 'VII-B', 'VII-C', 'VII-D',
            'VIII-A', 'VIII-B', 'VIII-C', 'VIII-D',
            'IX-A', 'IX-B', 'IX-C', 'IX-D',
        ];
    }
}
