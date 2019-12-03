<?php

namespace Amiriun\SMS\Services\Drivers;


use Amiriun\SMS\Contracts\DriverInterface;
use Amiriun\SMS\DataContracts\DeliverSMSDTO;
use Amiriun\SMS\DataContracts\ReceiveSMSDTO;
use Amiriun\SMS\DataContracts\SendSMSDTO;
use Amiriun\SMS\DataContracts\SentSMSOutputDTO;
use Amiriun\SMS\Repositories\StoreSMSDataRepository;
use GuzzleHttp\ClientInterface;

class PayamResanDriver extends AbstractDriver
{
    private $client;

    public function __construct(StoreSMSDataRepository $repository)
    {
        $this->client = new \SoapClient('http://sms-webservice.ir/v1/v1.asmx?WSDL');
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
        $messageResult = $this->prepareRequest($DTO);
        $getResponseDTO = $this->prepareResponseDTO($messageResult->SendMessageResult->long, $DTO);
        $this->repository->storeSendSMSLog($getResponseDTO);

        return $getResponseDTO;
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
        if ($statusCode > 0) {
            return DriverState::SENT;
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
    private function prepareResponseDTO($resultCode, SendSMSDTO $DTO)
    {
        $outputDTO = new SentSMSOutputDTO();
        $outputDTO->status = $this->getSystemStatus($resultCode);
        $outputDTO->messageResult = $this->getSystemMessage($outputDTO->status);
        $outputDTO->senderNumber = $this->getSenderNumber($DTO->senderNumber);
        $outputDTO->to = $DTO->to;
        $outputDTO->messageId = $resultCode;
        $outputDTO->connectorName = $this->getConnectorName();

        return $outputDTO;
    }

    /**
     * @return array
     */
    private function getStatuseArray()
    {
        return [
            -1  => DriverState::AUTH_PROBLEM,
            -2  => DriverState::AUTH_PROBLEM,
            -3  => DriverState::FAILED,
            -4  => DriverState::FAILED,
            -5  => DriverState::FAILED,
            -6  => DriverState::FAILED,
            -7  => DriverState::FAILED,
            -8  => DriverState::FAILED,
            -9  => DriverState::FAILED,
            -10 => DriverState::FAILED,
            -11 => DriverState::FAILED,
            -12 => DriverState::AUTH_PROBLEM,
            -13 => DriverState::AUTH_PROBLEM,
            -14 => DriverState::AUTH_PROBLEM,
            -15 => DriverState::FAILED,
            -16 => DriverState::FAILED,
            -18 => DriverState::FAILED,
            -19 => DriverState::FAILED,
            -20 => DriverState::FAILED,
            -21 => DriverState::FAILED,
        ];
    }

    /**
     * @return array
     */
    private function getStatusMessageArray()
    {
        return [
            DriverState::AUTH_PROBLEM => 'مشکلی در احراز هویت شما وجود دارد',
            DriverState::FAILED       => 'مشکل در ارسال پیامک',
            DriverState::SENT         => 'ارسال شده',
        ];
    }

    /**
     * @param $DTO
     *
     * @return mixed
     * @throws \Exception
     */
    private function prepareRequest($DTO)
    {
        $parameters = $this->setParameters($DTO);
        $this->checkCredit($parameters);

        return $this->sendMessage($parameters);
    }
}