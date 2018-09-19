<?php

namespace Amiriun\SMS\Services;


use Amiriun\SMS\Contracts\SMSConnectorInterface;
use Amiriun\SMS\DataContracts\SendSMSDTO;

class SMSService
{
    private $connector;

    public function __construct(SMSConnectorInterface $connector)
    {
        $this->connector = $connector;
    }

    public function send(SendSMSDTO $DTO)
    {

    }

    public function receive($mobile, $text)
    {

    }
}