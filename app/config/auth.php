<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'customer'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    */

    'guards' => [
        'customer' => [
            'driver' => 'token',
            'provider' => 'customers',
        ],
        'admin' => [
            'driver' => 'token',
            'provider' => 'admins',
        ],
        'user' => [
            'driver' => 'token',
            'provider' => 'users',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    */

    'providers' => [
        'customers' => [
            'driver' => 'eloquent',
            'model' => App\Model\Customer\Customer::class,
        ],
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Model\Admin\Admin::class,
        ],
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Model\User::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    */

    'passwords' => [
        //
    ],

];