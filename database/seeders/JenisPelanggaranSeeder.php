<?php

namespace Database\Seeders;

use App\Models\JenisPelanggaran;
use Illuminate\Database\Seeder;

/**
 * Seeder data jenis pelanggaran sesuai tabel poin resmi MTs Ibnu Sina
 * Kode format: [RNG/SDG/BRT/SBT]-[nomor urut]
 */
class JenisPelanggaranSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // ═══════════════════════════════════════════════════════
            // KATEGORI RINGAN (5–15 poin)
            // ═══════════════════════════════════════════════════════
            [
                'kode'       => 'RNG-001',
                'nama'       => 'Tidak memakai seragam/atribut lengkap',
                'kategori'   => 'ringan',
                'poin'       => 5,
                'keterangan' => 'Per hari',
            ],
            [
                'kode'       => 'RNG-002',
                'nama'       => 'Terlambat masuk sekolah/kelas',
                'kategori'   => 'ringan',
                'poin'       => 5,
                'keterangan' => 'Per kejadian',
            ],
            [
                'kode'       => 'RNG-003',
                'nama'       => 'Tidak mengikuti kegiatan mengaji pagi',
                'kategori'   => 'ringan',
                'poin'       => 10,
                'keterangan' => 'Per kejadian',
            ],
            [
                'kode'       => 'RNG-004',
                'nama'       => 'Tidak mengikuti upacara tanpa alasan',
                'kategori'   => 'ringan',
                'poin'       => 10,
                'keterangan' => 'Per kejadian',
            ],
            [
                'kode'       => 'RNG-005',
                'nama'       => 'Membawa HP/alat elektronik tanpa izin',
                'kategori'   => 'ringan',
                'poin'       => 10,
                'keterangan' => 'Per penemuan',
            ],
            [
                'kode'       => 'RNG-006',
                'nama'       => 'Membuang sampah sembarangan',
                'kategori'   => 'ringan',
                'poin'       => 5,
                'keterangan' => 'Per kejadian',
            ],
            [
                'kode'       => 'RNG-007',
                'nama'       => 'Keluar kelas/area sekolah tanpa izin',
                'kategori'   => 'ringan',
                'poin'       => 10,
                'keterangan' => 'Per kejadian',
            ],

            // ═══════════════════════════════════════════════════════
            // KATEGORI SEDANG (15–40 poin)
            // ═══════════════════════════════════════════════════════
            [
                'kode'       => 'SDG-001',
                'nama'       => 'Alpha (tidak hadir tanpa keterangan)',
                'kategori'   => 'sedang',
                'poin'       => 15,
                'keterangan' => 'Per hari',
            ],
            [
                'kode'       => 'SDG-002',
                'nama'       => 'Bolos/meninggalkan sekolah tanpa izin',
                'kategori'   => 'sedang',
                'poin'       => 20,
                'keterangan' => 'Per kejadian',
            ],
            [
                'kode'       => 'SDG-003',
                'nama'       => 'Tidak mengikuti kegiatan diklat/pesantren kilat',
                'kategori'   => 'sedang',
                'poin'       => 20,
                'keterangan' => 'Per kegiatan',
            ],
            [
                'kode'       => 'SDG-004',
                'nama'       => 'Berpakaian tidak sesuai aturan (tidak sopan)',
                'kategori'   => 'sedang',
                'poin'       => 15,
                'keterangan' => 'Per kejadian',
            ],
            [
                'kode'       => 'SDG-005',
                'nama'       => 'Mengganggu proses pembelajaran',
                'kategori'   => 'sedang',
                'poin'       => 15,
                'keterangan' => 'Per kejadian',
            ],
            [
                'kode'       => 'SDG-006',
                'nama'       => 'Berbicara tidak sopan kepada guru',
                'kategori'   => 'sedang',
                'poin'       => 20,
                'keterangan' => 'Per kejadian',
            ],
            [
                'kode'       => 'SDG-007',
                'nama'       => 'Merusak fasilitas sekolah (ringan)',
                'kategori'   => 'sedang',
                'poin'       => 20,
                'keterangan' => 'Per kejadian + ganti rugi',
            ],

            // ═══════════════════════════════════════════════════════
            // KATEGORI BERAT (50–100 poin)
            // ═══════════════════════════════════════════════════════
            [
                'kode'       => 'BRT-001',
                'nama'       => 'Berkelahi/tawuran',
                'kategori'   => 'berat',
                'poin'       => 50,
                'keterangan' => 'Per kejadian',
            ],
            [
                'kode'       => 'BRT-002',
                'nama'       => 'Bullying (fisik, verbal, atau cyber)',
                'kategori'   => 'berat',
                'poin'       => 60,
                'keterangan' => 'Per kejadian',
            ],
            [
                'kode'       => 'BRT-003',
                'nama'       => 'Membawa rokok/merokok/vape',
                'kategori'   => 'berat',
                'poin'       => 50,
                'keterangan' => 'Per penemuan',
            ],
            [
                'kode'       => 'BRT-004',
                'nama'       => 'Melawan/tidak menghormati guru',
                'kategori'   => 'berat',
                'poin'       => 60,
                'keterangan' => 'Per kejadian',
            ],
            [
                'kode'       => 'BRT-005',
                'nama'       => 'Mencuri',
                'kategori'   => 'berat',
                'poin'       => 75,
                'keterangan' => 'Per kejadian + wajib kembalikan',
            ],
            [
                'kode'       => 'BRT-006',
                'nama'       => 'Merusak fasilitas sekolah (berat)',
                'kategori'   => 'berat',
                'poin'       => 75,
                'keterangan' => 'Per kejadian + ganti rugi',
            ],

            // ═══════════════════════════════════════════════════════
            // KATEGORI SANGAT BERAT (langsung tindakan keras)
            // ═══════════════════════════════════════════════════════
            [
                'kode'       => 'SBT-001',
                'nama'       => 'Membawa senjata tajam',
                'kategori'   => 'sangat_berat',
                'poin'       => 100,
                'keterangan' => 'Langsung proses hukum',
            ],
            [
                'kode'       => 'SBT-002',
                'nama'       => 'Pelecehan seksual',
                'kategori'   => 'sangat_berat',
                'poin'       => 150,
                'keterangan' => 'FATAL – rapat komite',
            ],
            [
                'kode'       => 'SBT-003',
                'nama'       => 'Penyalahgunaan narkoba',
                'kategori'   => 'sangat_berat',
                'poin'       => 150,
                'keterangan' => 'FATAL – rapat komite',
            ],
            [
                'kode'       => 'SBT-004',
                'nama'       => 'Tindakan kriminal berat',
                'kategori'   => 'sangat_berat',
                'poin'       => 150,
                'keterangan' => 'Rapat komite + koordinasi pihak berwajib',
            ],
        ];

        foreach ($data as $item) {
            JenisPelanggaran::updateOrCreate(
                ['kode' => $item['kode']],
                $item
            );
        }

        $this->command->info('✅ Seeder JenisPelanggaran: ' . count($data) . ' data berhasil ditanam.');
    }
}
