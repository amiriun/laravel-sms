<?php

namespace Amiriun\SMS\Services\Drivers;


use Amiriun\SMS\Contracts\DriverInterface;
use Amiriun\SMS\DataContracts\DeliverSMSDTO;
use Amiriun\SMS\DataContracts\ReceiveSMSDTO;
use Amiriun\SMS\DataContracts\SendInstantDTO;
use Amiriun\SMS\DataContracts\SendSMSDTO;
use Amiriun\SMS\DataContracts\SentSMSOutputDTO;
use Amiriun\SMS\Repositories\StoreSMSDataRepository;
use GuzzleHttp\ClientInterface;

class SmsIrDriver extends AbstractDriver
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
        $clientRequest = $this->prepareRequest($DTO);
        $getResponseDTO = $this->prepareResponseDTO($clientRequest);
        $this->repository->storeSendSMSLog($getResponseDTO);

        return $getResponseDTO;
    }

    /**
     * @param SendInstantDTO $DTO
     *
     * @return SentSMSOutputDTO
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendInstant(SendInstantDTO $DTO)
    {
        // TODO: Implement sendInstant() method.
    }

    public function deliver(DeliverSMSDTO $DTO)
    {
        //
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
    private function prepareResponseDTO($res)
    {
        $responseArray = json_decode((string)$res->getBody());
        $outputDTO = new SentSMSOutputDTO();
        $outputDTO->status = $responseArray->ErrorCode;
        $outputDTO->messageId = $responseArray->MessageId;
        $outputDTO->connectorName = $this->getConnectorName();

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

    /**
     * @param SendSMSDTO $DTO
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function prepareRequest(SendSMSDTO $DTO)
    {
        return $this->client->request(
            'POST',
            "https://api.sms.ir/users/v1/Message/SendByMobileNumbers",
            [
                'form_params' => [
                    'Message'                  => $DTO->message,
                    'MobileNumbers'            => [$DTO->to],
                    'CanContinueInCaseOfError' => true,
                ],
                'headers'     => [
                    'Content-Type'          => 'application/json',
                    'x-sms-ir-secure-token' => $this->getToken(),
                ]
            ]
        );
    }
}