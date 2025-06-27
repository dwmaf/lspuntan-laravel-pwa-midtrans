<?php

namespace Database\Seeders;

use App\Models\Sertification;
use App\Models\Student;
use App\Models\User;
use App\Models\Skema;
use App\Models\Asesor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
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

        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'email' => 'd10412110' . str_pad($i, 2, '0', STR_PAD_LEFT) . '@student.untan.ac.id',
                'password' => Hash::make('1234'),
                'role' => 'asesi',
            ]);

            event(new Registered($user));
            Student::create([
                'user_id' => $user->id,
            ]);
        }
    }
}
