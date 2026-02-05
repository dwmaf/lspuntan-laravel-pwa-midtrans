<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Skema>
 */
class SkemaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_skema' => 'Skema Sertifikasi ' . fake()->unique()->words(3, true),
            'format_apl_1' => 'seed/format_apl.pdf', // Contoh path file, bisa null jika diinginkan
            'format_apl_2' => 'seed/format_apl.pdf', // Contoh path file, bisa null jika diinginkan
            'format_asesmen' => 'seed/format_asesmen.zip',
        ];
    }
}