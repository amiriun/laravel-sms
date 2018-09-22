<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Default SMS Gateway
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, sms messages should not be send to endusers.
    | for handle this issue you can use "debug" gateway to log messages instead of send to users.
    | supported: debug , kavenegar
    |
    */
    'default_gateway' => env('SMS_GATEWAY','debug'),

    'kavenegar' => [
        'api_key' => env('KAVENEGAR_API_KEY','YOUR_API_KEY'),
        'numbers' => [
            'YOUR_NUMBERS',
        ],
    ],
    'mellipayamak' => [
        'api_key' => env('MELLIPAYAMAK_API_KEY','YOUR_API_KEY'),
        'numbers' => [
            'YOUR_NUMBERS',
        ],
    ],

    'map_gateway_to_connector' => [
        'debug' => \Amiriun\SMS\Services\Connectors\DebugConnector::class,
        'kavenegar' => \Amiriun\SMS\Services\Connectors\KavenegarConnector::class,
    ],
];