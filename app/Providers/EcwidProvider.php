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

        $this->app->singleton('App\Services\EcwidService', function ($app) {
            return new EcwidService();
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
