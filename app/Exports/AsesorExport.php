<?php

namespace App\Exports;

use App\Helpers\DateHelper;
use App\Models\Asesor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AsesorExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithStyles
{
    public function collection()
    {
        return Asesor::query()
            ->with('user', 'skemas')
            ->withCount('sertifications')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No.',
            'Nama Lengkap',
            'Email',
            'No. HP/WA',
            'No. Registrasi MET',
            'Masa Berlaku Sertifikat Teknis',
            'Masa Berlaku Sertifikat Asesor',
            'Jumlah Penugasan Sertifikasi',
            'Kompetensi Skema (Bidang)',
        ];
    }

    public function map($asesor): array
    {
        $skemaList = $asesor->skemas->pluck('nama_skema')->implode(', ');

        return [
            $asesor->id,
            $asesor->user->name ?? '-',
            $asesor->user->email ?? '-',
            $asesor->user->no_tlp_hp ?? '-',
            $asesor->no_reg_met ?? '-',
            $asesor->masa_berlaku_sertif_teknis ? DateHelper::formatIdDate($asesor->masa_berlaku_sertif_teknis) : '-',
            $asesor->masa_berlaku_sertif_asesor ? DateHelper::formatIdDate($asesor->masa_berlaku_sertif_asesor) : '-',
            $asesor->sertifications_count ?? '0',
            $skemaList,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
            },
        ];
    }
}
