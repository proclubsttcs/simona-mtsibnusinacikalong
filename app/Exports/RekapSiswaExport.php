<?php

namespace App\Exports;

use App\Models\Siswa;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * Export rekap poin siswa ke Excel
 */
class RekapSiswaExport implements FromView, WithTitle, ShouldAutoSize, WithEvents
{
    public function __construct(
        private array $filters = []
    ) {}

    public function view(): View
    {
        $query = Siswa::with(['rekapPoin', 'waliKelas', 'suratPeringatan'])
            ->aktif()
            ->join('rekap_poin_siswa', 'siswa.id', '=', 'rekap_poin_siswa.siswa_id')
            ->orderBy('rekap_poin_siswa.total_poin', 'desc')
            ->orderBy('siswa.kelas')
            ->orderBy('siswa.nama')
            ->select('siswa.*');

        if (! empty($this->filters['kelas'])) {
            $query->where('siswa.kelas', $this->filters['kelas']);
        }
        if (! empty($this->filters['status_sp'])) {
            $query->where('rekap_poin_siswa.status_sp', $this->filters['status_sp']);
        }

        $siswa = $query->get();

        return view('exports.rekap-siswa', [
            'siswa'       => $siswa,
            'filters'     => $this->filters,
            'generatedAt' => now()->isoFormat('D MMMM Y, HH:mm'),
        ]);
    }

    public function title(): string
    {
        return 'Rekap Poin Siswa';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet     = $event->sheet->getDelegate();
                $headerRow = 7;
                $lastCol   = 'J';

                $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")
                    ->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '1E3A5F']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                $lastRow = $sheet->getHighestRow();
                if ($lastRow > $headerRow) {
                    $sheet->getStyle("A{$headerRow}:{$lastCol}{$lastRow}")
                        ->getBorders()->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);
                }

                $sheet->freezePane("A" . ($headerRow + 1));
            },
        ];
    }
}
