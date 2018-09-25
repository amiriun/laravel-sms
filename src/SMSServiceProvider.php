<?php

namespace Amiriun\SMS;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\ServiceProvider;
use Amiriun\SMS\Contracts\SMSConnectorInterface;
use Amiriun\SMS\Services\Connectors\KavenegarConnector;

class SMSServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
            __DIR__.'/config/sms.php' => config_path('sms.php'),
        ]);
        $this->publishes([
            __DIR__.'/database/2018_09_24_135839_create_table_sms_replies.php' => database_path('migrations/2018_09_24_135839_create_table_sms_replies.php'),
        ]);

        $this->loadRoutesFrom(__DIR__.'/routes.php');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ClientInterface::class,Client::class);
        $this->app->bind(SMSConnectorInterface::class,$this->getConnectorInstance());
        $this->app->instance('PayamResanClient',new \SoapClient('http://sms-webservice.ir/v1/v1.asmx?WSDL'));
    }

    private function getConnectorInstance()
    {
        $defaultGateway = config('sms.default_gateway');
        $gatewaysArray = config('sms.map_gateway_to_connector');

        return $gatewaysArray[$defaultGateway];
    }
}
