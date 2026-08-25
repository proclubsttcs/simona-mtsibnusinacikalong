<?php

namespace App\Exports;

use App\Models\Pelanggaran;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * Export data pelanggaran ke Excel (.xlsx)
 * Menggunakan view Blade sebagai template tabel.
 */
class PelanggaranExport implements FromView, WithTitle, ShouldAutoSize, WithEvents
{
    public function __construct(
        private array  $filters = [],
        private string $judulLaporan = 'Laporan Pelanggaran Siswa'
    ) {}

    /**
     * Gunakan view Blade sebagai sumber data tabel Excel
     */
    public function view(): View
    {
        // Build query sesuai filter
        $query = Pelanggaran::with(['siswa', 'jenisPelanggaran', 'inputOleh', 'konfirmasiOleh'])
            ->orderBy('tanggal_pelanggaran', 'desc');

        if (! empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        if (! empty($this->filters['kelas'])) {
            $query->whereHas('siswa', fn($q) => $q->where('kelas', $this->filters['kelas']));
        }
        if (! empty($this->filters['kategori'])) {
            $query->whereHas('jenisPelanggaran', fn($q) => $q->where('kategori', $this->filters['kategori']));
        }
        if (! empty($this->filters['bulan'])) {
            [$tahun, $bulan] = explode('-', $this->filters['bulan']);
            $query->whereYear('tanggal_pelanggaran', $tahun)
                  ->whereMonth('tanggal_pelanggaran', $bulan);
        }
        if (! empty($this->filters['tahun'])) {
            $query->whereYear('tanggal_pelanggaran', $this->filters['tahun']);
        }

        $data = $query->get();

        return view('exports.pelanggaran', [
            'pelanggaran'   => $data,
            'judulLaporan'  => $this->judulLaporan,
            'filters'       => $this->filters,
            'generatedAt'   => now()->isoFormat('D MMMM Y, HH:mm'),
        ]);
    }

    public function title(): string
    {
        return 'Data Pelanggaran';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Style header row (baris 7 biasanya setelah judul & meta)
                $headerRow = 7;
                $lastCol   = 'J';

                $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")
                    ->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '1E3A5F']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                // Border semua sel data
                $lastRow = $sheet->getHighestRow();
                if ($lastRow > $headerRow) {
                    $sheet->getStyle("A{$headerRow}:{$lastCol}{$lastRow}")
                        ->getBorders()->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);
                }

                // Freeze header row
                $sheet->freezePane("A" . ($headerRow + 1));
            },
        ];
    }
}
