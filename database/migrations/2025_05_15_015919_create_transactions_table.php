<?php

use App\Enums\TransactionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asesi_id')->constrained();
            $table->foreignId('sertification_id')->constrained();
            $table->string('status')->default(TransactionStatus::PENDING->value);
            $table->string('tipe');
            $table->string('invoice_number')->unique()->nullable();
            $table->string('bukti_bayar')->nullable();
            $table->text('snap_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
