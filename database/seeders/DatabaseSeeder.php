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
            'name' => 'afif',
            'email' => 'admin@g.c',
            'password' => bcrypt('1234'),
            'role' => 'admin'
        ]);
        User::create([
            'name' => 'dawam',
            'email' => 'dwmasesi@g.c',
            'password' => bcrypt('1234'),
            'role' => 'asesi'
        ]);
        Student::create([
            'user_id' => 2,
        ]);
        // $asesor = Asesor::create([
        //     'user_id' => 1,
        // ]);
        // Skema::create([
        //     'nama_skema' => 'Skema Programmer'
        // ]);
        // Skema::create([
        //     'nama_skema' => 'Skema Analisis K3 Umum'
        // ]);
        // $skema = Skema::find(2);
        // $asesor->skemas()->attach($skema->id);
        // Sertification::create([
        //     'skema_id' => 2,
        //     'asesor_id' => 1,
        //     'tgl_apply_dibuka' => '2023-08-01', 
        //     'tgl_apply_ditutup' => '2023-08-31', 
        //     'tgl_bayar_ditutup' => '2023-09-15 23:59:59',
        // ]);
    }
}
