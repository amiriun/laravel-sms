<?php

namespace Amiriun\SMS\Services\Connectors;


use Amiriun\SMS\Contracts\SMSConnectorInterface;
use Amiriun\SMS\DataContracts\SendSMSDTO;
use Amiriun\SMS\DataContracts\SentSMSOutputDTO;
use GuzzleHttp\ClientInterface;

class KavenegarConnector implements SMSConnectorInterface
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
                    'message'  => $DTO->text,
                    'sender'   => $this->getSenderNumber($DTO->from)
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
     * @return SentSMSOutputDTO
     */
    private function getResponseDTO($res)
    {
        $responseArray = json_decode((string)$res->getBody());
        $outputDTO = new SentSMSOutputDTO();
        $outputDTO->status = $responseArray->entries[0]->status;
        $outputDTO->messageId = $responseArray->entries[0]->messageid;

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