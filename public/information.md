Agar data dari eloquent bisa diterima oleh alpine, perlu dilakukan hal berikut
JSON.parse('{{ json_encode($skemas) }}').

pnpm install, JANGAN pakai npm install

extension sodium harus dienable nanti di hostingan

fas : fontawesome
bxs : boxicons
tni : teeny icons

feather-icons-react 0.9.0

php artisan migrate:fresh --seed
php artisan db:seed

-mustverifyemail nya kita nonaktifin dulu utk mempercepat development

what to check if u connect firebase to your app:
- /android/app/build.gradle.kts
- /android/settings.gradle.kts
- MainActivity.kt

DAFTAR NOTIFIKASI IN APP YG HARUS DIBUAT:
- ketika student daftar (kasih ke asesor yg bersangkutan dan admin)
- ketika admin/asesor update status asesi (kasih ke asesi yg bersangkutan)
- ketika student update data asesinya (kasih ke asesor yg bersangkutan dan admin)
- ketika admin buat rincian pembayaran (kasih ke asesi yg statusnya 'dilanjutkan_asesmen')
- ketika admin/asesor buat pengumuman (kasih ke asesi yg statusnya 'dilanjutkan_asesmen')
- ketika asesor buat rincian tugas asesmen (kasih ke asesi yg statusnya 'dilanjutkan_asesmen' & status bayarnya 'pembayaran_terverifikasi')
- ketika admin upload sertifikat (kasih ke asesi yg bersangkutan)


CATATAN FILE DI TIAP" TABEL UNTUK TIAP ROLE:
Asesi:
-Tabel Student : foto_ktp, pas_foto (single, cara hapusnya butuh id student, trus hapus kolom tersebut).
-Tabel Asesi : apl_1, apl_2 (single, cara hapusnya butuh asesi id, trus hapus kolom tersebut).
-Tabel Asesifile : ada kolom type () dan path_file, dia 1 to M dari tabel asesi, cara hapusnya butuh id dari asesifile, trus hapus satu recordnya langsung, ini file" yg diinput ketika daftar.
-Tabel Asesiasesmenfile : ada path_file, dia 1 to M dari Asesi, ini file" yang diinput ketika ngerjain tugas, cara hapusnya butuh id dari asesiasesmenfile.

Admin:
-Tabel Pengumumanfile : dia 1 to M dari pengumuman, ini file" yg diinput ketika buat pengumuman, pengumuman tuh 1 to M dari sertification
-Tabel Asesmenfile : dia 1 to M dari sertification, ini file" yg diinput ketika buat asesmen


skenarionya gini, kalo misal dia kuliah di dua jurusan, jurusan infor dan bisnis

