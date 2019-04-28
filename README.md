# Iranian Laravel SMS Gateways ( integrate with laravel notification )


Laravel SMS include **The popular Iranian SMS gateways** library providing an easier way to send sms from any gateway you want or switch between them.


## Supported sms gateways:
- [Kavenegar](http://kavenegar.com/)
- [SMS.ir](http://sms.ir/)
- [Payam Resan](http://payam-resan.com/)
- [Melli Payamak](https://www.melipayamak.com/) (**soon**)
- **Debug** ( Dont send sms to end users , just log it to laravel.log )

## Requirements

- PHP >=5.4
- PHP SoapClient Extension (***just for payamresan gateway***)

## Prerequisites

- Laravel >=5.2

```
Give examples
```

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
Give the example
```

And repeat

```
until finished
```

End with an example of getting some data out of the system or using it for a little demo

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

