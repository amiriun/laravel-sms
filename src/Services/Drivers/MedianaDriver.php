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
use IPPanel\Client;

class MedianaDriver extends AbstractDriver
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
        if (config('sms.default_gateway') != 'mediana') {
            throw new \Exception("Default SMS driver is mediana, but " . $DTO->senderNumber);
        }
        try {
            $client = new Client(config("sms.mediana.api_key"));
            $bulkId = $client->send(
                $DTO->senderNumber,
                [$DTO->to],
                $DTO->message
            );
            $result = $client->getMessage($bulkId);
            return $this->prepareResponseDTO($result, $DTO);
        } catch (\Exception $e) {
            event(new NotificationFailed($DTO, new Notification(), $this, [
                'error' => $e->getMessage(),
                'data' => serialize($DTO),
            ]));
        }
    }

    /**
     * @param SendSMSDTO $DTO
     *
     * @return SentSMSOutputDTO
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendInstant(SendSMSDTO $DTO)
    {
        if (config('sms.default_gateway') != 'mediana') {
            throw new \Exception("Default SMS driver is mediana, but " . $DTO->senderNumber);
        }
        try {
            $client = new Client(config("sms.mediana.api_key"));
            $bulkId = $client->sendPattern(
                $DTO->template,
                $DTO->senderNumber,
                $DTO->to,
                $DTO->message
            );
            $result = $client->getMessage($bulkId);
            return $this->prepareResponseDTO($result, $DTO);
        } catch (\Exception $e) {
            report($e);
            event(new NotificationFailed($DTO, new Notification(), $this, [
                'error' => $e->getMessage(),
                'data' => serialize($DTO),
            ]));
        }
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
    private function prepareResponseDTO($res, SendSMSDTO $DTO)
    {
        $outputDTO = new SentSMSOutputDTO();
        $outputDTO->status = $res->status;
        $outputDTO->messageResult = $res->message;
        $outputDTO->messageId = $res->bulkId;
        $outputDTO->senderNumber = $DTO->senderNumber;
        $outputDTO->to = $DTO->to;
        $outputDTO->connectorName = $this->getConnectorName();

        return $outputDTO;
    }
}
