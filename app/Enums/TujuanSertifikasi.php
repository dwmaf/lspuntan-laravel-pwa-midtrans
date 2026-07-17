<?php

namespace App\Enums;

enum TujuanSertifikasi: string
{
    case SERTIFIKASI = 'Sertifikasi';
    case PKT = 'Pengakuan Kompetensi Terkini (PKT)';
    case RPL = 'Rekognisi Pembelajaran Lampau (RPL)';
    case LAINNYA = 'Lainnya';

    public function label()
    {
        return match ($this){
            self::SERTIFIKASI=> 'Selesai',
            self::PKT=> 'Pengakuan Kompetensi Terkini (PKT)',
            self::RPL => 'Rekognisi Pembelajaran Lampau (RPL)',
            self::LAINNYA => 'Lainnya',
        };
    }

    public static function options()
    {
        return collect(self::cases())->map(fn($case) => [
            'value' => $case->value,
            'text' => $case->label(),
        ])->toArray();
    }
}
