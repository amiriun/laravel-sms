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

    'logging' => [
        'send_logs' => [
            'need_log' => true,
            'table_name' => 'sms_logs',
            'connection' => \Illuminate\Database\MySqlConnection::class,
        ],
        'receive_logs' => [
            'need_log' => true,
            'table_name' => 'sms_logs',
            'connection' => \Illuminate\Database\MySqlConnection::class,
        ],
    ],

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
    'debug' => [
        'numbers' => [
            '30003000300',
        ],
    ],

    'map_gateway_to_connector' => [
        \Amiriun\SMS\Services\Connectors\DebugConnector::class,
        \Amiriun\SMS\Services\Connectors\KavenegarConnector::class,
        \Amiriun\SMS\Services\Connectors\SmsIrConnector::class,
        \Amiriun\SMS\Services\Connectors\PayamResanConnector::class,
    ],

    'events' => [
        'after_receiving_sms' => \Amiriun\Sms\Events\SMSWasReceived::class,
        'after_delivering_sms' => \Amiriun\Sms\Events\SMSWasDelivered::class,
    ]
];