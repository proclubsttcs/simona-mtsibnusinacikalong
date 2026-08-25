<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSiswaRequest;
use App\Http\Requests\Admin\UpdateSiswaRequest;
use App\Models\RekapPoinSiswa;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Controller untuk manajemen data siswa oleh admin
 */
class SiswaController extends Controller
{
    /**
     * Daftar semua siswa dengan filter dan pencarian
     */
    public function index(Request $request): View
    {
        $query = Siswa::with(['waliKelas', 'rekapPoin'])
                      ->orderBy('kelas')
                      ->orderBy('nama');

        // Filter berdasarkan kelas
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        // Filter berdasarkan jenis kelamin
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        // Filter berdasarkan status SP
        if ($request->filled('status_sp')) {
            $query->whereHas('rekapPoin', function ($q) use ($request) {
                $q->where('status_sp', $request->status_sp);
            });
        }

        // Pencarian nama/NIS
        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                  ->orWhere('nis', 'like', "%{$cari}%");
            });
        }

        // Filter status aktif (default tampilkan yang aktif)
        if ($request->input('tampilkan') !== 'semua') {
            $query->aktif();
        }

        $siswa     = $query->paginate(20)->withQueryString();
        $kelasList = $this->getKelasList();

        // Data untuk filter dropdown
        $waliKelasList = User::waliKelas()->aktif()->orderBy('kelas')->get();

        return view('admin.siswa.index', compact('siswa', 'kelasList', 'waliKelasList'));
    }

    /**
     * Form tambah siswa baru
     */
    public function create(): View
    {
        $kelasList     = $this->getKelasList();
        $waliKelasList = User::waliKelas()->aktif()->orderBy('kelas')->get();

        return view('admin.siswa.create', compact('kelasList', 'waliKelasList'));
    }

    /**
     * Simpan data siswa baru
     */
    public function store(StoreSiswaRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Upload foto jika ada
            if ($request->hasFile('foto')) {
                $data['foto'] = $request->file('foto')->store('foto-siswa', 'public');
            }

            $siswa = Siswa::create($data);

            // Buat rekap poin awal untuk siswa ini
            RekapPoinSiswa::create([
                'siswa_id'   => $siswa->id,
                'total_poin' => 0,
                'status_sp'  => 'aman',
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.siswa.show', $siswa)
                ->with('success', "Siswa {$siswa->nama} (NIS: {$siswa->nis}) berhasil ditambahkan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan siswa. Silakan coba lagi.');
        }
    }

    /**
     * Detail profil siswa lengkap
     */
    public function show(Siswa $siswa): View
    {
        $siswa->load([
            'waliKelas',
            'rekapPoin',
            'pelanggaran' => function ($q) {
                $q->with(['jenisPelanggaran', 'inputOleh'])
                  ->orderBy('tanggal_pelanggaran', 'desc')
                  ->limit(10);
            },
            'suratPeringatan' => function ($q) {
                $q->with('diterbitkanOleh')->orderBy('tanggal_terbit', 'desc');
            },
        ]);

        return view('admin.siswa.show', compact('siswa'));
    }

    /**
     * Form edit data siswa
     */
    public function edit(Siswa $siswa): View
    {
        $kelasList     = $this->getKelasList();
        $waliKelasList = User::waliKelas()->aktif()->orderBy('kelas')->get();

        return view('admin.siswa.edit', compact('siswa', 'kelasList', 'waliKelasList'));
    }

    /**
     * Update data siswa
     */
    public function update(UpdateSiswaRequest $request, Siswa $siswa): RedirectResponse
    {
        try {
            $data = $request->validated();

            // Upload foto baru jika ada
            if ($request->hasFile('foto')) {
                if ($siswa->foto) {
                    Storage::disk('public')->delete($siswa->foto);
                }
                $data['foto'] = $request->file('foto')->store('foto-siswa', 'public');
            }

            $siswa->update($data);

            return redirect()->route('admin.siswa.show', $siswa)
                ->with('success', "Data siswa {$siswa->nama} berhasil diperbarui.");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data siswa. Silakan coba lagi.');
        }
    }

    /**
     * Nonaktifkan siswa (soft delete)
     */
    public function destroy(Siswa $siswa): RedirectResponse
    {
        try {
            $siswa->update(['is_active' => false]);
            $siswa->delete();

            return redirect()->route('admin.siswa.index')
                ->with('success', "Siswa {$siswa->nama} berhasil dinonaktifkan.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data siswa.');
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
