<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Contract\Messaging;
use App\Services\FakeMessagingService;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;
use App\Policies\ActivityPolicy;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // if ($this->app->environment() !== 'production') {
        //     $this->app->singleton(Messaging::class, function ($app) {
        //         return new FakeMessagingService();
        //     });
        // }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Activity::class, ActivityPolicy::class);
        // 2. Daftarkan event listener di sini
        // Event::listen(
        //     Registered::class,
        //     SendQueuedEmailVerificationNotification::class
        // );
        if (config('app.env') !== 'local' || request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }
    }
}
