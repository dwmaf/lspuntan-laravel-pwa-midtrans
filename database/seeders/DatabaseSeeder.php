<?php

namespace Database\Seeders;

use App\Models\Sertification;
use App\Models\Student;
use App\Models\User;
use App\Models\Skema;
use App\Models\Asesor;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'email' => 'admin@g.c',
            'password' => bcrypt('1234'),
            'role' => 'admin'
        ]);
    }
}
