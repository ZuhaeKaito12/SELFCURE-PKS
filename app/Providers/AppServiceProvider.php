<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        Paginator::useBootstrapFive();

        // Every new password (registration, reset, profile, admin-created
        // accounts) needs 8+ characters with upper and lower case and a number.
        Password::defaults(fn () => Password::min(8)->mixedCase()->numbers());

        // In production every generated URL (assets, forms, redirects, links in
        // emails) uses https, even when a request reaches PHP over plain http.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
