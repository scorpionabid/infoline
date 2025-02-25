<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        // Error mesajlarını override et
        $this->loadTranslationsFrom(resource_path('lang/az'), 'messages');
        
        // Bootstrap pagination-i aktiv et
        Paginator::useBootstrap();
    }
}