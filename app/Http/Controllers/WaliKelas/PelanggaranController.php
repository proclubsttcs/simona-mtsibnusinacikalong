<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Http\Requests\WaliKelas\StorePelanggaranRequest;
use App\Http\Requests\WaliKelas\UpdatePelanggaranRequest;
use App\Models\JenisPelanggaran;
use App\Models\Pelanggaran;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller untuk wali kelas:
 * - Input pelanggaran baru
 * - Lihat pelanggaran yang sudah diinput
 * - Edit / hapus pelanggaran yang masih 'menunggu'
 */
class PelanggaranController extends Controller
{
    /**
     * Daftar pelanggaran yang diinput oleh wali kelas ini
     */
    public function index(Request $request): View
    {
        $user  = auth()->user();
        $query = Pelanggaran::with(['siswa', 'jenisPelanggaran'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter bulan
        if ($request->filled('bulan')) {
            [$tahun, $bulan] = explode('-', $request->bulan);
            $query->whereYear('tanggal_pelanggaran', $tahun)
                  ->whereMonth('tanggal_pelanggaran', $bulan);
        }

        // Cari nama siswa
        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->whereHas('siswa', fn($q) => $q->where('nama', 'like', "%{$cari}%"));
        }

        $pelanggaran = $query->paginate(20)->withQueryString();

        // Statistik singkat untuk header
        $stats = [
            'menunggu'     => Pelanggaran::where('user_id', $user->id)->menunggu()->count(),
            'dikonfirmasi' => Pelanggaran::where('user_id', $user->id)->dikonfirmasi()->count(),
            'bulan_ini'    => Pelanggaran::where('user_id', $user->id)->bulanIni()->count(),
        ];

        return view('wali-kelas.pelanggaran.index', compact('pelanggaran', 'stats'));
    }

    /**
     * Form input pelanggaran baru
     */
    public function create(Request $request): View
    {
        $user = auth()->user();

        // Siswa di kelas wali kelas ini (aktif saja)
        $siswaList = Siswa::where('user_id', $user->id)
            ->aktif()
            ->orderBy('nama')
            ->get();

        // Semua jenis pelanggaran aktif, dikelompokkan per kategori
        $jenisList = JenisPelanggaran::aktif()
            ->orderBy('kategori')
            ->orderBy('nama')
            ->get()
            ->groupBy('kategori');

        // Jika ada siswa_id dari query string (dari halaman profil siswa)
        $selectedSiswaId = $request->query('siswa_id');

        return view('wali-kelas.pelanggaran.create', compact(
            'siswaList', 'jenisList', 'selectedSiswaId'
        ));
    }

    /**
     * Simpan pelanggaran baru
     */
    public function store(StorePelanggaranRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();

            // Ambil poin default dari jenis pelanggaran
            $jenis = JenisPelanggaran::findOrFail($data['jenis_pelanggaran_id']);

            // Gunakan poin dari form (bisa diubah wali kelas dengan alasan), atau poin default
            $data['poin_diberikan'] = $data['poin_diberikan'] ?? $jenis->poin;
            $data['user_id']        = auth()->id();
            $data['status']         = 'menunggu'; // selalu mulai dari menunggu

            Pelanggaran::create($data);

            return redirect()->route('wali-kelas.pelanggaran.index')
                ->with('success', 'Pelanggaran berhasil dicatat. Menunggu konfirmasi dari BK.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Gagal menyimpan pelanggaran. Silakan coba lagi.');
        }
    }

    /**
     * Detail satu pelanggaran
     */
    public function show(Pelanggaran $pelanggaran): View
    {
        // Pastikan pelanggaran ini milik wali kelas yang login
        abort_if($pelanggaran->user_id !== auth()->id(), 403);

        $pelanggaran->load(['siswa.rekapPoin', 'jenisPelanggaran', 'konfirmasiOleh']);

        return view('wali-kelas.pelanggaran.show', compact('pelanggaran'));
    }

    /**
     * Form edit pelanggaran (hanya yang masih 'menunggu')
     */
    public function edit(Pelanggaran $pelanggaran): View|RedirectResponse
    {
        abort_if($pelanggaran->user_id !== auth()->id(), 403);

        // Hanya bisa edit jika masih menunggu
        if ($pelanggaran->status !== 'menunggu') {
            return back()->with('error',
                'Pelanggaran yang sudah dikonfirmasi atau ditolak tidak dapat diedit.');
        }

        $user      = auth()->user();
        $siswaList = Siswa::where('user_id', $user->id)->aktif()->orderBy('nama')->get();
        $jenisList = JenisPelanggaran::aktif()->orderBy('kategori')->orderBy('nama')->get()->groupBy('kategori');

        return view('wali-kelas.pelanggaran.edit', compact('pelanggaran', 'siswaList', 'jenisList'));
    }

    /**
     * Update pelanggaran (hanya yang masih 'menunggu')
     */
    public function update(UpdatePelanggaranRequest $request, Pelanggaran $pelanggaran): RedirectResponse
    {
        abort_if($pelanggaran->user_id !== auth()->id(), 403);

        if ($pelanggaran->status !== 'menunggu') {
            return back()->with('error', 'Pelanggaran ini tidak dapat diedit.');
        }

        try {
            $pelanggaran->update($request->validated());

            return redirect()->route('wali-kelas.pelanggaran.index')
                ->with('success', 'Pelanggaran berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui. Coba lagi.');
        }
    }

    /**
     * Hapus/tarik pelanggaran (hanya yang masih 'menunggu')
     */
    public function destroy(Pelanggaran $pelanggaran): RedirectResponse
    {
        abort_if($pelanggaran->user_id !== auth()->id(), 403);

        if ($pelanggaran->status !== 'menunggu') {
            return back()->with('error',
                'Pelanggaran yang sudah diproses tidak dapat dihapus.');
        }

        try {
            $pelanggaran->delete();

            return back()->with('success', 'Catatan pelanggaran berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus. Coba lagi.');
        }
    }
}
