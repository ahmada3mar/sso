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
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        $this->mergeConfigFrom(__DIR__ . '/config/connect-in.php', 'connect-in');
        if (!config('database.connections.mongodb'))
            $this->mergeConfigFrom(__DIR__ . '/config/database.php', 'database.connections');


        $this->publishes([__DIR__ . '/dist/Controllers' => app_path('Http/Controllers') ], 'controllers');
        $this->publishes([__DIR__ . '/config/connect-in.php' => config_path('connect-in.php')], 'config');
        $this->publishes([__DIR__ . '/Database/migrations' => $this->app->databasePath() . "/migrations/"], 'migrations');


        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');
    }
}
