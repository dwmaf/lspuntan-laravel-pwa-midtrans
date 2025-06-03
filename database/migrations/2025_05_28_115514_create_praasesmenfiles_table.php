<?php

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
        Schema::create('praasesmenfiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sertification_id')->constrained();
            $table->string('praasesmen_attachment_file')->nullable();
            $table->string('file_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('praasesmenfiles');
    }
};
