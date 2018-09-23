<?php

namespace Amiriun\SMS\Services\Connectors;


use Amiriun\SMS\Contracts\SMSConnectorInterface;
use Amiriun\SMS\DataContracts\SendSMSDTO;
use Amiriun\SMS\DataContracts\SentSMSOutputDTO;
use GuzzleHttp\ClientInterface;

class PayamResanConnector implements SMSConnectorInterface
{
    private $client;

    public function __construct()
    {
        $this->client =  app('PayamResanClient');
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

        return $this->getResponseDTO($messageResult->SendMessageResult->long);
    }

    /**
     * @param $res
     *
     * @return SentSMSOutputDTO
     */
    private function getResponseDTO($resultCode)
    {
        $outputDTO = new SentSMSOutputDTO();
        $outputDTO->status = 1;
        $outputDTO->messageId = $resultCode;

        return $outputDTO;
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
     * @param SendSMSDTO $DTO
     * @param            $parameters
     *
     * @return mixed
     */
    private function setParameters(SendSMSDTO $DTO)
    {
        $parameters['Username'] = config('sms.payamresan.username');
        $parameters['PassWord'] = config('sms.payamresan.password');
        $parameters['SenderNumber'] = $this->getSenderNumber($DTO->from);
        $parameters['RecipientNumbers'] = [$DTO->to];
        $parameters['MessageBodie'] = $DTO->message;
        $parameters['Type'] = 1;
        $parameters['AllowedDelay'] = 0;

        return $parameters;
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
}