# LSP UNTAN - Laravel PWA Midtrans

Aplikasi Progressive Web App (PWA) untuk sistem sertifikasi LSP UNTAN dengan integrasi payment gateway Midtrans.

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Tentang Proyek

Proyek ini merupakan aplikasi web berbasis Laravel untuk mengelola sistem sertifikasi di LSP UNTAN, dilengkapi dengan:

- Progressive Web App (PWA) untuk pengalaman mobile-first
- Integrasi payment gateway Midtrans
- Sistem notifikasi push dengan Firebase Cloud Messaging (FCM)
- Dashboard admin, asesor, dan asesi
- Manajemen dokumen dan sertifikat

## Dokumentasi

Untuk panduan lengkap instalasi, pengembangan, dan fitur aplikasi, silakan lihat:

- [Panduan Developer](public/information.md) - Instalasi, setup, dan catatan pengembangan
- [Perbandingan Chat Agents](docs/COPILOT_AGENTS_COMPARISON.md) - Informasi tentang Local vs Cloud AI Agents untuk developer

## Quick Start

```bash
# Clone repository
git clone [URL_REPOSITORY]
cd lspuntan-laravel-pwa-midtrans

# Install dependencies
composer install
pnpm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate:fresh --seed

# Run development servers
pnpm run dev        # Terminal 1
php artisan serve   # Terminal 2
```

## Tech Stack

- **Framework**: Laravel 11.x
- **Frontend**: Vite, Alpine.js
- **Database**: SQLite / MySQL
- **Payment**: Midtrans
- **PWA**: Workbox
- **Notifications**: Firebase Cloud Messaging

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
