<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PelanggaranExport;
use App\Exports\RekapSiswaExport;
use App\Models\JenisPelanggaran;
use App\Models\Pelanggaran;
use App\Models\RekapPoinSiswa;
use App\Models\Siswa;
use App\Models\SuratPeringatan;
use App\Services\PoinService;
use App\Services\SuratPeringatanService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Http\Controllers\Controller;

/**
 * LaporanController
 * ─────────────────────────────────────────────────────────────
 * Menyediakan:
 *   1. Halaman laporan utama dengan grafik Chart.js
 *   2. Export Excel pelanggaran
 *   3. Export Excel rekap poin siswa
 *   4. API data grafik (JSON endpoint)
 */
class LaporanController extends Controller
{
    public function __construct(
        private PoinService            $poinService,
        private SuratPeringatanService $spService,
    ) {}

    /**
     * Halaman laporan utama
     */
    public function index(Request $request): View
    {
        $tahun   = (int) $request->input('tahun', now()->year);
        $kelas   = $request->input('kelas', '');

        // ── Data untuk grafik pelanggaran per bulan ──────────────
        $pelanggaranPerBulan = $this->poinService->distribusiPerBulan($tahun);

        // ── Pelanggaran per kategori tahun ini ───────────────────
        $query = Pelanggaran::join('jenis_pelanggaran', 'pelanggaran.jenis_pelanggaran_id', '=', 'jenis_pelanggaran.id')
            ->whereYear('tanggal_pelanggaran', $tahun)
            ->where('pelanggaran.status', 'dikonfirmasi');

        if ($kelas) {
            $query->whereHas('siswa', fn($q) => $q->where('kelas', $kelas));
        }

        $perKategori = $query
            ->selectRaw('jenis_pelanggaran.kategori, COUNT(*) as jumlah')
            ->groupBy('jenis_pelanggaran.kategori')
            ->pluck('jumlah', 'kategori')
            ->toArray();

        // ── Distribusi SP ────────────────────────────────────────
        $distribusiSp = [
            'aman' => RekapPoinSiswa::where('status_sp', 'aman')->count(),
            'SP1'  => RekapPoinSiswa::where('status_sp', 'SP1')->count(),
            'SP2'  => RekapPoinSiswa::where('status_sp', 'SP2')->count(),
            'SP3'  => RekapPoinSiswa::where('status_sp', 'SP3')->count(),
        ];

        // ── Top 10 jenis pelanggaran paling sering ───────────────
        $topJenis = Pelanggaran::join('jenis_pelanggaran', 'pelanggaran.jenis_pelanggaran_id', '=', 'jenis_pelanggaran.id')
            ->whereYear('tanggal_pelanggaran', $tahun)
            ->where('pelanggaran.status', 'dikonfirmasi')
            ->selectRaw('jenis_pelanggaran.nama, jenis_pelanggaran.kategori, COUNT(*) as jumlah')
            ->groupBy('jenis_pelanggaran.id', 'jenis_pelanggaran.nama', 'jenis_pelanggaran.kategori')
            ->orderByDesc('jumlah')
            ->limit(10)
            ->get();

        // ── Statistik ringkasan ──────────────────────────────────
        $stats = [
            'total_siswa'        => Siswa::aktif()->count(),
            'total_pelanggaran'  => Pelanggaran::whereYear('tanggal_pelanggaran', $tahun)->dikonfirmasi()->count(),
            'siswa_punya_sp'     => RekapPoinSiswa::where('status_sp', '!=', 'aman')->count(),
            'sp_terbit_tahun'    => SuratPeringatan::whereYear('tanggal_terbit', $tahun)->count(),
            'rata_poin'          => round(RekapPoinSiswa::avg('total_poin') ?? 0, 1),
            'poin_tertinggi'     => RekapPoinSiswa::max('total_poin') ?? 0,
        ];

        // ── Pelanggaran per kelas ────────────────────────────────
        $perKelas = Pelanggaran::join('siswa', 'pelanggaran.siswa_id', '=', 'siswa.id')
            ->whereYear('tanggal_pelanggaran', $tahun)
            ->where('pelanggaran.status', 'dikonfirmasi')
            ->selectRaw('siswa.kelas, COUNT(*) as jumlah, SUM(pelanggaran.poin_diberikan) as total_poin')
            ->groupBy('siswa.kelas')
            ->orderBy('siswa.kelas')
            ->get();

        // ── Tahun tersedia untuk filter ──────────────────────────
        $tahunList = range(now()->year, max(now()->year - 4, 2020));

        $kelasList = [
            'VII-A','VII-B','VII-C','VII-D',
            'VIII-A','VIII-B','VIII-C','VIII-D',
            'IX-A','IX-B','IX-C','IX-D',
        ];

        return view('admin.laporan.index', compact(
            'pelanggaranPerBulan',
            'perKategori',
            'distribusiSp',
            'topJenis',
            'stats',
            'perKelas',
            'tahun',
            'kelas',
            'tahunList',
            'kelasList',
        ));
    }

