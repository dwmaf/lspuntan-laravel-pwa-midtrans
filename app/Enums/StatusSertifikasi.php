<?php

namespace App\Enums;

enum StatusSertifikasi: string
{
    case SELESAI = 'selesai';
    case BERLANGSUNG = 'berlangsung';
    case DIBATALKAN = 'dibatalkan';

    public function label()
    {
        return match ($this){
            self::SELESAI=> 'Selesai',
            self::BERLANGSUNG=> 'Sedang Berlangsung',
            self::DIBATALKAN => 'Dibatalkan',
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
