<?php

namespace Amiriun\SMS\Services;


use Amiriun\SMS\Contracts\DriverInterface;
use Amiriun\SMS\DataContracts\DeliverSMSDTO;
use Amiriun\SMS\DataContracts\ReceiveSMSDTO;
use Amiriun\SMS\DataContracts\SendSMSDTO;
use Amiriun\SMS\DataContracts\SentSMSOutputDTO;

class SMSService
{
    private $connector;

    public function __construct(DriverInterface $connector)
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

    /**
     * @param SendSMSDTO $DTO
     *
     * @return SentSMSOutputDTO
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendInstant(SendSMSDTO $DTO)
    {
        return $this->connector->sendInstant($DTO);
    }

    /**
     * @param ReceiveSMSDTO $DTO
     *
     * @return void
     */
    public function receive(ReceiveSMSDTO $DTO)
    {
        $this->connector->receive($DTO);
    }

    public function deliver(DeliverSMSDTO $DTO)
    {
        $this->connector->deliver($DTO);
    }
}