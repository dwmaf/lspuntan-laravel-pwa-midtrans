Agar data dari eloquent bisa diterima oleh alpine, perlu dilakukan hal berikut
JSON.parse('{{ json_encode($skemas) }}').

Buat tabel pivot asesi_sertifications = done
pnpm install, jgn pakai npm install

fas : fontawesome
bxs : boxicons
tni : teeny icons

feather-icons-react 0.9.0

-kasih kolom 'batas_pengumpulan_tugas_asesmen','tuk' di sertifications = done, tinggal migrate = done

-kolom khs nye taruh di asesiattachmentfiles, soalnya perlu khs dari semester 1-5 = 

-kolom makul_nilai itu bakal dibuat tabel baru, jadi relasinya asesi 1 to M makulnilai (kolomnya, nama mata kuliah, dan nilai)

php artisan migrate:fresh --seed
php artisan db:seed

-mustverifyemail nya kita nonaktifin dulu utk mempercepat development

@if (!$latestTransaction) disabled @endif

what to check if u connect firebase to your app:
- /android/app/build.gradle.kts
- /android/settings.gradle.kts
- 