<?php

use Hyperpay\SSO\Models\User;

return [
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'jwt',
            'provider' => 'users',
            'hash' => false,
        ]
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => User::class,
        ],
    ],

    'public' => 'file://./jwt/public.pem',
    'private' => 'file://./jwt/private.pem',
];