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
            'apl_1' => 'seed/apl_1.pdf',
            'apl_2' => 'seed/apl_2.pdf',
            'foto_ktm' => 'seed/apl_2.pdf',
            'transkrip_nilai' => 'seed/apl_2.pdf',
            'rekap_nilai' => 'Strago A, Strukdat A',
        ];
    }
}