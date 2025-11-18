<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL; // <-- PASTIKAN 'use URL' ADA
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
    // --- NONAKTIFKAN KODE INI UNTUK SEMENTARA ---
    // if (env('APP_ENV') === 'production') {
    //     URL::forceScheme('https');
    // }
    // --- AKHIR DARI KODE TAMBAHAN ---
}