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
        $skema1 = Skema::create(['nama_skema' => 'Skema Programmer']);
        $skema2 = Skema::create(['nama_skema' => 'Skema Ahli K3 Umum']);
        $skema3 = Skema::create(['nama_skema' => 'Skema Pendamping UMKM']);
        $skema4 = Skema::create(['nama_skema' => 'Skema Teknisi Pendengaran Jauh']);
        $skema5 = Skema::create(['nama_skema' => 'Penyuluh Kehutanan Fasilitator']);
        $skema6 = Skema::create(['nama_skema' => 'Skema Analis Sumber Daya Manusia']);
        $skema7 = Skema::create(['nama_skema' => 'Skema Pengoperasian PLC (Programmable Logic Controller)']);
        $skema8 = Skema::create(['nama_skema' => 'Skema Penerapan K3-Laboratorium']);
        // Buat user admin dan berikan role 'admin'
        $admin = User::create([
            'email' => 'admin@g.c',
            'password' => bcrypt('1234'),
        ]);
        $asesor1 = User::create([
            'email' => 'azhar@asesor.c',
            'password' => bcrypt('12345678'),
        ]);
        $admin->assignRole('admin');
        $admin->assignRole('asesor');
        $asesor1->assignRole('asesor');
        event(new Registered($admin));
        $asesorafif = Asesor::create([
                'user_id' => $admin->id,
                'name' => 'Afif',
        ]);
        $asesorazhar = Asesor::create([
                'user_id' => $asesor1->id,
                'name' => 'Azhar',
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
        $asesorazhar->skemas()->attach($skema1->id);
        $asesorafif->skemas()->attach($skema2->id);
    }
}
