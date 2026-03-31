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
use Carbon\Carbon;

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

        
        echo "Membuat 12 sertifikasi yang sudah selesai...\n";
        for ($i = 0; $i < 12; $i++) {
            $selectedSkema = $skemas->random();
            $tglSelesai = now()->subMonths($i);

            // Simpan tanggal ke dalam variabel agar bisa digunakan untuk tanggal daftar (created_at) Asesi
            $tglDibuka = $tglSelesai->copy()->subWeeks(4);
            $tglDitutup = $tglSelesai->copy()->subWeek();

            $sertification = Sertification::factory()->create([
                'skema_id' => $selectedSkema->id,
                'status' => 'selesai',
                'tgl_apply_dibuka' => $tglDibuka,
                'tgl_apply_ditutup' => $tglDitutup,
                'biaya' => rand(100, 200) * 1000,
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

            $pendaftar = $asesiUsers->random(rand(10, 35));
            foreach ($pendaftar as $user) {
                // Generate tanggal daftar acak di antara tgl dibuka dan ditutup
                $tglDaftar = clone $tglDibuka;
                $tglDaftar->addMinutes(rand(0, $tglDibuka->diffInMinutes($tglDitutup)));
                $asesi = Asesi::factory()->create([
                    'student_id' => $user->student->id,
                    'sertification_id' => $sertification->id,
                    'status_final' => StatusFinalAsesi::KOMPETEN,
                    'bukti_bayar' => 'seed/bukti_bayar.jpg',
                    'created_at' => $tglDaftar,
                    'updated_at' => $tglDaftar,
                ]);
            }
        }


        echo "Membuat 4 sertifikasi yang sedang berlangsung...\n";
        for ($i = 0; $i < 4; $i++) {
            $selectedSkema = $skemas->random();
            $tglBuka = now()->subDays(rand(5, 10));
            $tglTutup = $tglBuka->copy()->addWeeks(2);

            $sertification = Sertification::factory()->create([
                'skema_id' => $selectedSkema->id,
                'status' => 'berlangsung',
                'tgl_apply_dibuka' => $tglBuka,
                'tgl_apply_ditutup' => $tglTutup,
                'tgl_asesmen_mulai' => $tglBuka->copy()->addWeeks(2),
                'biaya' => rand(100, 200) * 1000,
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
                $batasAkhir = now()->min($tglTutup); 
                $tglDaftar = clone $tglDibuka;
                $tglDaftar->addMinutes(rand(0, $tglBuka->diffInMinutes($batasAkhir)));

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
                    'created_at' => $tglDaftar, // Set created_at acak
                    'updated_at' => $tglDaftar,
                ]);
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

                // Hitung tanggal asesi dari rentang tgl_apply_dibuka
                $tglBukaBaru = Carbon::parse($sertifikasiBaru->tgl_apply_dibuka);
                $tglTutupBaru = Carbon::parse($sertifikasiBaru->tgl_apply_ditutup);
                $batasAkhirBaru = now()->min($tglTutupBaru);
                
                $tglDaftar = clone $tglBukaBaru;
                $tglDaftar->addMinutes(rand(0, max(1, $tglBukaBaru->diffInMinutes($batasAkhirBaru))));

                $asesi = Asesi::factory()->create([
                    'student_id' => $user->student->id,
                    'sertification_id' => $sertifikasiBaru->id,
                    'bukti_bayar' => 'seed/bukti_bayar.jpg',
                    'created_at' => $tglDaftar, // Set created_at acak
                    'updated_at' => $tglDaftar,
                ]);
            }
        }
    }
}
