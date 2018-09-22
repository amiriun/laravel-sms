<?php

namespace Amiriun\SMS\Services;


use Amiriun\SMS\Contracts\SMSConnectorInterface;
use Amiriun\SMS\DataContracts\SendSMSDTO;
use Amiriun\SMS\DataContracts\SentSMSOutputDTO;

class SMSService
{
    private $connector;

    public function __construct(SMSConnectorInterface $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @param SendSMSDTO $DTO
     *
     * @return SentSMSOutputDTO
     */
    public function send(SendSMSDTO $DTO)
    {
        return $this->connector->send($DTO);
    }

    public function receive($mobile, $text)
    {

    }
}