<?php
namespace Amiriun\SMS\Services\Connectors;


use Amiriun\SMS\Contracts\SMSConnectorInterface;
use Amiriun\SMS\DataContracts\SendSMSDTO;

class DebugConnector implements SMSConnectorInterface
{

    public function send(SendSMSDTO $DTO)
    {
        \Log::info("Send SMS \n from: {$DTO->from} \n to: {$DTO->to} \n message: {$DTO->text}");
    }
}