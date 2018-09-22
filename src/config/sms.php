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
    'default_gateway' => 'debug',

    'kavenegar' => [
        'api_key' => '',
        'numbers' => [
            '20003000',
            '20003001',
            '20003002',
            '20003003',
        ],
    ],
    'mellipayamak' => [
        'api_key' => '',
        'numbers' => [
            '20003000',
            '20003001',
            '20003002',
            '20003003',
        ],
    ],

    'map_gateway_to_connector' => [
        'debug' => \Amiriun\SMS\Services\Connectors\DebugConnector::class,
        'kavenegar' => \Amiriun\SMS\Services\Connectors\KavenegarConnector::class,
    ],
];