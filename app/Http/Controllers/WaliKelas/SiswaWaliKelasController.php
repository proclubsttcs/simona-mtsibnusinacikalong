<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller untuk wali kelas — hanya bisa melihat siswa kelasnya sendiri
 */
class SiswaWaliKelasController extends Controller
{
    /**
     * Daftar siswa di kelas yang diampu
     */
    public function index(Request $request): View
    {
        $user  = auth()->user();
        $query = Siswa::with('rekapPoin')
                      ->where('user_id', $user->id)
                      ->aktif()
                      ->orderBy('nama');

        // Pencarian nama/NIS
        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                  ->orWhere('nis', 'like', "%{$cari}%");
            });
        }

        // Filter jenis kelamin
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        $siswa = $query->paginate(20)->withQueryString();

        return view('wali-kelas.siswa.index', compact('siswa'));
    }

    /**
     * Detail profil siswa (hanya yang ada di kelas sendiri)
     */
    public function show(Siswa $siswa): View
    {
        $user = auth()->user();

        // Pastikan siswa ini memang milik wali kelas yang login
        abort_if($siswa->user_id !== $user->id, 403,
            'Anda tidak memiliki akses ke profil siswa ini.');

        $siswa->load([
            'rekapPoin',
            'pelanggaran' => function ($q) {
                $q->with(['jenisPelanggaran', 'inputOleh'])
                  ->orderBy('tanggal_pelanggaran', 'desc');
            },
            'suratPeringatan' => function ($q) {
                $q->orderBy('tanggal_terbit', 'desc');
            },
        ]);

        return view('wali-kelas.siswa.show', compact('siswa'));
    }
}
