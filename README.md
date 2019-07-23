# Iranian Laravel SMS Gateways ( integrate with laravel notification )


Laravel SMS include **The popular Iranian SMS gateways** library providing an easier way to send sms from any gateway you want or switch between them.


## Supported sms gateways:
- [Kavenegar](http://kavenegar.com/)
- [SMS.ir](http://sms.ir/)
- [Payam Resan](http://payam-resan.com/)
- [Melli Payamak](https://www.melipayamak.com/) (**soon**)
- **Debug** ( Won't send sms to end users , just log it to laravel.log )

## Requirements

- PHP >=5.4
- PHP SoapClient Extension (***just for payamresan gateway***)

## Prerequisites

- Laravel >=5.2



### Installing

#### Composer Install (for Laravel 5+/Lumen 5)

```shell
composer require amiriun/sms:dev-master
```



#### Add the package service provider to config/app.php
```php
'providers' => [
	\Amiriun\SMS\SMSServiceProvider::class,
];
```


Then publish the configurations by:
```bash
php artisan vendor:publish
```
(Now you can specify your sms gateways identifiers and **select default gateway** from config/sms.php file) 


```
return [
     // debug , kavenegar, sms_ir, payamresan
    'default_gateway' => env('SMS_GATEWAY', 'debug'),
    ...
]
```

**Note:** If you choose "debug" driver as a default driver, your messages will not be sent and just logged in laravel.log file.

### How to send SMS:

```
$data = new \Amiriun\SMS\DataContracts\SendSMSDTO();
$data->setSenderNumber('300024444'); // also this can be set as default in config/sms.php
$data->setMessage("Hello, this is a test");
$data->setTo('09123000000');

$getResponse = app('\Amiriun\SMS\Services\SMSService')->send($data);

// expected output is instance of \Amiriun\SMS\DataContracts\SentSMSOutputDTO()
//      $getResponse->messageId // get response of message id
//      $getResponse->status // get response state of provider
//      $getResponse->to // get recipient mobile number
//      $getResponse->senderNumber // get provider sender number
//      $getResponse->messageResult // get any other results from provider
//      $getResponse->connectorName // get provider name, such as: Kavenegar, MelliPayamak, etc.

```

In the above example your message will be sent with the default provider(driver) which you choosed in config/sms.php file but
#####you can change provider manually as you can see in the below example:

```
...
...

// $driver = app(\Amiriun\SMS\Services\Drivers\PayamResanDriver::class);
// $driver = app(\Amiriun\SMS\Services\Drivers\SmsIrDriver::class);
if(config('app.debug')){
    $driver = app(\Amiriun\SMS\Services\Drivers\DebugDriver::class);
}else{
    $driver = app(\Amiriun\SMS\Services\Drivers\KavenegarDriver::class);
}
$getResponse = new \Amiriun\SMS\Services\SMSService($kavenegarDriver);
$getResponse = $getResponse->send($data);

...
...

```


### How to receive SMS:

This package obtain route uri which hooked by providers to update your application about received and delivered messages
this routes are listed below:
```
YOUR_SITE.COM/amiriun-sms/kavenegar/receive
YOUR_SITE.COM/amiriun-sms/kavenegar/deliver
```
You may define that urls with your own website domain in your providers (till now just kavenegar supported)

When you received new message from provider or you have new delivery update, you may like to do some action, in these cases you can create two event with artisan command:
```
php artisan make:event SMSWasDelivered
php artisan make:event SMSWasReceived
```
and after that you can define the class in you config/sms.php like this:
```
	...
	
    'events' => [
        'after_receiving_sms' => \App\Events\SMSWasReceived::class,
        'after_delivering_sms' => \App\Events\SMSWasDelivered::class,
    ],
    ...
    
```
and you can define your actions when you getting new hooks from provider

## Extending

You can change the log storage by implementing the **Amiriun\SMS\Contracts\StorageInterface** interface and change the storage class where is in the config/sms.php

## Contributing

Please read [CONTRIBUTING.md](https://gist.github.com/PurpleBooth/b24679402957c63ec426) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/your/project/tags). 

## Authors

* **Amir Alian** - *Initial work* - [Amiriun](https://github.com/amiriun)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

