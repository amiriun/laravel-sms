<?php
namespace Amiriun\SMS\Services\Connectors;


use Amiriun\SMS\Contracts\SMSConnectorInterface;
use Amiriun\SMS\DataContracts\SendSMSDTO;

class DebugConnector extends AbstractConnector implements SMSConnectorInterface
{

    public function send(SendSMSDTO $DTO)
    {
        \Log::info("Send SMS \n from: {$DTO->from} \n to: {$DTO->to} \n message: {$DTO->message}");
    }

    /**
     * @param int $statusCode
     *
     * @return string
     */
    public function getSystemStatus($statusCode)
    {
        // TODO: Implement getSystemStatus() method.
    }

    /**
     * @param string $systemStatus
     *
     * @return string
     */
    public function getSystemMessage($systemStatus)
    {
        // TODO: Implement getSystemMessage() method.
    }
}