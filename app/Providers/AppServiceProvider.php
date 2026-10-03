<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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
        Paginator::useBootstrapFive();

        // In production every generated URL (assets, forms, redirects, links in
        // emails) uses https, even when a request reaches PHP over plain http.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
