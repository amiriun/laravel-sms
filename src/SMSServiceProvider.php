<?php

namespace Amiriun\SMS;

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
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SMSConnectorInterface::class,$this->getConnectorInstance());
    }

    private function getConnectorInstance()
    {
        $defaultGateway = config('sms.default_gateway');
        $gatewaysArray = config('sms.map_gateway_to_connector');

        return $gatewaysArray[$defaultGateway];
    }
}
