<?php

namespace App\Providers;

use App\LocalConfig;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton(LocalConfig::class, fn() => (new LocalConfig())->load());
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
