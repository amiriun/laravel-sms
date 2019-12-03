<?php
namespace Amiriun\SMS\Services\Drivers;


use Amiriun\SMS\Contracts\DriverInterface;
use Amiriun\SMS\DataContracts\DeliverSMSDTO;
use Amiriun\SMS\DataContracts\ReceiveSMSDTO;
use Amiriun\SMS\DataContracts\SendInstantDTO;
use Amiriun\SMS\DataContracts\SendSMSDTO;
use Amiriun\SMS\DataContracts\SentSMSOutputDTO;
use Amiriun\SMS\Repositories\StoreSMSDataRepository;

class DebugDriver extends AbstractDriver
{

    public function __construct(StoreSMSDataRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param SendSMSDTO $DTO
     *
     * @return SentSMSOutputDTO
     * @throws \Exception
     */
    public function send(SendSMSDTO $DTO)
    {
        \Log::info("Send SMS \n from: {$DTO->senderNumber} \n to: {$DTO->to} \n message: {$DTO->message}");
        $getResponseDTO = $this->prepareResponseDTO($DTO);
        $this->repository->storeSendSMSLog($getResponseDTO);

        return $getResponseDTO;
    }

    /**
     * @param SendInstantDTO $DTO
     *
     * @return SentSMSOutputDTO
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendInstant(SendInstantDTO $DTO)
    {
        \Log::info("Send Instant SMS \n from: {$DTO->senderNumber} \n to: {$DTO->to} \n message: your defined template + code: {$DTO->message}");
        $getResponseDTO = $this->prepareResponseDTO($DTO);
        $this->repository->storeSendSMSLog($getResponseDTO);

        return $getResponseDTO;
    }

    public function deliver(DeliverSMSDTO $DTO)
    {
        //
    }

    /**
     * @param null $statusCode
     *
     * @return string
     */
    public function getSystemStatus($statusCode = null)
    {
        return DriverState::QUEUED;
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

    private function prepareResponseDTO(SendSMSDTO $DTO)
    {
        $outputDTO = new SentSMSOutputDTO();
        $outputDTO->status = $this->getSystemStatus();
        $outputDTO->messageResult = $this->getSystemMessage();
        $outputDTO->messageId = rand(1000000,9000000);
        $outputDTO->senderNumber = $this->getSenderNumber($DTO->senderNumber);
        $outputDTO->to = $DTO->to;
        $outputDTO->connectorName = $this->getConnectorName();

        return $outputDTO;
    }

    /**
     * @param SendSMSDTO $DTO
     *
     * @return mixed
     */
    private function getSenderNumber($senderNumber)
    {
        if (is_null($senderNumber)) {
            return config('sms.debug.numbers.0');
        }

        return $senderNumber;
    }
}