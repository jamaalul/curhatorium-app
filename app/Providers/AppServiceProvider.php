<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $caPath = base_path('vendor/midtrans/midtrans-php/data/cacert.pem');
        if (file_exists($caPath)) {
            ini_set('curl.cainfo', $caPath);
            ini_set('openssl.cafile', $caPath);
        }

        if (app()->isLocal()) {
            \Illuminate\Support\Facades\Http::globalOptions([
                'verify' => false,
            ]);

            \Illuminate\Support\Facades\Http::globalMiddleware(function ($handler) {
                return function ($request, $options) use ($handler) {
                    $options['verify'] = false;

                    return $handler($request, $options);
                };
            });
        }

        User::observe(UserObserver::class);
    }
}
