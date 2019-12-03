<?php
namespace Amiriun\SMS\Contracts;


use Amiriun\SMS\DataContracts\DeliverSMSDTO;
use Amiriun\SMS\DataContracts\ReceiveSMSDTO;
use Amiriun\SMS\DataContracts\SendSMSDTO;
use Amiriun\SMS\DataContracts\SentSMSOutputDTO;

/**
 * Interface SMSConnectorInterface
 * @package Amiriun\SMS\Contracts
 *
 */
interface DriverInterface
{
    /**
     * @param SendSMSDTO $DTO
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return SentSMSOutputDTO
     */
    public function send(SendSMSDTO $DTO);

    /**
     * @param SendSMSDTO $DTO
     *
     * @return SentSMSOutputDTO
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendInstant(SendSMSDTO $DTO);

    /**
     * @param ReceiveSMSDTO $DTO
     *
     * @return void
     */
    public function receive(ReceiveSMSDTO $DTO);

    /**
     * @return string
     */
    public function getConnectorName();

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

    public function deliver(DeliverSMSDTO $DTO);
}