<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Default SMS Gateway
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, sms messages should not be send to endusers.
    | for handle this issue you can use "debug" gateway to log messages instead of send to users.
    | supported: debug , kavenegar, sms_ir, payamresan
    |
    */
    'default_gateway' => env('SMS_GATEWAY', 'debug'),

    'kavenegar'    => [
        'api_key' => env('KAVENEGAR_API_KEY', 'YOUR_API_KEY'),
        'numbers' => [
            'YOUR_NUMBERS',
        ],
    ],
    'mellipayamak' => [
        'api_key' => env('MELLIPAYAMAK_API_KEY', 'YOUR_API_KEY'),
        'numbers' => [
            'YOUR_NUMBERS',
        ],
    ],
    'sms_ir'       => [
        'api_key'    => env('SMSIR_API_KEY', 'YOUR_API_KEY'),
        'secret_key' => env('SMSIR_SECRET_KEY', 'YOUR_SECRET_KEY'),
        'numbers'    => [
            'YOUR_NUMBERS',
        ],
    ],
    'payamresan'   => [
        'username' => env('PAYAMRESAN_USERNAME', 'YOUR_USERNAME'),
        'password' => env('PAYAMRESAN_PASSWORD', 'YOUR_PASSWORD'),
        'numbers'  => [
            'YOUR_NUMBERS',
        ],
    ],

    'map_gateway_to_connector' => [
        'debug'      => \Amiriun\SMS\Services\Connectors\DebugConnector::class,
        'kavenegar'  => \Amiriun\SMS\Services\Connectors\KavenegarConnector::class,
        'sms_ir'     => \Amiriun\SMS\Services\Connectors\SmsIrConnector::class,
        'payamresan' => \Amiriun\SMS\Services\Connectors\PayamResanConnector::class,
    ],

    'events' => [
        'after_receiving_sms' => \Amiriun\Sms\Events\SMSWasReceived::class,
    ]
];