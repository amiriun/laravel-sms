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
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SMSConnectorInterface::class,KavenegarConnector::class);
    }
}
