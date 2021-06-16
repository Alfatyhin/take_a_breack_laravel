<?php

namespace App\Providers;

use App\Services\EcwidService;
use Illuminate\Support\ServiceProvider;

class EcwidProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(EcwidService::class, function ($app) {
            return new EcwidService(config('EcwidService'));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
