<?php
namespace Amiriun\SMS\Contracts;


use Amiriun\SMS\DataContracts\SendSMSDTO;

interface SMSConnectorInterface
{
    public function send(SendSMSDTO $DTO);
}