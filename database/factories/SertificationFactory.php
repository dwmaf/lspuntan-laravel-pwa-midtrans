<?php

namespace Database\Factories;

use App\Models\Skema;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sertification>
 */
class SertificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'skema_id' => Skema::factory(),
            'tgl_apply_dibuka' => now(),
            'tgl_apply_ditutup' => now()->addWeeks(2),
            'status' => fake()->randomElement(['berlangsung', 'selesai']),
        ];
    }
}