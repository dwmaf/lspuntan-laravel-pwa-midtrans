<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Skema;
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
            'password'=>bcrypt('1234'),
            'role'=>'admin'
        ]);
        User::create([
            'name' => 'dawam',
            'email' => 'dwmasesi@g.c',
            'password'=>bcrypt('1234'),
            'role'=>'asesi'
        ]);
        User::create([
            'name' => 'afif',
            'email' => 'asesor@g.c',
            'password'=>bcrypt('1234'),
            'role'=>'asesor'
        ]);
        Skema::create([
            'nama_skema' =>'Skema Programmer'
        ]);
        Skema::create([
            'nama_skema' =>'Skema Analisis K3 Umum'
        ]);
    }
}
