<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL; // <-- TAMBAHKAN BARIS INI
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
        // --- TAMBAHKAN KODE INI ---
        // Paksa Laravel untuk selalu membuat URL HTTPS
        // Ini akan memperbaiki error "Mixed Content" (CSS/JS tidak ter-load) di Render
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
        // --- AKHIR TAMBAHAN KODE ---
    }
}