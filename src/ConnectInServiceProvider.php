<?php

namespace Hyperpay\ConnectIn;

use Illuminate\Support\ServiceProvider;

class ConnectInServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {


        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
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
