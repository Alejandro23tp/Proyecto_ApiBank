<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SriService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SriService::class, function ($app) {
            return new SriService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
