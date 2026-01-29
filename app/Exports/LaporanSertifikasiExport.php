<?php

namespace App\Exports;

use App\Helpers\DateHelper;
use App\Models\Sertification;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;


class LaporanSertifikasiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    protected $sertification;

    public function __construct(Sertification $sertification)
    {
        $this->sertification = $sertification;
    }

    public function collection()
    {
        return $this->sertification->asesis()->with('student.user')->get();
    }

    public function headings(): array
    {
        $rows = [
            ['LAPORAN HASIL SERTIFIKASI'],
            ['Skema: ' . $this->sertification->skema->nama_skema],
            [
                'Tanggal Pendaftaran: ' . DateHelper::formatIdDate($this->sertification->tgl_apply_dibuka)
                . ' - ' . DateHelper::formatIdDate($this->sertification->tgl_apply_ditutup),
            ],
        ];

        $asesorCount = $this->sertification->asesors->count();
        foreach ($this->sertification->asesors as $index => $asesor) {
            $noMet = $asesor->no_reg_met ? " [{$asesor->no_reg_met}]" : "";
            $label = $asesorCount > 1 ? 'Asesor ' . ($index + 1) : 'Asesor';
            $rows[] = [$label . ' : ' . ($asesor->user->name ?? '-') . $noMet];
        }

        $rows[] = ['']; // Baris kosong
        $rows[] = [
            'No',
            'Nama Peserta',
            'NIM / ID',
            'Status Akhir (Kompetensi)',
        ];

        return $rows;
    }

    public function map($asesi): array
    {
        static $no = 0;
        $no++;

        $statusText = 'Belum Ditetapkan';
        if ($asesi->status_final === 'kompeten') {
            $statusText = 'KOMPETEN (K)';
        } elseif ($asesi->status_final === 'belum_kompeten') {
            $statusText = 'BELUM KOMPETEN (BK)';
        }

        return [
            $no,
            $asesi->student->user->name ?? '-',
            $asesi->student->nim ?? '-',
            $statusText,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 45,  // Nama Peserta
            'C' => 20,  // NIM / ID
            'D' => 30,  // Status Akhir
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $asesorCount = $this->sertification->asesors->count();
        $headerRow = 5 + $asesorCount;

        $styles = [
            1 => [
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
        ];

        for ($i = 2; $i < $headerRow - 1; $i++) {
            $styles[$i] = ['font' => ['bold' => true]];
        }

        $styles[$headerRow] = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];

        return $styles;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $asesorCount = $this->sertification->asesors->count();
                $lastInfoRow = 3 + $asesorCount;

                // Merge Judul (A1:D1)
                $event->sheet->mergeCells('A1:D1');

                // Merge baris info lainnya (A2:D2, A3:D3, dst sampai baris asesor terakhir)
                for ($i = 2; $i <= $lastInfoRow; $i++) {
                    $event->sheet->mergeCells("A{$i}:D{$i}");
                }
                
                // Tambahkan border ke tabel data
                $totalRows = $this->collection()->count() + (5 + $asesorCount);
                $tableRange = 'A' . (5 + $asesorCount) . ':D' . $totalRows;
                $event->sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
