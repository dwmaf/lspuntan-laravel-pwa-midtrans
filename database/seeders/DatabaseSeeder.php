<?php

namespace Database\Seeders;

use App\Enums\AsesiStatus;
use App\Enums\TransactionStatus;
use App\Models\Asesi;
use App\Models\Asesor;
use App\Models\Sertification;
use App\Models\Skema;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

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

        // 1. Panggil RoleSeeder
        $this->call(RoleSeeder::class);

        // 2. Buat Skema Sertifikasi
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

        $skemas = collect(); // Buat koleksi kosong untuk menampung skema
        foreach ($namaSkemas as $nama) {
            $skemas->push(Skema::factory()->create([
                'nama_skema' => $nama,
            ]));
        }

        // 3. Buat User Admin Utama
        User::withoutEvents(function () use (&$admin, &$asesiUsers, $skemas) {
            echo "Membuat user (tanpa event)...\n";
            // Buat User Admin Utama
            $admin = User::create([
                'email' => 'admin@g.c',
                'name' => 'Afif Admin',
                'password' => Hash::make('1234'), 
                'email_verified_at' => now(),
            ]);
            $admin->assignRole('admin', 'asesor');
            Asesor::create(['user_id' => $admin->id]);

            // Buat 24 Asesor
            User::factory(24)->create()->each(function ($user) use ($skemas) {
                $user->assignRole('asesor');
                $asesor = Asesor::create(['user_id' => $user->id]);
                $asesor->skemas()->attach($skemas->random(rand(1, 3))->pluck('id'));
            });

            // Buat 300 User Asesi sebagai pool
            $asesiUsers = User::factory(300)->create()->each(function ($user) {
                $user->assignRole('asesi');
                Student::create(['user_id' => $user->id]);
            });
        });

        $allAsesors = Asesor::all();

        // 6. Buat 9 Sertifikasi yang SUDAH SELESAI
        echo "Membuat 9 sertifikasi yang sudah selesai...\n";
        for ($i = 0; $i < 9; $i++) {
            $tglSelesai = now()->subMonths(rand(1, 12));
            $sertification = Sertification::factory()->create([
                'skema_id' => $skemas->random()->id,
                'status' => 'selesai',
                'tgl_apply_dibuka' => $tglSelesai->copy()->subWeeks(2),
                'tgl_apply_ditutup' => $tglSelesai->copy()->subWeek(),
            ]);

            // Tambahkan payment instruction
            $paymentInstruction = $sertification->paymentInstruction()->create([
                'biaya' => rand(500, 2000) * 1000,
                'deadline' => $tglSelesai->copy()->subDays(3),
                'user_id' => $admin->id,
                'content' => 'Pembayaran untuk sertifikasi yang telah selesai.',
                'published_at' => now(),
            ]);

            // Assign 1-2 asesor ke sertifikasi ini
            $sertification->asesors()->attach($allAsesors->random(rand(1, 2))->pluck('id'));

            // Daftarkan 20-30 asesi ke sertifikasi ini
            $pendaftar = $asesiUsers->random(rand(20, 30));
            foreach ($pendaftar as $user) {
                $asesi = Asesi::factory()->create([
                    'student_id' => $user->student->id,
                    'sertification_id' => $sertification->id,
                    'status' => AsesiStatus::LULUS_SERTIFIKASI,
                ]);
                Transaction::factory()->create([
                    'asesi_id' => $asesi->id,
                    'sertification_id' => $sertification->id,
                    'status' => TransactionStatus::BUKTI_PEMBAYARAN_TERVERIFIKASI,
                    'tipe' => 'manual',
                    'bukti_bayar' => 'seed/bukti_bayar.jpg',
                ]);
            }
        }

        // 7. Buat 4 Sertifikasi yang BERLANGSUNG
        echo "Membuat 4 sertifikasi yang sedang berlangsung...\n";
        for ($i = 0; $i < 4; $i++) {
            $tglBuka = now()->subDays(rand(5, 10));
            $sertification = Sertification::factory()->create([
                'skema_id' => $skemas->random()->id,
                'status' => 'berlangsung',
                'tgl_apply_dibuka' => $tglBuka,
                'tgl_apply_ditutup' => $tglBuka->copy()->addWeeks(2),
            ]);

            // Tambahkan payment instruction
            $sertification->paymentInstruction()->create([
                'biaya' => rand(500, 2000) * 1000,
                'deadline' => $tglBuka->copy()->addWeeks(3),
                'user_id' => $admin->id,
                'content' => 'Silakan lakukan pembayaran sesuai nominal yang tertera.',
                'published_at' => now(),
            ]);

            // Assign 1-2 asesor ke sertifikasi ini
            $sertification->asesors()->attach($allAsesors->random(rand(1, 2))->pluck('id'));

            // Daftarkan 20-30 asesi ke sertifikasi ini
            $pendaftar = $asesiUsers->random(rand(20, 30));
            foreach ($pendaftar as $user) {
                $randomStatus = [
                    AsesiStatus::MENUNGGU_VERIFIKASI_BERKAS,
                    AsesiStatus::PERLU_PERBAIKAN_BERKAS,
                    AsesiStatus::DILANJUTKAN_ASESMEN,
                ];
                $asesi = Asesi::factory()->create([
                    'student_id' => $user->student->id,
                    'sertification_id' => $sertification->id,
                    // Status acak untuk sertifikasi yang berlangsung
                    'status' => $randomStatus[array_rand($randomStatus)], // Gunakan Enum
                ]);
                $randomTransactionStatus = [
                    TransactionStatus::PENDING,
                    TransactionStatus::BUKTI_PEMBAYARAN_DITOLAK,
                ];
                Transaction::factory()->create([
                    'asesi_id' => $asesi->id,
                    'sertification_id' => $sertification->id,
                    'status' => $randomTransactionStatus[array_rand($randomTransactionStatus)], // Gunakan Enum
                    'tipe' => 'manual',
                    'bukti_bayar' => 'seed/bukti_bayar.jpg',
                ]);
            }
        }

        // 8. Pastikan beberapa asesi mendaftar lebih dari 1 sertifikasi
        echo "Memastikan beberapa asesi mendaftar lebih dari satu sertifikasi...\n";
        $sertifications = Sertification::all();
        $asesiMultiDaftar = $asesiUsers->random(25); // Ambil 25 asesi untuk daftar lagi
        foreach ($asesiMultiDaftar as $user) {
            // Cari sertifikasi yang belum pernah dia ikuti
            $sertifikasiSudahDiikuti = Asesi::where('student_id', $user->student->id)->pluck('sertification_id');
            $sertifikasiTersedia = $sertifications->whereNotIn('id', $sertifikasiSudahDiikuti);

            if ($sertifikasiTersedia->isNotEmpty()) {
                $sertifikasiBaru = $sertifikasiTersedia->random();
                Asesi::factory()->create([
                    'student_id' => $user->student->id,
                    'sertification_id' => $sertifikasiBaru->id,
                    // 'status' => $sertifikasiBaru->status === 'selesai' ? AsesiStatus::LULUS_SERTIFIKASI : AsesiStatus::MENUNGGU_VERIFIKASI_BERKAS, // Gunakan Enums
                ]);
            }
        }
        
    }
}