<?php

namespace Amiriun\SMS\Services\Drivers;


use Amiriun\SMS\DataContracts\DeliverSMSDTO;
use Amiriun\SMS\DataContracts\SendSMSDTO;
use Amiriun\SMS\DataContracts\SentSMSOutputDTO;
use Amiriun\SMS\Exceptions\DeliverSMSException;
use Amiriun\SMS\Repositories\StoreSMSDataRepository;
use GuzzleHttp\ClientInterface;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Events\NotificationFailed;


class KavenegarDriver extends AbstractDriver
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
        if (config('sms.default_gateway') != 'kavenegar') {
            throw new \Exception("Default SMS driver is kavenegar, but ".$DTO->senderNumber);
        }
        try{
            $response = $this->prepareSendRequest($DTO, config('sms.kavenegar.api_key'));
            $getResponseDTO = $this->prepareResponseDTO($response);

        }catch (\Exception $e){
            event(new NotificationFailed($DTO, new Notification(), $this, [
                'error' => $e->getMessage(),
                'data' => serialize($DTO),
            ]));
        }
        $this->repository->storeSendSMSLog($getResponseDTO);

        return $getResponseDTO;
    }

    /**
     * @param DeliverSMSDTO $DTO
     *
     * @throws DeliverSMSException
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
            1   => DriverState::QUEUED,
            2   => DriverState::SCHEDULED,
            4   => DriverState::SENT,
            5   => DriverState::SENT,
            6   => DriverState::FAILED,
            10  => DriverState::DELIVERED,
            11  => DriverState::UNDELIVERED,
            13  => DriverState::CANCELED,
            14  => DriverState::BLOCKED,
            100 => DriverState::INVALID,
        ];
    }

    private function getStatusMessageArray()
    {
        return [
            DriverState::QUEUED      => 'در صف ارسال قرار دارد.',
            DriverState::SCHEDULED   => 'زمان بندی شده (ارسال در تاریخ معین ).',
            DriverState::SENT        => 'ارسال شده به مخابرات.',
            DriverState::FAILED      => 'خطا در ارسال پیام که توسط سر شماره پیش می آید و به معنی عدم رسیدن پیامک می باشد ',
            DriverState::DELIVERED   => 'رسیده به گیرنده',
            DriverState::UNDELIVERED => 'نرسیده به گیرنده ،این وضعیت به دلایلی از جمله خاموش یا خارج از دسترس بودن گیرنده اتفاق می افتد',
            DriverState::CANCELED    => ' ارسال پیام از سمت کاربر لغو شده یا در ارسال آن مشکلی پیش آمده که هزینه آن به حساب برگشت داده میشود.',
            DriverState::BLOCKED     => 'بلاک شده است،عدم تمایل گیرنده به دریافت پیامک از خطوط تبلیغاتی که هزینه آن به حساب برگشت داده میشود',
            DriverState::INVALID     => 'شناسه پیامک نامعتبر است.( به این معنی که شناسه پیام در پایگاه داده کاوه نگار ثبت نشده است یا متعلق به شما نمی باشد)',
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
        $outputDTO->messageResult = $responseArray->entries[0]->message;
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