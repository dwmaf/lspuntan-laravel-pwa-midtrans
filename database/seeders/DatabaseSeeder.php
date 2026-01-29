<?php

namespace Database\Seeders;

use App\Enums\StatusFinalAsesi;
use App\Enums\StatusBerkasAdministrasi;
use App\Models\Asesi;
use App\Models\Asesor;
use App\Models\Makulnilai;
use App\Models\Sertification;
use App\Models\Skema;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Hapus data lama untuk memastikan kebersihan data (opsional, tapi direkomendasikan)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Asesi::truncate();
        DB::table('asesor_sertification')->truncate();
        DB::table('asesor_skema')->truncate();
        Sertification::truncate();
        Asesor::truncate();
        Student::truncate();
        User::truncate();
        Skema::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        $this->call(RoleSeeder::class);


        $namaSkemas = [
            'Skema Pendamping UMKM',
            'Skema Ahli K3 Umum',
            'Skema Teknisi Penginderaan Jauh',
            'Penyuluh Kehutanan Fasilitator',
            'Skema Analis Sumber Daya Manusia (SDM)',
            'Skema Pengoperasian PLC (Programmable Logic Controller)',
            'Skema Penerapan K3-Laboratorium',
            'Skema Programmer',
        ];

        $skemas = collect();
        foreach ($namaSkemas as $nama) {
            $skemas->push(Skema::factory()->create([
                'nama_skema' => $nama,
            ]));
        }

        $skemaMakuls = [
            'Skema Pendamping UMKM' => ['Manajemen UMKM', 'Analisis Kelayakan Bisnis', 'Pemasaran Strategis'],
            'Skema Ahli K3 Umum' => ['Dasar-Dasar K3', 'Hukum Ketenagakerjaan', 'Audit Internal'],
            'Skema Teknisi Penginderaan Jauh' => ['Sistem Informasi Geografis', 'Interpretasi Citra', 'Fotogrametri'],
            'Penyuluh Kehutanan Fasilitator' => ['Ekologi Hutan', 'Komunikasi Massa', 'Pengembangan Masyarakat'],
            'Skema Analis Sumber Daya Manusia (SDM)' => ['Manajemen SDM', 'Hubungan Industrial', 'Pelatihan dan Pengembangan'],
            'Skema Pengoperasian PLC (Programmable Logic Controller)' => ['Elektronika Industri', 'Logika Pemrograman', 'Kontrol Otomasi'],
            'Skema Penerapan K3-Laboratorium' => ['Keamanan Laboratorium', 'Kimia Analitik', 'Pengelolaan Limbah B3'],
            'Skema Programmer' => ['Algoritma dan Pemrograman', 'Struktur Data', 'Pemrograman Web', 'Basis Data'],
        ];


        /** @var \App\Models\User|null $admin */
        // /** @var \Illuminate\Database\Eloquent\Collection $asesiUsers */
        $admin = null;
        $asesiUsers = collect();
        User::withoutEvents(function () use (&$admin, &$asesiUsers, $skemas) {
            echo "Membuat user (tanpa event)...\n";
            $admin = User::create([
                'email' => 'admin@g.c',
                'name' => 'Afif',
                'password' => Hash::make('12345678'),
                'no_tlp_hp' => '081234567890',
                'email_verified_at' => now(),
            ]);
            $admin->assignRole('admin', 'asesor');
            $adminasesor = Asesor::factory()->create(['user_id' => $admin->id]);
            $adminasesor->skemas()->attach([1]);
            $direktur = User::create([
                'email' => 'bomo@asesor.c',
                'name' => 'Bomo Wibowo',
                'password' => Hash::make('12345678'),
                'no_tlp_hp' => '081234567891',
                'email_verified_at' => now(),
            ]);
            $direktur->assignRole('admin', 'asesor');
            $direkturasesor = Asesor::factory()->create(['user_id' => $direktur->id]);
            $direkturasesor->skemas()->attach([5]);
            Asesor::factory(24)->create()->each(function ($asesor) use ($skemas) {
                $asesor->user->assignRole('asesor');
                $asesor->skemas()->attach($skemas->random(rand(1, 3))->pluck('id'));
            });

            $student = User::create([
                'email' => 'mahasiswa1@student.c',
                'name' => 'Haningsih',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]);
            $student->assignRole('asesi');
            Student::create(['user_id' => $student->id]);
            $asesiUsers = User::factory(300)->create()->each(function ($user) {
                $user->assignRole('asesi');
                Student::factory()->create(['user_id' => $user->id]);
            });
        });

        $allAsesors = Asesor::all();

        // 6. Buat 9 Sertifikasi yang SUDAH SELESAI
        echo "Membuat 9 sertifikasi yang sudah selesai...\n";
        for ($i = 0; $i < 9; $i++) {
            $selectedSkema = $skemas->random();
            $tglSelesai = now()->subMonths(rand(1, 12));
            $sertification = Sertification::factory()->create([
                'skema_id' => $selectedSkema->id,
                'status' => 'selesai',
                'tgl_apply_dibuka' => $tglSelesai->copy()->subWeeks(2),
                'tgl_apply_ditutup' => $tglSelesai->copy()->subWeek(),
                'biaya' => rand(100, 400) * 1000,
                'no_rek' => '7126357123',
                'bank' => 'BSI',
                'atas_nama_rek' => 'Empat Pilar Interactive',
            ]);

            $asesorTersedia = $selectedSkema->asesors;

            if ($asesorTersedia->count() > 0) {
                $jumlahAmbil = rand(1, min(3, $asesorTersedia->count()));
                $sertification->asesors()->attach(
                    $asesorTersedia->random($jumlahAmbil)->pluck('id')
                );
            }

            $pendaftar = $asesiUsers->random(rand(20, 30));
            foreach ($pendaftar as $user) {
                $asesi = Asesi::factory()->create([
                    'student_id' => $user->student->id,
                    'sertification_id' => $sertification->id,
                    'status_final' => StatusFinalAsesi::KOMPETEN,
                    'bukti_bayar' => 'seed/bukti_bayar.jpg',
                ]);

                $makuls = $skemaMakuls[$selectedSkema->nama_skema] ?? [];
                foreach ($makuls as $makulName) {
                    Makulnilai::create([
                        'asesi_id' => $asesi->id,
                        'nama_makul' => $makulName,
                        'nilai_makul' => collect(['A', 'A-', 'B+', 'B'])->random(),
                    ]);
                }
            }
        }


        echo "Membuat 4 sertifikasi yang sedang berlangsung...\n";
        for ($i = 0; $i < 4; $i++) {
            $selectedSkema = $skemas->random();
            $tglBuka = now()->subDays(rand(5, 10));
            $sertification = Sertification::factory()->create([
                'skema_id' => $selectedSkema->id,
                'status' => 'berlangsung',
                'tgl_apply_dibuka' => $tglBuka,
                'tgl_apply_ditutup' => $tglBuka->copy()->addWeeks(2),
                'tgl_asesmen_mulai' => $tglBuka->copy()->addWeeks(2),
                'biaya' => rand(500, 2000) * 1000,
                'no_rek' => '7126354612',
                'bank' => 'BSI',
                'atas_nama_rek' => 'Empat Pilar Interactive',
            ]);

            $asesorTersedia = $selectedSkema->asesors;

            if ($asesorTersedia->count() > 0) {
                $jumlahAmbil = rand(1, min(3, $asesorTersedia->count()));

                $sertification->asesors()->attach(
                    $asesorTersedia->random($jumlahAmbil)->pluck('id')
                );
            }

            $pendaftar = $asesiUsers->random(rand(20, 30));
            foreach ($pendaftar as $user) {
                // Determine status logic if needed, but keeping simple
                $randomStatus = [
                    StatusBerkasAdministrasi::SUDAH_LENGKAP,
                    StatusBerkasAdministrasi::MENUNGGU_VERIFIKASI_ADMIN,
                    StatusBerkasAdministrasi::PERLU_PERBAIKAN_BERKAS,
                ];
                $asesi = Asesi::factory()->create([
                    'student_id' => $user->student->id,
                    'sertification_id' => $sertification->id,
                    'status_berkas' => $randomStatus[array_rand($randomStatus)],
                    'bukti_bayar' => 'seed/bukti_bayar.jpg',
                ]);

                $makuls = $skemaMakuls[$selectedSkema->nama_skema] ?? [];
                foreach ($makuls as $makulName) {
                    Makulnilai::create([
                        'asesi_id' => $asesi->id,
                        'nama_makul' => $makulName,
                        'nilai_makul' => collect(['A', 'A-', 'B+', 'B', 'C+', 'C'])->random(),
                    ]);
                }
            }
        }


        echo "Memastikan beberapa asesi mendaftar lebih dari satu sertifikasi...\n";
        $sertifications = Sertification::all();
        $asesiMultiDaftar = $asesiUsers->random(25);
        foreach ($asesiMultiDaftar as $user) {
            $sertifikasiSudahDiikuti = Asesi::where('student_id', $user->student->id)->pluck('sertification_id');
            $sertifikasiTersedia = $sertifications->whereNotIn('id', $sertifikasiSudahDiikuti);

            if ($sertifikasiTersedia->isNotEmpty()) {
                $sertifikasiBaru = $sertifikasiTersedia->random();
                $asesi = Asesi::factory()->create([
                    'student_id' => $user->student->id,
                    'sertification_id' => $sertifikasiBaru->id,
                    'bukti_bayar' => 'seed/bukti_bayar.jpg',
                ]);

                $makuls = $skemaMakuls[$sertifikasiBaru->skema->nama_skema] ?? [];
                foreach ($makuls as $makulName) {
                    Makulnilai::create([
                        'asesi_id' => $asesi->id,
                        'nama_makul' => $makulName,
                        'nilai_makul' => collect(['A', 'A-', 'B+', 'B', 'C+', 'C'])->random(),
                    ]);
                }
            }
        }
    }
}
