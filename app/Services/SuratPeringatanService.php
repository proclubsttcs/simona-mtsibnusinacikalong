<?php

namespace App\Services;

use App\Models\Pelanggaran;
use App\Models\RekapPoinSiswa;
use App\Models\Siswa;
use App\Models\SuratPeringatan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * SuratPeringatanService
 * ─────────────────────────────────────────────────────────────
 * Bertanggung jawab atas:
 *   1. Cek apakah siswa memerlukan SP baru setelah poin naik
 *   2. Buat record SuratPeringatan di database
 *   3. Generate file PDF menggunakan DomPDF
 *   4. Simpan PDF ke storage/app/public/surat-peringatan/
 *
 * Threshold:
 *   50–74 poin → SP1
 *   75–99 poin → SP2
 *   100+  poin → SP3
 */
class SuratPeringatanService
{
    /**
     * Cek apakah siswa memerlukan SP baru berdasarkan rekap poin terkini.
     * Dipanggil setelah PoinService::recalculate().
     *
     * @return SuratPeringatan|null  SP yang baru dibuat, atau null jika tidak perlu
     */
    public function cekDanBuatSpOtomatis(Siswa $siswa, int $adminId): ?SuratPeringatan
    {
        $rekap = $siswa->fresh()->rekapPoin;

        if (! $rekap || $rekap->status_sp === 'aman') {
            return null;
        }

        $jenisSp     = $rekap->status_sp; // 'SP1', 'SP2', atau 'SP3'
        $totalPoin   = $rekap->total_poin;

        // Cek apakah SP jenis ini sudah pernah dibuat untuk siswa ini
        $sudahAda = SuratPeringatan::where('siswa_id', $siswa->id)
            ->where('jenis_sp', $jenisSp)
            ->exists();

        if ($sudahAda) {
            return null; // Jangan duplikat
        }

        return $this->buatSp($siswa, $jenisSp, $totalPoin, $adminId);
    }

    /**
     * Buat SP secara manual oleh admin/BK.
     * Untuk kasus di mana SP ingin diterbitkan ulang atau secara manual.
     */
    public function buatSpManual(
        Siswa  $siswa,
        string $jenisSp,
        string $keterangan,
        int    $adminId
    ): SuratPeringatan {
        $totalPoin = $siswa->fresh()->total_poin;

        return $this->buatSp($siswa, $jenisSp, $totalPoin, $adminId, $keterangan);
    }

    /**
     * Core method: buat record + generate PDF
     */
    private function buatSp(
        Siswa  $siswa,
        string $jenisSp,
        int    $totalPoin,
        int    $adminId,
        string $keterangan = ''
    ): SuratPeringatan {
        return DB::transaction(function () use ($siswa, $jenisSp, $totalPoin, $adminId, $keterangan) {

            // Default keterangan jika kosong
            if (empty($keterangan)) {
                $keterangan = "Diterbitkan karena akumulasi poin pelanggaran siswa telah mencapai {$totalPoin} poin.";
            }

            // Buat record SP
            $sp = SuratPeringatan::create([
                'siswa_id'            => $siswa->id,
                'jenis_sp'            => $jenisSp,
                'total_poin_saat_ini' => $totalPoin,
                'tanggal_terbit'      => now()->toDateString(),
                'keterangan'          => $keterangan,
                'diterbitkan_oleh'    => $adminId,
                'status'              => 'aktif',
                'file_pdf'            => null, // akan diisi setelah generate
            ]);

            // Generate PDF dan simpan
            $pdfPath = $this->generatePdf($sp);
            $sp->update(['file_pdf' => $pdfPath]);

            Log::info("SP diterbitkan", [
                'sp_id'    => $sp->id,
                'siswa'    => $siswa->nama,
                'jenis_sp' => $jenisSp,
                'poin'     => $totalPoin,
            ]);

            return $sp;
        });
    }

    /**
     * Generate PDF untuk SP yang sudah ada di database.
     * Return path relatif dari storage/public/.
     */
    public function generatePdf(SuratPeringatan $sp): string
    {
        $sp->load(['siswa.waliKelas', 'diterbitkanOleh']);

        // Data untuk view PDF
        $data = [
            'sp'          => $sp,
            'siswa'       => $sp->siswa,
            'waliKelas'   => $sp->siswa->waliKelas,
            'admin'       => $sp->diterbitkanOleh,
            'sekolah'     => $this->getProfilSekolah(),
            'nomorSurat'  => $this->generateNomorSurat($sp),
        ];

        $pdf = Pdf::loadView('pdf.surat-peringatan', $data)
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'defaultFont'    => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'dpi'                  => 150,
            ]);

        // Path penyimpanan: surat-peringatan/TAHUN/SP1_NIS_ID.pdf
        $tahun    = now()->year;
        $fileName = "{$sp->jenis_sp}_{$sp->siswa->nis}_{$sp->id}.pdf";
        $folder   = "surat-peringatan/{$tahun}";
        $path     = "{$folder}/{$fileName}";

        // Pastikan folder ada
        Storage::disk('public')->makeDirectory($folder);

        // Simpan PDF
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Regenerate PDF (misal jika data sekolah diperbarui)
     */
    public function regeneratePdf(SuratPeringatan $sp): string
    {
        // Hapus PDF lama jika ada
        if ($sp->file_pdf && Storage::disk('public')->exists($sp->file_pdf)) {
            Storage::disk('public')->delete($sp->file_pdf);
        }

        $path = $this->generatePdf($sp);
        $sp->update(['file_pdf' => $path]);

        return $path;
    }

    /**
     * Nomor surat otomatis: SP1/MTs-IS/[BULAN_ROMAWI]/[TAHUN]/[ID]
     */
    private function generateNomorSurat(SuratPeringatan $sp): string
    {
        $romawi = [
            1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',
            7=>'VII',8=>'VIII',9=>'IX',10=>'X',11=>'XI',12=>'XII'
        ];
        $bulan = $romawi[now()->month];
        $tahun = now()->year;

        return "{$sp->jenis_sp}/MTs-IS/{$bulan}/{$tahun}/{$sp->id}";
    }

    /**
     * Profil sekolah untuk header surat
     */
    public function getProfilSekolah(): array
    {
        return [
            'nama'     => 'MTs Ibnu Sina',
            'alamat'   => 'Jl. Raya Ciawi No. 1, Kec. Ciawi, Kab. Tasikmalaya',
            'kota'     => 'Tasikmalaya',
            'provinsi' => 'Jawa Barat',
            'kode_pos' => '46156',
            'telp'     => '(0265) 123456',
            'email'    => 'info@mts-ibnusina.sch.id',
            'website'  => 'www.mts-ibnusina.sch.id',
            'nss'      => '212320611001',
            'npsn'     => '20278901',
            'kepsek'   => 'H. Asep Saepudin, S.Ag., M.Pd.',
        ];
    }

    /**
     * Statistik SP untuk dashboard
     */
    public function statistikSp(): array
    {
        return [
            'total'   => SuratPeringatan::count(),
            'aktif'   => SuratPeringatan::where('status', 'aktif')->count(),
            'sp1'     => SuratPeringatan::where('jenis_sp', 'SP1')->count(),
            'sp2'     => SuratPeringatan::where('jenis_sp', 'SP2')->count(),
            'sp3'     => SuratPeringatan::where('jenis_sp', 'SP3')->count(),
            'bulan'   => SuratPeringatan::whereMonth('tanggal_terbit', now()->month)
                                         ->whereYear('tanggal_terbit',  now()->year)
                                         ->count(),
        ];
    }
}
