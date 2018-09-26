<?php
namespace Amiriun\SMS\Services\Connectors;


use Amiriun\SMS\Contracts\SMSConnectorInterface;
use Amiriun\SMS\DataContracts\ReceiveSMSDTO;

abstract class AbstractConnector implements SMSConnectorInterface
{

    /**
     * @param ReceiveSMSDTO $DTO
     *
     * @throws \Exception
     *
     * @return void
     */
    public function receive(ReceiveSMSDTO $DTO)
    {
        $insertRecord = \DB::table('sms_replies')->insert(
            [
                'message_id' => $DTO->messageId,
                'message' => $DTO->message,
                'sender_number' => $DTO->senderNumber,
                'to' => $DTO->to,
                'connector' => $DTO->connectorName,
                'sent_at' => $DTO->sentAt,
            ]
        );

        if (!$insertRecord) {
            throw new \Exception("Record cannot be inserted.");
        }
    }
}