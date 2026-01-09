<?php

namespace App\Enums;

enum StatusFinalAsesi: string
{
    case KOMPETEN = 'kompeten';
    case BELUM_KOMPETEN = 'belum_kompeten';
    case BELUM_DITETAPKAN = 'belum_ditetapkan';
    case DISKUALIFIKASI = 'diskualifikasi';

    public function label()
    {
        return match($this) {
            self::KOMPETEN => 'Kompeten',
            self::BELUM_KOMPETEN => 'Belum Kompeten',
            self::BELUM_DITETAPKAN => 'Belum Ditetapkan',
            self::DISKUALIFIKASI => 'Diskualifikasi',
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
