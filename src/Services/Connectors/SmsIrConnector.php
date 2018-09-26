<?php

namespace Amiriun\SMS\Services\Connectors;


use Amiriun\SMS\Contracts\SMSConnectorInterface;
use Amiriun\SMS\DataContracts\ReceiveSMSDTO;
use Amiriun\SMS\DataContracts\SendSMSDTO;
use Amiriun\SMS\DataContracts\SentSMSOutputDTO;
use Amiriun\SMS\Repositories\StoreSMSDataRepository;
use GuzzleHttp\ClientInterface;

class SmsIrConnector extends AbstractConnector
{
    private $client;

    public function __construct(ClientInterface $client,StoreSMSDataRepository $repository)
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
        $response = $this->client->request(
            'POST',
            "https://api.sms.ir/users/v1/Message/SendByMobileNumbers",
            [
                'form_params' => [
                    'Message'  => $DTO->message,
                    'MobileNumbers' => [$DTO->to],
                    'CanContinueInCaseOfError'   => true,
                ],
                'headers'     => [
                    'Content-Type' => 'application/json',
                    'x-sms-ir-secure-token' => $this->getToken(),
                ]
            ]
        );

        return $this->getResponseDTO($response);
    }

    /**
     * @param int $statusCode
     *
     * @return string
     */
    public function getSystemStatus($statusCode)
    {
        // TODO: Implement getSystemStatus() method.
    }

    /**
     * @param string $systemStatus
     *
     * @return string
     */
    public function getSystemMessage($systemStatus)
    {
        // TODO: Implement getSystemMessage() method.
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
        $outputDTO->status = $responseArray->ErrorCode;
        $outputDTO->messageId = $responseArray->MessageId;

        return $outputDTO;

    }

    /**
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getToken()
    {
        $tokenResponse = $this->client->request(
            'POST',
            "https://api.sms.ir/users/v1/Token/GetToken",
            [
                'form_params' => [
                    'UserApiKey' => config('sms.sms_ir.api_key'),
                    'SecretKey'  => config('sms.sms_ir.secret_key'),
                ],
                'headers'     => [
                    'Content-Type' => 'application/json',
                ]
            ]
        );
        $responseObject = json_decode((string)$tokenResponse->getBody());
        if(!$responseObject->IsSuccessful){
            throw new \Exception('Poblem on Sms.ir Response: '.$responseObject->Message);
        }

        return $responseObject->TokenKey;


    }
}