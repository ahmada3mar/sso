<?php

namespace Hyperpay\SSO;

use App\Guard\UserProvider;
use Hyperpay\SSO\JWTGuard;
use Illuminate\Support\ServiceProvider;

class SSOServiceProvider extends ServiceProvider
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

        $this->mergeConfigFrom(__DIR__ . '/config/auth.php', 'auth');
        $this->mergeConfigFrom(__DIR__ . '/config/sso-client.php', 'sso-client');
        $this->publishes([__DIR__ . '/config/sso-client.php' => config_path('sso-client.php')], 'config');


        $this->app['auth']->extend(
            'jwt',
            function ($app, $name, array $config) {
                // dd($app['auth']->createUserProvider($config['provider']));
                $guard = new JWTGuard(
                    $app['tymon.jwt'],
                    app(UserProvider::class),
                    $app['request']
                );
                $app->refresh('request', $guard, 'setRequest');
                return $guard;
            }
        );


    }
}
