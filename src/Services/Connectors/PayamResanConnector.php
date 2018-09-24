<?php

namespace Amiriun\SMS\Services\Connectors;


use Amiriun\SMS\Contracts\SMSConnectorInterface;
use Amiriun\SMS\DataContracts\SendSMSDTO;
use Amiriun\SMS\DataContracts\SentSMSOutputDTO;
use GuzzleHttp\ClientInterface;

class PayamResanConnector extends AbstractConnector implements SMSConnectorInterface
{
    private $client;

    public function __construct()
    {
        $this->client = app('PayamResanClient');
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
        $parameters = $this->setParameters($DTO);
        $this->checkCredit($parameters);
        $messageResult = $this->sendMessage($parameters);

        return $this->getResponseDTO($messageResult->SendMessageResult->long, $DTO);
    }

    /**
     * @param int $statusCode
     *
     * @return string
     */
    public function getSystemStatus($statusCode)
    {
        if ($statusCode > 0) {
            return self::SENT;
        }
        $statusArray = $this->getStatuseArray();

        return $statusArray[$statusCode];
    }

    /**
     * @param string $systemStatus
     *
     * @return string
     */
    public function getSystemMessage($systemStatus)
    {
        $messagesArray = $this->getStatusMessageArray();

        return $messagesArray[$systemStatus];
    }

    /**
     * @param SendSMSDTO $DTO
     * @param            $parameters
     *
     * @return mixed
     */
    private function setParameters(SendSMSDTO $DTO)
    {
        $parameters['Username'] = config('sms.payamresan.username');
        $parameters['PassWord'] = config('sms.payamresan.password');
        $parameters['SenderNumber'] = $this->getSenderNumber($DTO->senderNumber);
        $parameters['RecipientNumbers'] = [$DTO->to];
        $parameters['MessageBodie'] = $DTO->message;
        $parameters['Type'] = 1;
        $parameters['AllowedDelay'] = 0;

        return $parameters;
    }

    /**
     * @param string $senderNumber
     *
     * @return string
     */
    private function getSenderNumber($senderNumber)
    {
        if (is_null($senderNumber)) {
            return config('sms.payamresan.numbers.0');
        }

        return $senderNumber;
    }

    /**
     * @param $parameters
     *
     * @return mixed
     * @throws \Exception
     */
    private function checkCredit($parameters)
    {
        $response = $this->client->GeCredit($parameters);
        if ($response->GeCreditResult == '-1') {
            throw new \Exception('Your credential username or password is invalid.');
        }
        if ($response->GeCreditResult < 0) {
            throw new \Exception('You have not enough credit.');
        }

        return $response;
    }

    /**
     * @param $parameters
     *
     * @return mixed
     * @throws \Exception
     */
    private function sendMessage($parameters)
    {
        $response = $this->client->SendMessage($parameters);
        if ($response->SendMessageResult->long < 0) {
            throw new \Exception('The message can not be send.');
        }

        return $response;
    }

    /**
     * @param            $resultCode
     * @param SendSMSDTO $DTO
     *
     * @return SentSMSOutputDTO
     */
    private function getResponseDTO($resultCode, SendSMSDTO $DTO)
    {
        $outputDTO = new SentSMSOutputDTO();
        $outputDTO->status = $this->getSystemStatus($resultCode);
        $outputDTO->messageResult = $this->getSystemMessage($outputDTO->status);
        $outputDTO->senderNumber = $this->getSenderNumber($DTO->senderNumber);
        $outputDTO->to = $DTO->to;
        $outputDTO->messageId = $resultCode;

        return $outputDTO;
    }

    /**
     * @return array
     */
    private function getStatuseArray()
    {
        return [
            -1  => self::AUTH_PROBLEM,
            -2  => self::AUTH_PROBLEM,
            -3  => self::FAILED,
            -4  => self::FAILED,
            -5  => self::FAILED,
            -6  => self::FAILED,
            -7  => self::FAILED,
            -8  => self::FAILED,
            -9  => self::FAILED,
            -10 => self::FAILED,
            -11 => self::FAILED,
            -12 => self::AUTH_PROBLEM,
            -13 => self::AUTH_PROBLEM,
            -14 => self::AUTH_PROBLEM,
            -15 => self::FAILED,
            -16 => self::FAILED,
            -18 => self::FAILED,
            -19 => self::FAILED,
            -20 => self::FAILED,
            -21 => self::FAILED,
        ];
    }

    /**
     * @return array
     */
    private function getStatusMessageArray()
    {
        return [
            self::AUTH_PROBLEM => 'مشکلی در احراز هویت شما وجود دارد',
            self::FAILED       => 'مشکل در ارسال پیامک',
            self::SENT         => 'ارسال شده',
        ];
    }
}