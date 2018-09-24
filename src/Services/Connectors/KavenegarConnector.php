<?php

namespace Amiriun\SMS\Services\Connectors;


use Amiriun\SMS\Contracts\SMSConnectorInterface;
use Amiriun\SMS\DataContracts\SendSMSDTO;
use Amiriun\SMS\DataContracts\SentSMSOutputDTO;
use GuzzleHttp\ClientInterface;

class KavenegarConnector extends AbstractConnector implements SMSConnectorInterface
{
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param SendSMSDTO $DTO
     *
     * @return SentSMSOutputDTO
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(SendSMSDTO $DTO)
    {
        $apiKey = config('sms.kavenegar.api_key');
        $response = $this->client->request(
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

        return $this->getResponseDTO($response);
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
            1   => self::QUEUED,
            2   => self::SCHEDULED,
            4   => self::SENT,
            5   => self::SENT,
            6   => self::FAILED,
            10  => self::DELIVERED,
            11  => self::UNDELIVERED,
            13  => self::CANCELED,
            14  => self::BLOCKED,
            100 => self::INVALID,
        ];
    }

    private function getStatusMessageArray()
    {
        return [
            self::QUEUED => 'در صف ارسال قرار دارد.',
            self::SCHEDULED => 'زمان بندی شده (ارسال در تاریخ معین ).',
            self::SENT => 'ارسال شده به مخابرات.',
            self::FAILED => 'خطا در ارسال پیام که توسط سر شماره پیش می آید و به معنی عدم رسیدن پیامک می باشد ',
            self::DELIVERED => 'رسیده به گیرنده',
            self::UNDELIVERED => 'نرسیده به گیرنده ،این وضعیت به دلایلی از جمله خاموش یا خارج از دسترس بودن گیرنده اتفاق می افتد',
            self::CANCELED => ' ارسال پیام از سمت کاربر لغو شده یا در ارسال آن مشکلی پیش آمده که هزینه آن به حساب برگشت داده میشود.',
            self::BLOCKED => 'بلاک شده است،عدم تمایل گیرنده به دریافت پیامک از خطوط تبلیغاتی که هزینه آن به حساب برگشت داده میشود',
            self::INVALID => 'شناسه پیامک نامعتبر است.( به این معنی که شناسه پیام در پایگاه داده کاوه نگار ثبت نشده است یا متعلق به شما نمی باشد)',
        ];
    }

    /**
     * @param $res
     *
     * @return SentSMSOutputDTO
     */
    private function getResponseDTO($res)
    {
        $responseArray = json_decode((string)$res->getBody());
        $outputDTO = new SentSMSOutputDTO();
        $outputDTO->status = $this->getSystemStatus($responseArray->entries[0]->status);
        $outputDTO->messageResult = $this->getSystemMessage($outputDTO->status);
        $outputDTO->messageId = $responseArray->entries[0]->messageid;
        $outputDTO->senderNumber = $responseArray->entries[0]->sender;
        $outputDTO->to = $responseArray->entries[0]->receptor;

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
}