    /**
     * Halaman detail laporan per siswa
     */
    public function siswa(Request $request): View
    {
        $kelas    = $request->input('kelas', '');
        $statusSp = $request->input('status_sp', '');
        $cari     = $request->input('cari', '');

        $query = Siswa::with(['rekapPoin', 'waliKelas'])
            ->aktif()
            ->join('rekap_poin_siswa', 'siswa.id', '=', 'rekap_poin_siswa.siswa_id')
            ->orderByDesc('rekap_poin_siswa.total_poin')
            ->select('siswa.*');

        if ($kelas)    $query->where('siswa.kelas', $kelas);
        if ($statusSp) $query->where('rekap_poin_siswa.status_sp', $statusSp);
        if ($cari)     $query->where(fn($q) => $q->where('siswa.nama', 'like', "%{$cari}%")
                                                   ->orWhere('siswa.nis', 'like', "%{$cari}%"));

        $siswa = $query->paginate(25)->withQueryString();

        $kelasList = ['VII-A','VII-B','VII-C','VII-D','VIII-A','VIII-B','VIII-C','VIII-D','IX-A','IX-B','IX-C','IX-D'];

        return view('admin.laporan.siswa', compact('siswa', 'kelas', 'statusSp', 'cari', 'kelasList'));
    }

    /**
     * Export pelanggaran ke Excel
     */
    public function exportPelanggaranExcel(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['status', 'kelas', 'kategori', 'bulan', 'tahun']);
        $tahun   = $filters['tahun'] ?? now()->year;
        $bulan   = $filters['bulan'] ?? '';

        $judul   = 'Laporan Pelanggaran Siswa';
        if ($bulan) {
            [$thn, $bln] = explode('-', $bulan);
            $judul .= ' — ' . now()->setYear($thn)->setMonth($bln)->isoFormat('MMMM Y');
        } else {
            $judul .= " — Tahun {$tahun}";
        }

        $fileName = 'laporan-pelanggaran-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new PelanggaranExport($filters, $judul), $fileName);
    }

    /**
     * Export rekap poin siswa ke Excel
     */
    public function exportRekapExcel(Request $request): BinaryFileResponse
    {
        $filters  = $request->only(['kelas', 'status_sp']);
        $fileName = 'rekap-poin-siswa-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new RekapSiswaExport($filters), $fileName);
    }


    /**
     * JSON endpoint untuk data grafik (dipakai Chart.js via fetch)
     */
    public function chartData(Request $request): \Illuminate\Http\JsonResponse
    {
        $tahun = (int) $request->input('tahun', now()->year);
        $kelas = $request->input('kelas', '');

        // Pelanggaran per bulan
        $perBulan = [];
        for ($m = 1; $m <= 12; $m++) {
            $q = Pelanggaran::whereYear('tanggal_pelanggaran', $tahun)
                ->whereMonth('tanggal_pelanggaran', $m)
                ->where('status', 'dikonfirmasi');
            if ($kelas) $q->whereHas('siswa', fn($sq) => $sq->where('kelas', $kelas));
            $perBulan[] = $q->count();
        }

        // Per kategori
        $kategoriQuery = Pelanggaran::join('jenis_pelanggaran', 'pelanggaran.jenis_pelanggaran_id', '=', 'jenis_pelanggaran.id')
            ->whereYear('tanggal_pelanggaran', $tahun)
            ->where('pelanggaran.status', 'dikonfirmasi');
        if ($kelas) $kategoriQuery->whereHas('siswa', fn($q) => $q->where('kelas', $kelas));
        $perKategori = $kategoriQuery
            ->selectRaw('jenis_pelanggaran.kategori, COUNT(*) as jumlah')
            ->groupBy('jenis_pelanggaran.kategori')
            ->pluck('jumlah', 'kategori');

        return response()->json([
            'perBulan'   => $perBulan,
            'perKategori' => [
                'ringan'       => $perKategori['ringan']       ?? 0,
                'sedang'       => $perKategori['sedang']       ?? 0,
                'berat'        => $perKategori['berat']        ?? 0,
                'sangat_berat' => $perKategori['sangat_berat'] ?? 0,
            ],
            'distribusiSp' => [
                'aman' => RekapPoinSiswa::where('status_sp', 'aman')->count(),
                'SP1'  => RekapPoinSiswa::where('status_sp', 'SP1')->count(),
                'SP2'  => RekapPoinSiswa::where('status_sp', 'SP2')->count(),
                'SP3'  => RekapPoinSiswa::where('status_sp', 'SP3')->count(),
            ],
        ]);
    }
}
