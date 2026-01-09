<?php

namespace App\Enums;

enum StatusAksesMenuAsesmen: string
{
    case BELUM_DIBERIKAN = 'belum_diberikan';
    case DIBERIKAN = 'diberikan';

    public function label()
    {
        return match($this) {
            self::BELUM_DIBERIKAN => 'Belum Diberikan',
            self::DIBERIKAN => 'Diberikan',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->map(fn($case) => [
            'value' => $case->value,
            'text' => $case->label(),
        ])->toArray();
    }
}
