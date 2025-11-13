# LSP UNTAN PWA - Panduan Developer

Dokumen ini berisi panduan instalasi, catatan pengembangan, dan roadmap untuk aplikasi PWA LSP UNTAN.

## 1. Prasyarat

Pastikan lingkungan pengembangan Anda memenuhi persyaratan berikut:
- PHP 8.2 atau lebih tinggi
- Composer 2.x
- Node.js 20.x atau lebih tinggi
- **pnpm** sebagai package manager (`npm install -g pnpm`)
- Ekstensi PHP `sodium` (diperlukan untuk beberapa dependensi, pastikan aktif di lingkungan produksi/hosting)

## 2. Instalasi & Setup

Ikuti langkah-langkah berikut untuk menjalankan proyek secara lokal.

1.  **Clone Repository**
    ```bash
    git clone [URL_REPOSITORY_ANDA]
    cd lsp-untan-laravel-pwa
    ```

2.  **Install Dependensi**
    ```bash
    # Install dependensi PHP
    composer install

    # Install dependensi JavaScript (WAJIB menggunakan pnpm)
    pnpm install
    ```

3.  **Konfigurasi Lingkungan**
    Salin file `.env.example` menjadi `.env` dan konfigurasikan variabel database serta kredensial lainnya.
    ```bash
    cp .env.example .env
    ```
    Setelah itu, jalankan perintah berikut untuk menghasilkan kunci aplikasi:
    ```bash
    php artisan key:generate
    ```

4.  **Migrasi & Seeding Database**
    Perintah ini akan menghapus semua data lama dan mengisi database dengan data awal (fresh start).
    ```bash
    php artisan migrate:fresh --seed
    ```

5.  **Jalankan Server Pengembangan**
    Buka dua terminal terpisah dan jalankan perintah berikut:
    ```bash
    # Terminal 1: Menjalankan server Vite untuk frontend
    pnpm run dev

    # Terminal 2: Menjalankan server PHP Laravel
    php artisan serve
    ```

## 3. Catatan Pengembangan

### Verifikasi Email
Untuk mempercepat proses pengembangan, fitur `MustVerifyEmail` pada model `User` saat ini dinonaktifkan. Fitur ini harus diaktifkan kembali sebelum masuk ke tahap produksi.

### Struktur Penyimpanan File
Berikut adalah ringkasan bagaimana file yang diunggah pengguna disimpan:

-   **Asesi:**
    -   `students` table: `foto_ktp`, `pas_foto` (File tunggal per kolom).
    -   `asesis` table: `apl_1`, `apl_2` (File tunggal per kolom).
    -   `asesi_files` table: File pendaftaran (Relasi One-to-Many dari `asesis`).
    -   `asesi_asesmen_files` table: File tugas asesmen (Relasi One-to-Many dari `asesis`).

-   **Admin/Asesor:**
    -   `pengumuman_files` table: Lampiran pengumuman (Relasi One-to-Many dari `pengumuman`).
    -   `asesmen_files` table: Lampiran rincian asesmen (Relasi One-to-Many dari `sertifications`).

### Sistem Notifikasi
Aplikasi ini memiliki dua jenis notifikasi:

1.  **Notifikasi In-App (Database):** Disimpan di tabel `notification_logs` dan ditampilkan melalui ikon lonceng di UI.
2.  **Notifikasi Push (FCM):** Dikirim secara real-time ke perangkat pengguna bahkan saat aplikasi tidak dibuka.

**Pemicu Notifikasi Saat Ini:**
-   **Pendaftar Baru:** Dikirim ke Admin & Asesor terkait.
-   **Update Status Asesi:** Dikirim ke Asesi yang bersangkutan.
-   **Update Data Asesi:** Dikirim ke Admin & Asesor terkait.
-   **Instruksi Pembayaran Dibuat:** Dikirim ke Asesi yang lolos seleksi berkas.
-   **Pengumuman Dibuat:** Dikirim ke Asesi yang relevan.
-   **Rincian Tugas Asesmen Dibuat:** Dikirim ke Asesi yang telah terverifikasi pembayarannya.
-   **Sertifikat Diunggah:** Dikirim ke Asesi yang bersangkutan.

## 4. Roadmap & Pengembangan Lanjutan

Berikut adalah daftar fitur yang direncanakan untuk pengembangan selanjutnya:

1.  **Preferensi Notifikasi Pengguna:**
    -   **Tujuan:** Memberi pengguna (terutama Admin/Asesor) kemampuan untuk memilih notifikasi push mana yang ingin mereka terima.
    -   **Implementasi:**
        -   Menambahkan kolom boolean baru di tabel `users` (e.g., `notif_pendaftar_baru`, `notif_pengumuman_baru`, dll).
        -   Membuat UI di halaman profil untuk mengelola preferensi ini.
        -   Menambahkan kondisi `->where('nama_kolom_notif', true)` pada query saat mengambil daftar penerima notifikasi.

2.  **Penyempurnaan Notifikasi Sertifikasi Baru:**
    -   **Tujuan:** Meningkatkan relevansi dan fungsionalitas notifikasi yang dikirim saat sertifikasi baru dibuat.
    -   **Batasan Saat Ini & Solusi yang Diusulkan:**
        -   **Penargetan (Targeting):** Saat ini, notifikasi dikirim ke *semua* pengguna dengan peran 'asesi'. Ini kurang ideal karena bisa jadi tidak relevan untuk semua jurusan.
            -   **Solusi:** Menambahkan relasi `jurusan` ke model `Skema` dan `Student`. Ini akan memungkinkan penargetan notifikasi yang jauh lebih akurat hanya kepada mahasiswa dari jurusan yang relevan.
        -   **Tandai Otomatis (Auto Mark-as-Read):** Notifikasi ini menggunakan `sendMulticast` dengan URL generik untuk efisiensi. Akibatnya, mengklik notifikasi push tidak akan menandai notifikasi di ikon lonceng sebagai terbaca.
            -   **Solusi:** Mengubah strategi pengiriman dari `sendMulticast` menjadi loop `sendTo` individual. Ini akan memungkinkan penyisipan `notification_id` yang unik di setiap URL, dengan konsekuensi sedikit penurunan performa pengiriman massal.

3.  **Dukungan Multi-Jurusan:**
    -   **Tujuan:** Menangani skenario di mana seorang mahasiswa terdaftar di lebih dari satu jurusan dan ingin mengikuti sertifikasi untuk keduanya.
    -   **Implementasi:** Memerlukan analisis lebih lanjut pada struktur relasi antara `student`, `jurusan`, dan `sertification`.