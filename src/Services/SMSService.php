<?php

namespace Amiriun\SMS\Services;


use Amiriun\SMS\Contracts\SMSConnectorInterface;

class SMSService
{
    private $connector;

    public function __construct(SMSConnectorInterface $connector)
    {
        $this->connector = $connector;
    }

    public function send($mobile, $text)
    {

    }

    public function receive($mobile, $text)
    {

    }
}