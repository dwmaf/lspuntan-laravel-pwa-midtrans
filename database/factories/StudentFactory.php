<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nik' => fake()->unique()->numerify('################'),
            'nim' => fake()->unique()->numerify('H10512####'),
            'tmpt_lhr' => fake()->city(),
            'tgl_lhr' => fake()->dateTimeBetween('-30 years', '-18 years')->format('Y-m-d'),
            'kelamin' => fake()->randomElement(['Laki-laki', 'Perempuan']),
            'kebangsaan' => 'Indonesia',
            'no_tlp_rmh' => fake()->phoneNumber(),
            'no_tlp_kntr' => fake()->phoneNumber(),
            'kualifikasi_pendidikan' => fake()->randomElement(['S1', 'D3', 'SMA/SMK']),
            'nama_institusi' => fake()->company(),
            'jabatan' => fake()->jobTitle(),
            'alamat_kantor' => fake()->address(),
            'no_tlp_email_fax' => fake()->companyEmail(),
            'foto_ktp' => 'seed/ktp.jpg',
            'pas_foto' => 'seed/pas_foto.jpg',
        ];
    }
}