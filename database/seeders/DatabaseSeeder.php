<?php

namespace Database\Seeders;

use App\Models\Sertification;
use App\Models\Student;
use App\Models\User;
use App\Models\Skema;
use App\Models\Asesor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil RoleSeeder terlebih dahulu
        $this->call(RoleSeeder::class);

        // Buat user admin dan berikan role 'admin'
        $admin = User::create([
            'email' => 'admin@g.c',
            'password' => bcrypt('1234'),
        ]);
        $admin->assignRole('admin');
        $admin->assignRole('asesor');
        Asesor::create([
                'user_id' => $admin->id,
                'name' => 'Afif',
        ]);
        // Buat user asesi dan berikan role 'asesi'
        $user = User::create([
            'email' => 'd1041211005@student.untan.ac.id',
            'password' => Hash::make('12345678'),
        ]);
        $user->assignRole('asesi');
        event(new Registered($user));
        Student::create([
            'user_id' => $user->id,
        ]);

        $skema1 = Skema::create(['nama_skema' => 'Skema Programmer']);
        $skema2 = Skema::create(['nama_skema' => 'Skema Ahli K3 Umum']);
    }
}
