<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Contract\Messaging;
use App\Services\FakeMessagingService; 

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment() !== 'production') {
            $this->app->singleton(Messaging::class, function ($app) {
                return new FakeMessagingService();
            });
        }
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
