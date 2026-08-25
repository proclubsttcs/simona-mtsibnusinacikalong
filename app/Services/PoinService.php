<?php

namespace App\Services;

use App\Models\Pelanggaran;
use App\Models\RekapPoinSiswa;
use App\Models\Siswa;
use App\Models\SuratPeringatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PoinService
 * ─────────────────────────────────────────────────────────────
 * Bertanggung jawab atas semua perhitungan poin pelanggaran:
 *   1. Menghitung ulang total poin dari semua pelanggaran DIKONFIRMASI
 *   2. Menentukan status SP berdasarkan threshold
 *   3. Meng-update tabel rekap_poin_siswa
 *   4. Men-trigger penerbitan SP otomatis jika threshold terlampaui
 *
 * Threshold poin (sesuai dokumen resmi MTs Ibnu Sina):
 *   0–49   poin → aman
 *   50–74  poin → SP1
 *   75–99  poin → SP2
 *   100+   poin → SP3
 */
class PoinService
{
    // ─── Threshold SP ───────────────────────────────────────────
    const THRESHOLD_SP1 = 50;
    const THRESHOLD_SP2 = 75;
    const THRESHOLD_SP3 = 100;

    /**
     * Hitung ulang total poin dan perbarui rekap untuk satu siswa.
     * Dipanggil setiap kali pelanggaran dikonfirmasi atau dibatalkan.
     *
     * @return array{poin_sebelum: int, poin_sesudah: int, status_sebelum: string, status_sesudah: string, naik_sp: string|null}
     */
    public function recalculate(Siswa $siswa): array
    {
        return DB::transaction(function () use ($siswa) {

            // Ambil rekap saat ini (buat jika belum ada)
            $rekap = RekapPoinSiswa::firstOrCreate(
                ['siswa_id' => $siswa->id],
                ['total_poin' => 0, 'status_sp' => 'aman', 'updated_at' => now()]
            );

            $poinSebelum  = $rekap->total_poin;
            $statusSebelum = $rekap->status_sp;

            // Hitung ulang dari semua pelanggaran yang sudah dikonfirmasi
            $totalPoinBaru = Pelanggaran::where('siswa_id', $siswa->id)
                ->where('status', 'dikonfirmasi')
                ->sum('poin_diberikan');

            $statusBaru = RekapPoinSiswa::hitungStatusSp($totalPoinBaru);

            // Update rekap
            $rekap->update([
                'total_poin' => $totalPoinBaru,
                'status_sp'  => $statusBaru,
                'updated_at' => now(),
            ]);

            // Cek apakah ada kenaikan SP yang perlu di-trigger
            $naikSp = $this->cekKenaikanSp($statusSebelum, $statusBaru);

            return [
                'poin_sebelum'   => $poinSebelum,
                'poin_sesudah'   => $totalPoinBaru,
                'status_sebelum' => $statusSebelum,
                'status_sesudah' => $statusBaru,
                'naik_sp'        => $naikSp,
            ];
        });
    }

    /**
     * Tentukan apakah ada kenaikan level SP.
     * Return nama SP baru jika ada kenaikan, null jika tidak.
     */
    private function cekKenaikanSp(string $sebelum, string $sesudah): ?string
    {
        $urutan = ['aman' => 0, 'SP1' => 1, 'SP2' => 2, 'SP3' => 3];

        $rankSebelum = $urutan[$sebelum] ?? 0;
        $rankSesudah = $urutan[$sesudah] ?? 0;

        // Naik level berarti SP baru perlu diterbitkan
        if ($rankSesudah > $rankSebelum && $sesudah !== 'aman') {
            return $sesudah;
        }

        return null;
    }

    /**
     * Buat Surat Peringatan secara otomatis setelah poin melebihi threshold.
     * Hanya buat SP baru jika SP dengan jenis ini belum aktif untuk siswa ini.
     */
    public function buatSpOtomatis(Siswa $siswa, string $jenisSp, int $totalPoin, int $adminId): ?SuratPeringatan
    {
        // Cek apakah SP jenis ini sudah ada dan masih aktif
        $spSudahAda = SuratPeringatan::where('siswa_id', $siswa->id)
            ->where('jenis_sp', $jenisSp)
            ->where('status', 'aktif')
            ->exists();

        if ($spSudahAda) {
            return null; // Jangan buat duplikat
        }

        try {
            $sp = SuratPeringatan::create([
                'siswa_id'           => $siswa->id,
                'jenis_sp'           => $jenisSp,
                'total_poin_saat_ini' => $totalPoin,
                'tanggal_terbit'     => now()->toDateString(),
                'keterangan'         => "Diterbitkan otomatis karena akumulasi poin mencapai {$totalPoin}.",
                'diterbitkan_oleh'   => $adminId,
                'status'             => 'aktif',
            ]);

            Log::info("SP otomatis diterbitkan", [
                'siswa_id' => $siswa->id,
                'jenis_sp' => $jenisSp,
                'poin'     => $totalPoin,
            ]);

            return $sp;
        } catch (\Exception $e) {
            Log::error("Gagal buat SP otomatis: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Rekap ringkasan poin untuk seluruh siswa di satu kelas.
     * Digunakan di dashboard wali kelas.
     */
    public function ringkasanKelas(string $kelas): array
    {
        $rekaps = RekapPoinSiswa::join('siswa', 'siswa.id', '=', 'rekap_poin_siswa.siswa_id')
            ->where('siswa.kelas', $kelas)
            ->where('siswa.is_active', true)
            ->whereNull('siswa.deleted_at')
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status_sp = "aman" THEN 1 ELSE 0 END) as aman,
                SUM(CASE WHEN status_sp = "SP1"  THEN 1 ELSE 0 END) as sp1,
                SUM(CASE WHEN status_sp = "SP2"  THEN 1 ELSE 0 END) as sp2,
                SUM(CASE WHEN status_sp = "SP3"  THEN 1 ELSE 0 END) as sp3,
                AVG(total_poin) as rata_poin,
                MAX(total_poin) as poin_tertinggi
            ')
            ->first();

        return [
            'total'         => $rekaps->total ?? 0,
            'aman'          => $rekaps->aman ?? 0,
            'sp1'           => $rekaps->sp1 ?? 0,
            'sp2'           => $rekaps->sp2 ?? 0,
            'sp3'           => $rekaps->sp3 ?? 0,
            'rata_poin'     => round($rekaps->rata_poin ?? 0, 1),
            'poin_tertinggi' => $rekaps->poin_tertinggi ?? 0,
        ];
    }

    /**
     * Rekap distribusi pelanggaran per bulan untuk grafik Chart.js.
     * Return array 12 elemen (Jan–Des) untuk tahun yang diberikan.
     */
    public function distribusiPerBulan(int $tahun, ?int $siswaId = null): array
    {
        $query = Pelanggaran::selectRaw('MONTH(tanggal_pelanggaran) as bulan, COUNT(*) as jumlah, SUM(poin_diberikan) as total_poin')
            ->whereYear('tanggal_pelanggaran', $tahun)
            ->where('status', 'dikonfirmasi')
            ->groupBy('bulan')
            ->orderBy('bulan');

        if ($siswaId) {
            $query->where('siswa_id', $siswaId);
        }

        $data = $query->pluck('jumlah', 'bulan')->toArray();

        // Pastikan 12 bulan selalu ada (isi 0 jika tidak ada data)
        $hasil = [];
        for ($i = 1; $i <= 12; $i++) {
            $hasil[] = $data[$i] ?? 0;
        }

        return $hasil;
    }
}
