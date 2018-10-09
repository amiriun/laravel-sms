<?php

namespace Amiriun\SMS\Services\Connectors;


use Amiriun\SMS\DataContracts\DeliverSMSDTO;
use Amiriun\SMS\DataContracts\SendSMSDTO;
use Amiriun\SMS\DataContracts\SentSMSOutputDTO;
use Amiriun\SMS\Repositories\StoreSMSDataRepository;
use GuzzleHttp\ClientInterface;

class KavenegarConnector extends AbstractConnector
{
    private $client;

    public function __construct(ClientInterface $client, StoreSMSDataRepository $repository)
    {
        $this->client = $client;
        $this->repository = $repository;
    }

    /**
     * @param SendSMSDTO $DTO
     *
     * @return SentSMSOutputDTO
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(SendSMSDTO $DTO)
    {
        $response = $this->prepareSendRequest($DTO, config('sms.kavenegar.api_key'));
        $getResponseDTO = $this->prepareResponseDTO($response);
        $this->repository->storeSendSMSLog($getResponseDTO);

        return $getResponseDTO;
    }

    /**
     * @param DeliverSMSDTO $DTO
     *
     * @throws \Exception
     */
    public function deliver(DeliverSMSDTO $DTO)
    {
        $this->repository->deliver($DTO);
    }

    public function getSystemStatus($statusCode)
    {
        $statusArray = $this->getStatuseArray();

        return $statusArray[$statusCode];
    }

    public function getSystemMessage($systemStatus)
    {
        $messageArray = $this->getStatusMessageArray();

        return $messageArray[$systemStatus];
    }

    private function getStatuseArray()
    {
        return [
            1   => ConnectorState::QUEUED,
            2   => ConnectorState::SCHEDULED,
            4   => ConnectorState::SENT,
            5   => ConnectorState::SENT,
            6   => ConnectorState::FAILED,
            10  => ConnectorState::DELIVERED,
            11  => ConnectorState::UNDELIVERED,
            13  => ConnectorState::CANCELED,
            14  => ConnectorState::BLOCKED,
            100 => ConnectorState::INVALID,
        ];
    }

    private function getStatusMessageArray()
    {
        return [
            ConnectorState::QUEUED      => 'در صف ارسال قرار دارد.',
            ConnectorState::SCHEDULED   => 'زمان بندی شده (ارسال در تاریخ معین ).',
            ConnectorState::SENT        => 'ارسال شده به مخابرات.',
            ConnectorState::FAILED      => 'خطا در ارسال پیام که توسط سر شماره پیش می آید و به معنی عدم رسیدن پیامک می باشد ',
            ConnectorState::DELIVERED   => 'رسیده به گیرنده',
            ConnectorState::UNDELIVERED => 'نرسیده به گیرنده ،این وضعیت به دلایلی از جمله خاموش یا خارج از دسترس بودن گیرنده اتفاق می افتد',
            ConnectorState::CANCELED    => ' ارسال پیام از سمت کاربر لغو شده یا در ارسال آن مشکلی پیش آمده که هزینه آن به حساب برگشت داده میشود.',
            ConnectorState::BLOCKED     => 'بلاک شده است،عدم تمایل گیرنده به دریافت پیامک از خطوط تبلیغاتی که هزینه آن به حساب برگشت داده میشود',
            ConnectorState::INVALID     => 'شناسه پیامک نامعتبر است.( به این معنی که شناسه پیام در پایگاه داده کاوه نگار ثبت نشده است یا متعلق به شما نمی باشد)',
        ];
    }

    /**
     * @param $res
     *
     * @return SentSMSOutputDTO
     */
    private function prepareResponseDTO($res)
    {
        $responseArray = json_decode((string)$res->getBody());
        $outputDTO = new SentSMSOutputDTO();
        $outputDTO->status = $this->getSystemStatus($responseArray->entries[0]->status);
        $outputDTO->messageResult = $this->getSystemMessage($outputDTO->status);
        $outputDTO->messageId = $responseArray->entries[0]->messageid;
        $outputDTO->senderNumber = $responseArray->entries[0]->sender;
        $outputDTO->to = $responseArray->entries[0]->receptor;
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
            return config('sms.kavenegar.numbers.0');
        }

        return $senderNumber;
    }

    /**
     * @param SendSMSDTO $DTO
     * @param            $apiKey
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function prepareSendRequest(SendSMSDTO $DTO, $apiKey)
    {
        return $this->client->request(
            'POST',
            "https://api.kavenegar.com/v1/$apiKey/sms/send.json",
            [
                'form_params' => [
                    'receptor' => $DTO->to,
                    'message'  => $DTO->message,
                    'sender'   => $this->getSenderNumber($DTO->senderNumber)
                ],
                'headers'     => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ]
        );
    }
}