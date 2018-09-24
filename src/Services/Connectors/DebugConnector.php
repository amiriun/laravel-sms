<?php
namespace Amiriun\SMS\Services\Connectors;


use Amiriun\SMS\Contracts\SMSConnectorInterface;
use Amiriun\SMS\DataContracts\SendSMSDTO;
use Amiriun\SMS\DataContracts\SentSMSOutputDTO;

class DebugConnector extends AbstractConnector implements SMSConnectorInterface
{

    public function send(SendSMSDTO $DTO)
    {
        \Log::info("Send SMS \n from: {$DTO->senderNumber} \n to: {$DTO->to} \n message: {$DTO->message}");

        return $this->getResponseDTO($DTO);
    }

    /**
     * @param null $statusCode
     *
     * @return string
     */
    public function getSystemStatus($statusCode = null)
    {
        return self::QUEUED;
    }

    /**
     * @param null $statusCode
     *
     * @return string
     */
    public function getSystemMessage($statusCode = null)
    {
        return 'ارسال شده';
    }

    private function getResponseDTO(SendSMSDTO $DTO)
    {
        $outputDTO = new SentSMSOutputDTO();
        $outputDTO->status = $this->getSystemStatus();
        $outputDTO->messageResult = $this->getSystemMessage();
        $outputDTO->messageId = rand(1000000,9000000);
        $outputDTO->senderNumber = $DTO->senderNumber;
        $outputDTO->to = $DTO->to;

        return $outputDTO;
    }
}