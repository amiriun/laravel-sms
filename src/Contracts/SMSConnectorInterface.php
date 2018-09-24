<?php
namespace Amiriun\SMS\Contracts;


use Amiriun\SMS\DataContracts\SendSMSDTO;
use Amiriun\SMS\DataContracts\SentSMSOutputDTO;

interface SMSConnectorInterface
{
    /**
     * @param SendSMSDTO $DTO
     *
     * @return SentSMSOutputDTO
     */
    public function send(SendSMSDTO $DTO);

    /**
     * @param int $statusCode
     *
     * @return string
     */
    public function getSystemStatus($statusCode);

    /**
     * @param string $systemStatus
     *
     * @return string
     */
    public function getSystemMessage($systemStatus);
}