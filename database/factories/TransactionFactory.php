<?php

namespace Database\Factories;

use App\Enums\TransactionStatus;
use App\Models\Asesi;
use App\Models\PaymentInstruction;
use App\Models\Sertification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'asesi_id' => Asesi::factory(),
            'sertification_id' => Sertification::factory(),
            'status' => TransactionStatus::PENDING,
            'tipe' => 'manual',
            'bukti_bayar' => 'seed/default.jpg',
        ];
    }
}