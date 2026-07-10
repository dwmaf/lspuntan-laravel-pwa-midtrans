<?php

namespace Database\Factories;

use App\Enums\AsesiStatus;
use App\Models\Sertification;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asesi>
 */
class AsesiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'sertification_id' => Sertification::factory(),
            'tujuan_sert' => 'Sertifikasi',
            'foto_ktm' => 'seed/ktm.pdf',
            'transkrip_nilai' => 'seed/transkrip_nilai.pdf',
            'rekap_nilai' => 'Strago A, Strukdat A',
        ];
    }
}