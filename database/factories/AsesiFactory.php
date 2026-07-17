<?php

namespace Database\Factories;

use App\Models\Sertifikasi;
use App\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asesi>
 */
class AsesiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'mahasiswa_id' => Mahasiswa::factory(),
            'sertifikasi_id' => Sertifikasi::factory(),
            'tujuan_sert' => 'Sertifikasi',
            'foto_ktm' => 'seed/ktm.pdf',
            'transkrip_nilai' => 'seed/transkrip_nilai.pdf',
            'rekap_nilai' => 'Strago A, Strukdat A',
        ];
    }
}