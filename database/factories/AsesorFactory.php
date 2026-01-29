<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asesor>
 */
class AsesorFactory extends Factory
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
            'no_reg_met' => 'MET.' . $this->faker->numerify('###.######.####'),
            'masa_berlaku_sertif_teknis' => $this->faker->dateTimeBetween('now', '+3 years')->format('Y-m-d'),
            'masa_berlaku_sertif_asesor' => $this->faker->dateTimeBetween('now', '+3 years')->format('Y-m-d'),
        ];
    }
}
