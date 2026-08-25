<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisPelanggaran;
use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Services\PoinService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Controller untuk admin/BK:
 * - Lihat semua pelanggaran dari semua wali kelas
 * - Konfirmasi atau tolak pelanggaran
 * - Lihat detail + riwayat konfirmasi
 */
class PelanggaranController extends Controller
{
    public function __construct(private PoinService $poinService) {}

    /**
     * Daftar semua pelanggaran dengan filter lengkap
     */
    public function index(Request $request): View
    {
        $query = Pelanggaran::with(['siswa', 'jenisPelanggaran', 'inputOleh'])
            ->orderByRaw("FIELD(status, 'menunggu', 'dikonfirmasi', 'ditolak')")
            ->orderBy('created_at', 'desc');

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter kelas
        if ($request->filled('kelas')) {
            $query->whereHas('siswa', fn($q) => $q->where('kelas', $request->kelas));
        }

        // Filter kategori pelanggaran
        if ($request->filled('kategori')) {
            $query->whereHas('jenisPelanggaran', fn($q) => $q->where('kategori', $request->kategori));
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
            $query->whereHas('siswa', fn($q) => $q->where('nama', 'like', "%{$cari}%")
                ->orWhere('nis', 'like', "%{$cari}%"));
        }

        $pelanggaran = $query->paginate(25)->withQueryString();

        // Stat counts untuk badge tab
        $stats = [
            'semua'        => Pelanggaran::count(),
            'menunggu'     => Pelanggaran::menunggu()->count(),
            'dikonfirmasi' => Pelanggaran::dikonfirmasi()->count(),
            'ditolak'      => Pelanggaran::where('status', 'ditolak')->count(),
        ];

        $kelasList = ['VII-A','VII-B','VII-C','VII-D','VIII-A','VIII-B','VIII-C','VIII-D','IX-A','IX-B','IX-C','IX-D'];

        return view('admin.pelanggaran.index', compact('pelanggaran', 'stats', 'kelasList'));
    }

    /**
     * Detail satu pelanggaran
     */
    public function show(Pelanggaran $pelanggaran): View
    {
        $pelanggaran->load([
            'siswa.rekapPoin',
            'jenisPelanggaran',
            'inputOleh',
            'konfirmasiOleh',
        ]);

        return view('admin.pelanggaran.show', compact('pelanggaran'));
    }

    /**
     * Konfirmasi satu pelanggaran → poin langsung dihitung
     */
    public function konfirmasi(Request $request, Pelanggaran $pelanggaran): RedirectResponse
    {
        if ($pelanggaran->status !== 'menunggu') {
            return back()->with('error', 'Pelanggaran ini sudah diproses sebelumnya.');
        }

        try {
            DB::transaction(function () use ($pelanggaran) {
                $pelanggaran->update([
                    'status'           => 'dikonfirmasi',
                    'dikonfirmasi_oleh' => auth()->id(),
                    'dikonfirmasi_at'  => now(),
                    'alasan_tolak'     => null,
                ]);
                // Observer akan otomatis memanggil PoinService::recalculate()
            });

            // Cek apakah ada kenaikan SP setelah konfirmasi
            $rekap   = $pelanggaran->siswa->fresh()->rekapPoin;
            $statusSp = $rekap?->status_sp ?? 'aman';
            $pesan   = "Pelanggaran {$pelanggaran->siswa->nama} berhasil dikonfirmasi (+{$pelanggaran->poin_diberikan} poin).";

            if ($statusSp !== 'aman') {
                $pesan .= " Siswa kini berada di level {$statusSp}.";
            }

            return back()->with('success', $pesan);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengkonfirmasi. Coba lagi.');
        }
    }

    /**
     * Konfirmasi banyak pelanggaran sekaligus (bulk action)
     */
    public function konfirmasiBulk(Request $request): RedirectResponse
    {
        $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:pelanggaran,id'],
        ]);

        try {
            $pelanggaran = Pelanggaran::whereIn('id', $request->ids)
                ->where('status', 'menunggu')
                ->get();

            $jumlah = 0;
            foreach ($pelanggaran as $p) {
                DB::transaction(function () use ($p) {
                    $p->update([
                        'status'           => 'dikonfirmasi',
                        'dikonfirmasi_oleh' => auth()->id(),
                        'dikonfirmasi_at'  => now(),
                    ]);
                });
                $jumlah++;
            }

            return back()->with('success', "{$jumlah} pelanggaran berhasil dikonfirmasi.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal konfirmasi massal. Coba lagi.');
        }
    }

    /**
     * Tolak pelanggaran dengan alasan
     */
    public function tolak(Request $request, Pelanggaran $pelanggaran): RedirectResponse
    {
        if ($pelanggaran->status !== 'menunggu') {
            return back()->with('error', 'Pelanggaran ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'alasan_tolak' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'alasan_tolak.required' => 'Alasan penolakan wajib diisi.',
            'alasan_tolak.min'      => 'Alasan minimal 10 karakter.',
        ]);

        try {
            $pelanggaran->update([
                'status'           => 'ditolak',
                'dikonfirmasi_oleh' => auth()->id(),
                'dikonfirmasi_at'  => now(),
                'alasan_tolak'     => $request->alasan_tolak,
            ]);

            return back()->with('success',
                "Pelanggaran {$pelanggaran->siswa->nama} berhasil ditolak.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menolak. Coba lagi.');
        }
    }

    /**
     * Batalkan konfirmasi → kembalikan ke 'menunggu'
     * Hanya bisa jika belum ada SP yang diterbitkan berdasarkan ini
     */
    public function batalKonfirmasi(Pelanggaran $pelanggaran): RedirectResponse
    {
        if ($pelanggaran->status === 'menunggu') {
            return back()->with('error', 'Status sudah menunggu.');
        }

        try {
            DB::transaction(function () use ($pelanggaran) {
                $pelanggaran->update([
                    'status'           => 'menunggu',
                    'dikonfirmasi_oleh' => null,
                    'dikonfirmasi_at'  => null,
                    'alasan_tolak'     => null,
                ]);
                // Observer akan recalculate poin
            });

            return back()->with('success', 'Konfirmasi dibatalkan. Status kembali ke menunggu.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan konfirmasi.');
        }
    }
}
