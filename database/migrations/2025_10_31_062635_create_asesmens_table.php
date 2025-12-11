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
        Schema::create('asesmens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sertification_id')->constrained()->onDelete('cascade');
            $table->text('content')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->timestamp('content_created_at')->nullable();
            $table->timestamp('revised_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asesmens');
    }
};
