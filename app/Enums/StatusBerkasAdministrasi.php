<?php

namespace App\Enums;

enum StatusBerkasAdministrasi: string
{
    case SUDAH_LENGKAP = 'sudah_lengkap';
    case MENUNGGU_VERIFIKASI_ADMIN = 'menunggu_verifikasi_admin';
    case PERLU_PERBAIKAN_BERKAS = 'perlu_perbaikan_berkas';

    public function label()
    {
        return match($this) {
            self::SUDAH_LENGKAP => 'Sudah Lengkap',
            self::MENUNGGU_VERIFIKASI_ADMIN => 'Menunggu Verifikasi Admin',
            self::PERLU_PERBAIKAN_BERKAS => 'Perlu Perbaikan Berkas',
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
