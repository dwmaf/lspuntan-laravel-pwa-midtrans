<?php

namespace Database\Seeders;

use App\Enums\StatusFinalAsesi;
use App\Enums\StatusBerkasAdministrasi;
use App\Enums\StatusSertifikasi;
use App\Models\Asesi;
use App\Models\Asesor;
use App\Models\Sertifikasi;
use App\Models\Skema;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        activity()->disableLogging();
        // Hapus data lama untuk memastikan kebersihan data (opsional, tapi direkomendasikan)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Asesi::truncate();
        DB::table('asesor_sertifikasi')->truncate();
        DB::table('asesor_skema')->truncate();
        Sertifikasi::truncate();
        Asesor::truncate();
        Mahasiswa::truncate();
        User::truncate();
        Skema::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        $this->call(RoleSeeder::class);


        $namaSkemas = [
            'Skema Pendamping UMKM',
            'Skema Ahli K3 Umum',
            'Skema Teknisi Penginderaan Jauh',
            'Skema Penyuluh Kehutanan Fasilitator',
            'Skema Analis Sumber Daya Manusia (SDM)',
            'Skema Pengoperasian PLC (Programmable Logic Controller)',
            'Skema Penerapan K3-Laboratorium',
            'Skema Programmer',
        ];
        $formatFileSkema = [
            ['seed/FR_pendampingUMKM_apl_1.docx', 'seed/FR_pendampingUMKM_apl_2.docx', 'seed/FR_pendampingUMKM_asesmen.zip'],
            ['seed/FR_K3_apl_1.docx', 'seed/FR_K3_apl_2.docx', 'seed/FR_K3_asesmen.zip'],
            ['seed/FR_teknikPenginderaanJauh_apl_1.docx', 'seed/FR_teknikPenginderaanJauh_apl_2.docx', 'seed/FR_teknikPenginderaanJauh_asesmen.zip'],
            ['seed/FR_penyuluhKehutananFasilitator_apl_1.docx', 'seed/FR_penyuluhKehutananFasilitator_apl_2.docx', 'seed/FR_penyuluhKehutananFasilitator_asesmen.zip'],
            ['seed/FR_analisSDM_apl_1.docx', 'seed/FR_analisSDM_apl_2.docx', 'seed/FR_analisSDM_asesmen.zip'],
            ['seed/FR_plc_apl_1.docx', 'seed/FR_plc_apl_2.docx', 'seed/FR_plc_asesmen.zip'],
            ['seed/FR_K3Lab_apl_1.docx', 'seed/FR_K3Lab_apl_2.docx', 'seed/FR_K3Lab_asesmen.zip'],
            ['seed/FR_programmer_apl_1.docx', 'seed/FR_programmer_apl_2.docx', 'seed/FR_programmer_asesmen.zip'],
        ];
        $asesiAPLFiles = [
            ['seed/pendampingUMKM_apl_1.docx', 'seed/pendampingUMKM_apl_2.docx'],
            ['seed/K3_apl_1.docx', 'seed/K3_apl_2.docx'],
            ['seed/teknikPenginderaanJauh_apl_1.docx', 'seed/teknikPenginderaanJauh_apl_2.docx'],
            ['seed/penyuluhKehutananFasilitator_apl_1.docx', 'seed/penyuluhKehutananFasilitator_apl_2.docx'],
            ['seed/analisSDM_apl_1.docx', 'seed/analisSDM_apl_2.docx'],
            ['seed/plc_apl_1.docx', 'seed/plc_apl_2.docx'],
            ['seed/K3Lab_apl_1.docx', 'seed/K3Lab_apl_2.docx'],
            ['seed/programmer_apl_1.docx', 'seed/programmer_apl_2.docx'],
        ];

        $skemas = collect();
        echo "Membuat 8 Skema Sertifikasi...\n";
        activity()->enableLogging();
        foreach ($namaSkemas as $i => $nama) {
            $skemas->push(Skema::factory()->create([
                'nama_skema' => $nama,
                'format_apl_1' => $formatFileSkema[$i][0],
                'format_apl_2' => $formatFileSkema[$i][1],
                'format_asesmen' => $formatFileSkema[$i][2],
            ]));
        }
        activity()->disableLogging();


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
            $adminasesor->skema()->attach([1]);
            $direktur = User::create([
                'email' => 'bomo@asesor.c',
                'name' => 'Bomo Wibowo',
                'password' => Hash::make('12345678'),
                'no_tlp_hp' => '081234567891',
                'email_verified_at' => now(),
            ]);
            $direktur->assignRole('admin', 'asesor');
            $direkturasesor = Asesor::factory()->create(['user_id' => $direktur->id]);
            $direkturasesor->skema()->attach([5]);
            Asesor::factory(24)->create()->each(function ($asesor, $index) use ($skemas) {
                $asesor->user->assignRole('asesor');
                $idSkemaWjib = $skemas->get($index % $skemas->count())->id;
                $idsSkemaTambahan = $skemas->random(rand(1, 2))->pluck('id')->toArray();
                $idsAllSkema = collect([$idSkemaWjib])->merge($idsSkemaTambahan)->unique();
                $asesor->skema()->attach($idsAllSkema);
            });

            $mahasiswa = User::create([
                'email' => 'mahasiswa1@student.c',
                'name' => 'Haningsih',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]);
            $mahasiswa->assignRole('asesi');
            Mahasiswa::create(['user_id' => $mahasiswa->id]);
            $asesiUsers = User::factory(300)->create()->each(function ($user) {
                $user->assignRole('asesi');
                Mahasiswa::factory()->create(['user_id' => $user->id]);
            });
        });

        $daftarTuk = [
            'Lab Komputer Gedung A lantai 2',
            'Lab Multimedia Gedung C',
            'Ruang Teori 05 Gedung Utama',
            'Lab Rekayasa Perangkat Lunak',
            'Aula Utama Kampus 1',
            'Lab Jaringan Gedung Elektro',
            'Lab Jaringan & Sistem Operasi',
        ];
        echo "Membuat 8 sertifikasi yang sudah selesai...\n";
        for ($i = 0; $i < 8; $i++) {
            $selectedSkema = $skemas->get($i);
            $tglSelesai = now()->subMonths($i);

            // Simpan tanggal ke dalam variabel agar bisa digunakan untuk tanggal daftar (created_at) Asesi
            $tglDibuka = $tglSelesai->copy()->subWeeks(4);
            $tglDitutup = $tglSelesai->copy()->subWeek();

            $sertifikasi = Sertifikasi::factory()->create([
                'skema_id' => $selectedSkema->id,
                'status' => 'selesai',
                'tgl_apply_dibuka' => $tglDibuka,
                'tgl_apply_ditutup' => $tglDitutup,
                'biaya' => rand(100, 200) * 1000,
                'no_rek' => '7126357123',
                'bank' => 'BSI',
                'tuk' => Arr::random($daftarTuk),
                'atas_nama_rek' => 'Empat Pilar Interactive',
            ]);

            $asesorTersedia = $selectedSkema->asesor;
            $idsAsesorTerpilih = collect();

            if ($asesorTersedia->count() > 0) {
                $jumlahAmbil = rand(1, min(3, $asesorTersedia->count()));
                $idsAsesorTerpilih = $asesorTersedia->random($jumlahAmbil)->pluck('id');
                $sertifikasi->asesor()->attach($idsAsesorTerpilih);
            }

            $pendaftar = $asesiUsers->random(rand(10, 20));
            foreach ($pendaftar as $user) {
                // Generate tanggal daftar acak di antara tgl dibuka dan ditutup
                $tglDaftar = clone $tglDibuka;
                $tglDaftar->addMinutes(rand(0, $tglDibuka->diffInMinutes($tglDitutup)));
                $skemaIndex = array_search($selectedSkema->nama_skema, $namaSkemas);
                $randomAsesorId = $idsAsesorTerpilih->random();
                if (rand(1, 100) <= 90) {
                    $statusFinal = StatusFinalAsesi::KOMPETEN;
                } else {
                    // 10% Sisanya baru dibagi rata ke status zonk lainnya
                    $statusFinal = Arr::random([
                        StatusFinalAsesi::BELUM_DITETAPKAN,
                        StatusFinalAsesi::DISKUALIFIKASI,
                        StatusFinalAsesi::BELUM_KOMPETEN,
                    ]);
                }
                Asesi::factory()->create([
                    'mahasiswa_id' => $user->mahasiswa->id,
                    'sertifikasi_id' => $sertifikasi->id,
                    'status_berkas' => StatusBerkasAdministrasi::SUDAH_LENGKAP,
                    'asesor_id' => $randomAsesorId,
                    'status_final' => $statusFinal,
                    'apl_1' => $asesiAPLFiles[$skemaIndex][0],
                    'apl_2' => $asesiAPLFiles[$skemaIndex][1],
                    'bukti_bayar' => 'seed/bukti_bayar.jpeg',
                    'created_at' => $tglDaftar,
                    'updated_at' => $tglDaftar,
                ]);
            }
        }


        echo "Membuat 2 sertifikasi yang sedang berlangsung...\n";
        for ($i = 0; $i < 2; $i++) {
            $indeksSkemaTarget = [1, 7];
            $selectedSkema = $skemas->get($indeksSkemaTarget[$i]);
            $tglBuka = now()->subDays(rand(5, 10));
            $tglTutup = $tglBuka->copy()->addWeeks(2);

            $sertifikasi = Sertifikasi::factory()->create([
                'skema_id' => $selectedSkema->id,
                'status' => 'berlangsung',
                'tgl_apply_dibuka' => $tglBuka,
                'tgl_apply_ditutup' => $tglTutup,
                'tgl_asesmen_mulai' => $tglBuka->copy()->addWeeks(2),
                'biaya' => rand(100, 200) * 1000,
                'no_rek' => '7126354612',
                'bank' => 'BSI',
                'tuk' => Arr::random($daftarTuk),
                'atas_nama_rek' => 'Empat Pilar Interactive',
            ]);

            $asesorTersedia = $selectedSkema->asesor;
            if ($asesorTersedia->count() > 0) {
                $jumlahAmbil = rand(1, min(2, $asesorTersedia->count()));
                $sertifikasi->asesor()->attach(
                    $asesorTersedia->random($jumlahAmbil)->pluck('id')
                );
            }

            $pendaftar = $asesiUsers->random(rand(10, 20));
            foreach ($pendaftar as $user) {
                // Determine status logic if needed, but keeping simple
                $batasAkhir = now()->min($tglTutup);
                $tglDaftar = clone $tglBuka;
                $tglDaftar->addMinutes(rand(0, $tglBuka->diffInMinutes($batasAkhir)));
                $skemaIndex = array_search($selectedSkema->nama_skema, $namaSkemas);
                Asesi::factory()->create([
                    'mahasiswa_id' => $user->mahasiswa->id,
                    'sertifikasi_id' => $sertifikasi->id,
                    'status_berkas' => Arr::random([
                        StatusBerkasAdministrasi::SUDAH_LENGKAP,
                        StatusBerkasAdministrasi::MENUNGGU_VERIFIKASI_ADMIN,
                        StatusBerkasAdministrasi::PERLU_PERBAIKAN_BERKAS,
                    ]),
                    'apl_1' => $asesiAPLFiles[$skemaIndex][0],
                    'apl_2' => $asesiAPLFiles[$skemaIndex][1],
                    'bukti_bayar' => 'seed/bukti_bayar.jpeg',
                    'created_at' => $tglDaftar,
                    'updated_at' => $tglDaftar,
                ]);
            }
        }


        echo "Memastikan beberapa asesi mendaftar lebih dari satu sertifikasi...\n";
        $sertifikasis = Sertifikasi::all();
        $asesiMultiDaftar = $asesiUsers->random(25);
        foreach ($asesiMultiDaftar as $user) {
            $sertifikasiSudahDiikuti = Asesi::where('mahasiswa_id', $user->mahasiswa->id)->pluck('sertifikasi_id');
            $sertifikasiTersedia = $sertifikasis->whereNotIn('id', $sertifikasiSudahDiikuti);

            if ($sertifikasiTersedia->isNotEmpty()) {
                $sertifikasiBaru = $sertifikasiTersedia->random();

                // Hitung tanggal asesi dari rentang tgl_apply_dibuka
                $tglBukaBaru = Carbon::parse($sertifikasiBaru->tgl_apply_dibuka);
                $tglTutupBaru = Carbon::parse($sertifikasiBaru->tgl_apply_ditutup);
                $batasAkhirBaru = now()->min($tglTutupBaru);

                $tglDaftar = clone $tglBukaBaru;
                $tglDaftar->addMinutes(rand(0, max(1, $tglBukaBaru->diffInMinutes($batasAkhirBaru))));

                $skemaIndex = array_search($sertifikasiBaru->skema->nama_skema, $namaSkemas);

                $statusBerkas = null;
                $statusFinal = null;
                $asesorId = null;

                if ($sertifikasiBaru->status->value === StatusSertifikasi::SELESAI->value) {
                    $statusBerkas = StatusBerkasAdministrasi::SUDAH_LENGKAP;
                    if (rand(1, 100) <= 90) {
                        $statusFinal = StatusFinalAsesi::KOMPETEN;
                    } else {
                        $statusFinal = Arr::random([
                            StatusFinalAsesi::BELUM_DITETAPKAN,
                            StatusFinalAsesi::DISKUALIFIKASI,
                            StatusFinalAsesi::BELUM_KOMPETEN,
                        ]);
                    }
                    $asesorId = $sertifikasiBaru->asesor->random()->id;
                } else {
                    $statusBerkas = Arr::random([
                        StatusBerkasAdministrasi::SUDAH_LENGKAP,
                        StatusBerkasAdministrasi::MENUNGGU_VERIFIKASI_ADMIN,
                        StatusBerkasAdministrasi::PERLU_PERBAIKAN_BERKAS,
                    ]);
                    $statusFinal = StatusFinalAsesi::BELUM_DITETAPKAN;
                }
                Asesi::factory()->create([
                    'mahasiswa_id' => $user->mahasiswa->id,
                    'sertifikasi_id' => $sertifikasiBaru->id,
                    'status_berkas' => $statusBerkas,
                    'asesor_id' => $asesorId,
                    'status_final' => $statusFinal,
                    'apl_1' => $asesiAPLFiles[$skemaIndex][0],
                    'apl_2' => $asesiAPLFiles[$skemaIndex][1],
                    'bukti_bayar' => 'seed/bukti_bayar.jpeg',
                    'created_at' => $tglDaftar,
                    'updated_at' => $tglDaftar,
                ]);
            }
        }
        activity()->enableLogging();
    }
}
