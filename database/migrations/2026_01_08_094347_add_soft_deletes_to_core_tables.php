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
        Schema::table('asesors', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('skemas', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('sertifications', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('asesis', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asesors', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('skemas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('sertifications', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('asesis', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
