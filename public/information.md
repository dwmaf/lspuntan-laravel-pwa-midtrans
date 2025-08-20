Agar data dari eloquent bisa diterima oleh alpine, perlu dilakukan hal berikut
JSON.parse('{{ json_encode($skemas) }}').

pnpm install, JANGAN pakai npm install

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