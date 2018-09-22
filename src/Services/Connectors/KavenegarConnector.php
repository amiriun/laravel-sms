<?php

namespace Amiriun\SMS\Services\Connectors;


use Amiriun\SMS\Contracts\SMSConnectorInterface;
use Amiriun\SMS\DataContracts\SendSMSDTO;
use Amiriun\SMS\DataContracts\SentSMSOutputDTO;
use GuzzleHttp\Client;

class KavenegarConnector implements SMSConnectorInterface
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function send(SendSMSDTO $DTO)
    {
        $apiKey = config('sms.kavenegar.api_key');
        $response = $this->client->post(
            "https://api.kavenegar.com/v1/$apiKey/sms/send.json",
            [
                'form_params' => [
                    'receptor' => $DTO->to,
                    'message'  => $DTO->text,
                    'sender' => $this->getSenderNumber($DTO)
                ],
                'headers'     => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ]
        );

        return $this->getResponseDTO($response);
    }

    /**
     * @param $res
     *
     * @return mixed
     */
    private function getResponseDTO($res)
    {
        $responseArray = json_decode((string)$res->getBody());
        $outputDTO = new SentSMSOutputDTO();
        $outputDTO->status = $responseArray['entries'][0]['status'];
        $outputDTO->messageId = $responseArray['entries'][0]['messageid'];

        return $outputDTO;

    }

    /**
     * @param SendSMSDTO $DTO
     *
     * @return mixed
     */
    private function getSenderNumber(SendSMSDTO $DTO)
    {
        if(is_null($DTO->from)){
            return config('sms.kavenegar.numbers.0');
        }
        return $DTO->from;
    }
}