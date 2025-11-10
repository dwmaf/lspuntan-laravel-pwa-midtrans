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
            'status' => AsesiStatus::MENUNGGU_VERIFIKASI_BERKAS,
            'tujuan_sert' => 'Sertifikasi',
            'apl_1' => 'seed/ktp.jpg',
            'apl_2' => 'seed/ktp.jpg',
        ];
    }
}