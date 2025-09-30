<?php

namespace App\Providers;

// 1. Import class yang dibutuhkan
// use Illuminate\Support\Facades\Event;
// use Illuminate\Auth\Events\Registered;
// use App\Listeners\SendQueuedEmailVerificationNotification;

use Illuminate\Support\ServiceProvider;
use App\Repositories\TransactionRepositoryInterface;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 2. Daftarkan event listener di sini
        // Event::listen(
        //     Registered::class,
        //     SendQueuedEmailVerificationNotification::class
        // );
        // 2. Letakkan kode View Composer di sini
        // Ini akan membagikan data notifikasi ke semua view yang menggunakan layout ini.
        // Pastikan nama layout di dalam array sesuai dengan nama file blade Anda.
        // View::composer(['layouts.admin', 'layouts.app'], function ($view) {
        //     if (Auth::check()) {
        //         $unreadNotifications = Auth::user()->unreadNotifications;
        //         $view->with('unreadNotifications', $unreadNotifications);
        //     }
        // });
    }
}
