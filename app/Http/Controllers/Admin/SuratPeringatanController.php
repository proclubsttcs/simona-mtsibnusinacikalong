<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\SuratPeringatan;
use App\Services\SuratPeringatanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Controller Surat Peringatan (Admin/BK)
 * ─────────────────────────────────────────────────────────────
 * - Daftar semua SP dengan filter
 * - Detail SP
 * - Buat SP manual untuk siswa tertentu
 * - Download / preview PDF
 * - Regenerate PDF
 * - Ubah status SP (aktif ↔ selesai)
 * - Hapus SP
 */
class SuratPeringatanController extends Controller
{
    public function __construct(private SuratPeringatanService $spService) {}

    /**
     * Daftar semua SP
     */
    public function index(Request $request): View
    {
        $query = SuratPeringatan::with(['siswa', 'diterbitkanOleh'])
            ->orderBy('tanggal_terbit', 'desc');

        // Filter jenis SP
        if ($request->filled('jenis_sp')) {
            $query->where('jenis_sp', $request->jenis_sp);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter kelas
        if ($request->filled('kelas')) {
            $query->whereHas('siswa', fn($q) => $q->where('kelas', $request->kelas));
        }

        // Filter bulan
        if ($request->filled('bulan')) {
            [$tahun, $bulan] = explode('-', $request->bulan);
            $query->whereYear('tanggal_terbit', $tahun)
                  ->whereMonth('tanggal_terbit', $bulan);
        }

        // Cari nama siswa
        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->whereHas('siswa', fn($q) => $q->where('nama', 'like', "%{$cari}%")
                ->orWhere('nis', 'like', "%{$cari}%"));
        }

        $suratPeringatan = $query->paginate(20)->withQueryString();

        $stats    = $this->spService->statistikSp();
        $kelasList = $this->getKelasList();

        return view('admin.surat-peringatan.index', compact('suratPeringatan', 'stats', 'kelasList'));
    }

    /**
     * Form buat SP manual
     */
    public function create(Request $request): View
    {
        // Daftar siswa aktif yang sudah punya poin (prioritas yang butuh SP)
        $siswaList = Siswa::with('rekapPoin')
            ->aktif()
            ->whereHas('rekapPoin', fn($q) => $q->where('total_poin', '>=', 50))
            ->orderByDesc('rekap_poin_siswa.total_poin')
            ->join('rekap_poin_siswa', 'siswa.id', '=', 'rekap_poin_siswa.siswa_id')
            ->select('siswa.*')
            ->get();

        // Jika ada siswa_id dari query string
        $selectedSiswaId = $request->query('siswa_id');
        $selectedSiswa   = $selectedSiswaId ? Siswa::with('rekapPoin')->find($selectedSiswaId) : null;

        return view('admin.surat-peringatan.create', compact('siswaList', 'selectedSiswaId', 'selectedSiswa'));
    }

    /**
     * Simpan SP manual
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'siswa_id'   => ['required', 'exists:siswa,id'],
            'jenis_sp'   => ['required', 'in:SP1,SP2,SP3'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ], [
            'siswa_id.required' => 'Siswa wajib dipilih.',
            'jenis_sp.required' => 'Jenis SP wajib dipilih.',
        ]);

        try {
            $siswa = Siswa::findOrFail($data['siswa_id']);

            $sp = $this->spService->buatSpManual(
                siswa:       $siswa,
                jenisSp:     $data['jenis_sp'],
                keterangan:  $data['keterangan'] ?? '',
                adminId:     auth()->id(),
            );

            return redirect()->route('admin.surat-peringatan.show', $sp)
                ->with('success', "Surat Peringatan {$sp->jenis_sp} untuk {$siswa->nama} berhasil diterbitkan.");
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Gagal menerbitkan SP: ' . $e->getMessage());
        }
    }

    /**
     * Detail SP
     */
    public function show(SuratPeringatan $suratPeringatan): View
    {
        $suratPeringatan->load([
            'siswa.rekapPoin',
            'siswa.waliKelas',
            'diterbitkanOleh',
            'siswa.pelanggaran' => fn($q) => $q->with('jenisPelanggaran')
                ->dikonfirmasi()
                ->orderBy('tanggal_pelanggaran', 'desc'),
        ]);

        $sekolah    = $this->spService->getProfilSekolah();
        $allSpSiswa = SuratPeringatan::where('siswa_id', $suratPeringatan->siswa_id)
            ->orderBy('tanggal_terbit', 'desc')
            ->get();

        return view('admin.surat-peringatan.show', compact('suratPeringatan', 'sekolah', 'allSpSiswa'));
    }

    /**
     * Download PDF SP
     */
    public function download(SuratPeringatan $suratPeringatan): mixed
    {
        // Jika belum ada PDF, generate dulu
        if (! $suratPeringatan->file_pdf || ! Storage::disk('public')->exists($suratPeringatan->file_pdf)) {
            try {
                $this->spService->regeneratePdf($suratPeringatan);
                $suratPeringatan->refresh();
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
            }
        }

        $path     = Storage::disk('public')->path($suratPeringatan->file_pdf);
        $fileName = "{$suratPeringatan->jenis_sp}_{$suratPeringatan->siswa->nis}_{$suratPeringatan->tanggal_terbit->format('Ymd')}.pdf";

        return response()->download($path, $fileName);
    }

    /**
     * Preview PDF di browser (inline)
     */
    public function preview(SuratPeringatan $suratPeringatan): mixed
    {
        if (! $suratPeringatan->file_pdf || ! Storage::disk('public')->exists($suratPeringatan->file_pdf)) {
            try {
                $this->spService->regeneratePdf($suratPeringatan);
                $suratPeringatan->refresh();
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
            }
        }

        $content = Storage::disk('public')->get($suratPeringatan->file_pdf);

        return response($content, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="preview.pdf"');
    }

    /**
     * Regenerate PDF (jika data berubah atau PDF rusak)
     */
    public function regeneratePdf(SuratPeringatan $suratPeringatan): RedirectResponse
    {
        try {
            $this->spService->regeneratePdf($suratPeringatan);

            return back()->with('success', 'PDF berhasil di-generate ulang.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status aktif ↔ selesai
     */
    public function toggleStatus(SuratPeringatan $suratPeringatan): RedirectResponse
    {
        $statusBaru = $suratPeringatan->status === 'aktif' ? 'selesai' : 'aktif';
        $suratPeringatan->update(['status' => $statusBaru]);

        $label = $statusBaru === 'aktif' ? 'diaktifkan kembali' : 'ditandai selesai';

        return back()->with('success', "SP {$suratPeringatan->jenis_sp} berhasil {$label}.");
    }

    /**
     * Hapus SP (dan file PDF-nya)
     */
    public function destroy(SuratPeringatan $suratPeringatan): RedirectResponse
    {
        try {
            // Hapus file PDF jika ada
            if ($suratPeringatan->file_pdf && Storage::disk('public')->exists($suratPeringatan->file_pdf)) {
                Storage::disk('public')->delete($suratPeringatan->file_pdf);
            }

            $nama    = $suratPeringatan->siswa->nama;
            $jenisSp = $suratPeringatan->jenis_sp;

            $suratPeringatan->delete();

            return redirect()->route('admin.surat-peringatan.index')
                ->with('success', "SP {$jenisSp} untuk {$nama} berhasil dihapus.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus SP.');
        }
    }

    private function getKelasList(): array
    {
        return [
            'VII-A','VII-B','VII-C','VII-D',
            'VIII-A','VIII-B','VIII-C','VIII-D',
            'IX-A','IX-B','IX-C','IX-D',
        ];
    }
}
