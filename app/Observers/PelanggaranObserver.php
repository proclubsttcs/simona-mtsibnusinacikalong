<?php

namespace App\Observers;

use App\Models\Pelanggaran;
use App\Services\PoinService;
use App\Services\SuratPeringatanService;
use Illuminate\Support\Facades\Log;

/**
 * PelanggaranObserver
 * Setelah pelanggaran dikonfirmasi:
 *  1. Recalculate poin
 *  2. Jika status SP naik → generate SP + PDF otomatis
 */
class PelanggaranObserver
{
    public function __construct(
        private PoinService            $poinService,
        private SuratPeringatanService $spService,
    ) {}

    public function updated(Pelanggaran $pelanggaran): void
    {
        if (! $pelanggaran->wasChanged('status')) {
            return;
        }

        $statusBaru = $pelanggaran->status;

        if (in_array($statusBaru, ['dikonfirmasi', 'ditolak'])) {
            try {
                $siswa  = $pelanggaran->siswa;
                $result = $this->poinService->recalculate($siswa);

                Log::info("Rekap poin diperbarui", [
                    'siswa_id'       => $siswa->id,
                    'poin_sebelum'   => $result['poin_sebelum'],
                    'poin_sesudah'   => $result['poin_sesudah'],
                    'status_sebelum' => $result['status_sebelum'],
                    'status_sesudah' => $result['status_sesudah'],
                ]);

                // Auto SP jika poin naik level dan ada admin yang konfirmasi
                if ($statusBaru === 'dikonfirmasi' && $result['naik_sp'] !== null) {
                    $adminId = $pelanggaran->dikonfirmasi_oleh;
                    if ($adminId) {
                        $sp = $this->spService->cekDanBuatSpOtomatis($siswa, $adminId);
                        if ($sp) {
                            Log::info("SP otomatis diterbitkan", [
                                'sp_id'    => $sp->id,
                                'jenis_sp' => $sp->jenis_sp,
                                'siswa'    => $siswa->nama,
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("Observer error: " . $e->getMessage(), [
                    'pelanggaran_id' => $pelanggaran->id,
                ]);
            }
        }
    }

    public function deleted(Pelanggaran $pelanggaran): void
    {
        if ($pelanggaran->status === 'dikonfirmasi') {
            try {
                $this->poinService->recalculate($pelanggaran->siswa);
            } catch (\Exception $e) {
                Log::error("Gagal recalculate setelah hapus: " . $e->getMessage());
            }
        }
    }
}
