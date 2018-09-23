<?php

namespace Amiriun\SMS\Services\Connectors;


use Amiriun\SMS\Contracts\SMSConnectorInterface;
use Amiriun\SMS\DataContracts\SendSMSDTO;
use Amiriun\SMS\DataContracts\SentSMSOutputDTO;
use GuzzleHttp\ClientInterface;

class PayamResanConnector implements SMSConnectorInterface
{
    /**
     * @param SendSMSDTO $DTO
     *
     * @return SentSMSOutputDTO
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(SendSMSDTO $DTO)
    {
        try {
            $client = new \SoapClient('http://sms-webservice.ir/v1/v1.asmx?WSDL');

            $parameters['Username'] = config('sms.payamresan.username');
            $parameters['PassWord'] = config('sms.payamresan.password');
            $parameters['SenderNumber'] = $this->getSenderNumber($DTO->from);
            $parameters['RecipientNumbers'] = [$DTO->to];
            $parameters['MessageBodie'] = $DTO->message;
            $parameters['Type'] = 1;
            $parameters['AllowedDelay'] = 0;

            $res = $client->GetCredit($parameters);
            echo $res->GeCreditResult;
            $res = $client->SendMessage($parameters);
            foreach ($res->SendMessageResult as $r)
                echo $r;
        } catch (\SoapFault $ex) {
            echo $ex->faultstring;
        }

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
        $outputDTO->status = $responseArray->ErrorCode;
        $outputDTO->messageId = $responseArray->MessageId;

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
}