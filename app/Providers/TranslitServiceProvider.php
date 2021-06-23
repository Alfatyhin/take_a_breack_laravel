<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class TranslitServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('TranslitText', 'App\Services\TranslitText');
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
