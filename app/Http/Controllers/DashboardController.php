<?php

namespace App\Http\Controllers;

use App\Models\JenisPelanggaran;
use App\Models\Pelanggaran;
use App\Models\RekapPoinSiswa;
use App\Models\Siswa;
use App\Models\SuratPeringatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Routing dashboard berdasarkan role
     */
    public function index(): View
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $this->dashboardAdmin();
        }

        return $this->dashboardWaliKelas($user);
    }

    /**
     * Dashboard Admin/BK
     * Menampilkan stat card 6 item + grafik ringkasan
     */
    private function dashboardAdmin(): View
    {
        // ─── Stat Cards ────────────────────────────────────────────
        $stats = [
            // 1. Total siswa aktif
            'total_siswa' => Siswa::aktif()->count(),

            // 2. Pelanggaran bulan ini (semua status)
            'pelanggaran_bulan_ini' => Pelanggaran::bulanIni()->count(),

            // 3. Siswa dengan SP aktif
            'siswa_sp_aktif' => RekapPoinSiswa::where('status_sp', '!=', 'aman')->count(),

            // 4. Pelanggaran menunggu konfirmasi
            'menunggu_konfirmasi' => Pelanggaran::menunggu()->count(),

            // 5. SP terbit bulan ini
            'sp_bulan_ini' => SuratPeringatan::whereMonth('tanggal_terbit', now()->month)
                                              ->whereYear('tanggal_terbit', now()->year)
                                              ->count(),

            // 6. Siswa aman (poin < 50)
            'siswa_aman' => RekapPoinSiswa::where('status_sp', 'aman')->count(),
        ];

        // ─── Tren bulan sebelumnya untuk perbandingan ─────────────
        $bulanLalu = [
            'pelanggaran' => Pelanggaran::whereMonth('tanggal_pelanggaran', now()->subMonth()->month)
                                         ->whereYear('tanggal_pelanggaran', now()->subMonth()->year)
                                         ->count(),
        ];

        // ─── 10 Siswa dengan poin tertinggi ───────────────────────
        $siswaRisiko = Siswa::with(['rekapPoin', 'waliKelas'])
            ->aktif()
            ->whereHas('rekapPoin', fn($q) => $q->where('total_poin', '>', 0))
            ->join('rekap_poin_siswa', 'siswa.id', '=', 'rekap_poin_siswa.siswa_id')
            ->orderBy('rekap_poin_siswa.total_poin', 'desc')
            ->select('siswa.*')
            ->limit(10)
            ->get();

        // ─── Pelanggaran terbaru menunggu konfirmasi ──────────────
        $pelanggaranTerbaru = Pelanggaran::with(['siswa', 'jenisPelanggaran', 'inputOleh'])
            ->menunggu()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ─── Distribusi pelanggaran per kategori bulan ini ────────
        $distribusiKategori = Pelanggaran::join('jenis_pelanggaran', 'pelanggaran.jenis_pelanggaran_id', '=', 'jenis_pelanggaran.id')
            ->bulanIni()
            ->where('pelanggaran.status', 'dikonfirmasi')
            ->select('jenis_pelanggaran.kategori', \DB::raw('COUNT(*) as jumlah'))
            ->groupBy('jenis_pelanggaran.kategori')
            ->pluck('jumlah', 'kategori')
            ->toArray();

        return view('admin.dashboard', compact(
            'stats',
            'bulanLalu',
            'siswaRisiko',
            'pelanggaranTerbaru',
            'distribusiKategori'
        ));
    }

    /**
     * Dashboard Wali Kelas
     * Hanya menampilkan data kelas yang diampu
     */
    private function dashboardWaliKelas(User $user): View
    {
        // Ambil semua siswa di kelas wali kelas ini
        $siswaKelas = Siswa::with('rekapPoin')
            ->where('user_id', $user->id)
            ->aktif()
            ->get();

        $stats = [
            'total_siswa'       => $siswaKelas->count(),
            'siswa_sp'          => $siswaKelas->filter(fn($s) => ($s->rekapPoin?->status_sp ?? 'aman') !== 'aman')->count(),
            'pelanggaran_bulan' => Pelanggaran::where('user_id', $user->id)->bulanIni()->count(),
            'menunggu'          => Pelanggaran::where('user_id', $user->id)->menunggu()->count(),
        ];

        // Siswa dengan poin tertinggi di kelas ini
        $siswaRisikoKelas = $siswaKelas->sortByDesc(fn($s) => $s->rekapPoin?->total_poin ?? 0)->take(5);

        // Pelanggaran yang saya input, terbaru
        $pelanggaranSaya = Pelanggaran::with(['siswa', 'jenisPelanggaran'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('wali-kelas.dashboard', compact(
            'stats',
            'siswaKelas',
            'siswaRisikoKelas',
            'pelanggaranSaya'
        ));
    }
}
