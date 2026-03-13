# LSP UNTAN - Laravel PWA

Aplikasi PWA (Progressive Web App) berbasis Laravel untuk LSP UNTAN. Proyek ini menggunakan Laravel sebagai backend, dengan teknologi Inertia.js / Vue.js untuk frontend (di-build menggunakan Vite), serta terintegrasi dengan Firebase Cloud Messaging (FCM) untuk fitur push notification.

## Prasyarat

Pastikan perangkat Anda sudah terinstal:
- PHP >= 8.1
- Composer
- Node.js & NPM / pnpm
- MySQL / MariaDB

## Langkah-langkah Instalasi

### 1. Clone Repository
Clone repository proyek ini ke direktori lokal Anda:
```bash
git clone <url-repository>
cd lsp-untan-laravel-pwa
```

### 2. Install Dependencies
Install seluruh dependency untuk PHP dan Node.js:
```bash
composer install
npm install
# atau jika menggunakan pnpm:
# pnpm install
```

### 3. Konfigurasi Environment (`.env`)
Copy file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```

Sesuaikan konfigurasi di dalam file `.env` berdasarkan spesifikasi lingkungan lokal Anda. Berikut beberapa konfigurasi utama yang perlu diperhatikan:

**a. URL Aplikasi (Penting untuk PWA & Push Notification)**
Jika Anda menggunakan ngrok atau domain lokal, pastikan menggunakan `https` (Service Worker membutuhkan HTTPS) atau `localhost`:
```env
APP_NAME="LSP UNTAN"
APP_ENV=local
APP_URL=https://undaughterly-lon-subexternally.ngrok-free.dev
# atau APP_URL=http://localhost:8000
```

**b. Konfigurasi Database**
Buat database bernama `skripsi_pwa` di MySQL, dan sesuaikan dengan kredensial Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=skripsi_pwa
DB_USERNAME=root
DB_PASSWORD=password_database_anda
```

**c. Konfigurasi Firebase & Push Notification (FCM)**
Aplikasi ini menggunakan Firebase Admin SDK dan VAPID. Pastikan Anda memiliki file kredensial JSON Firebase Service Account dan konfigurasi frontend diletakkan di `.env`:
```env
# File Service Account JSON (simpan di storage/app/firebase/...)
FIREBASE_CREDENTIALS=storage/app/firebase/file-jsonn.json

# Konfigurasi Frontend Firebase
VITE_FCM_API_KEY=
VITE_FCM_AUTH_DOMAIN=
VITE_FCM_PROJECT_ID=
VITE_FCM_STORAGE_BUCKET=
VITE_FCM_MESSAGING_SENDER_ID=
VITE_FCM_APP_ID=
VITE_FCM_MEASUREMENT_ID=

# Kunci Publik VAPID untuk Web Push Notification
VITE_VAPID_PUBLIC_KEY=
```

**d. Konfigurasi Mail (opsional)**
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
...
```

**e. Konfigurasi Queue**
Proyek ini menggunakan database untuk queue (seperti pengiriman email/notifikasi di background).
```env
QUEUE_CONNECTION=database
```

### 4. Setup Aplikasi Laravel
Jalankan command-command berikut untuk inisialisasi aplikasi:
```bash
# Generate APP_KEY
php artisan key:generate

# Menjalankan migrasi database (beserta data dummy jika ada seeder)
php artisan migrate --seed

# Membuat link untuk folder storage (supaya file upload bisa diakses publik)
php artisan storage:link
```

## Menjalankan Aplikasi

Untuk menjalankan aplikasi secara lokal dengan fungsionalitas penuh (termasuk background job & queue), Anda perlu membuka beberapa tab terminal.

**Terminal 1: Laravel Backend Server**
```bash
php artisan serve
```

**Terminal 2: Vite Frontend Server (Hot-Reloading)**
```bash
npm run dev
# atau pnpm run dev
```

**Terminal 3: Queue Worker (Untuk notifikasi FCM / Job background lainnya)**
```bash
php artisan queue:work
```

*(Opsional)* **Terminal 4: Ngrok (Jika ingin tes Push Notification & PWA di Device Lain via HTTPS)**
```bash
ngrok http --domain=undaughterly-lon-subexternally.ngrok-free.dev 8000
```
*(Jangan lupa sesuaikan `APP_URL` di `.env` dengan domain ngrok Anda dan restart server Vite & Laravel setelah mengubah .env)*

## PWA & Notifikasi
Karena aplikasi ini adalah Progressive Web App, beberapa fitur canggih seperti Push Notification (FCM) dan Add to Home Screen mungkin mewajibkan akses melalui **HTTPS** (atau `localhost`). Pastikan `APP_URL` dan jaringan yang digunakan sudah mendukung hal tersebut.
