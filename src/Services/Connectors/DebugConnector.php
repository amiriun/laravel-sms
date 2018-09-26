<?php
namespace Amiriun\SMS\Services\Connectors;


use Amiriun\SMS\Contracts\SMSConnectorInterface;
use Amiriun\SMS\DataContracts\ReceiveSMSDTO;
use Amiriun\SMS\DataContracts\SendSMSDTO;
use Amiriun\SMS\DataContracts\SentSMSOutputDTO;
use Amiriun\SMS\Repositories\StoreSMSDataRepository;

class DebugConnector extends AbstractConnector
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
     * @param null $statusCode
     *
     * @return string
     */
    public function getSystemStatus($statusCode = null)
    {
        return ConnectorState::QUEUED;
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
        $outputDTO->senderNumber = $DTO->senderNumber;
        $outputDTO->to = $DTO->to;
        $outputDTO->connectorName = $this->getConnectorName();

        return $outputDTO;
    }
}