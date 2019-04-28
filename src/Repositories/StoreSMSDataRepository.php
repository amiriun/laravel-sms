<?php

namespace Amiriun\SMS\Repositories;


use Amiriun\SMS\Contracts\StorageInterface;
use Amiriun\SMS\DataContracts\DeliverSMSDTO;
use Amiriun\SMS\DataContracts\ReceiveSMSDTO;
use Amiriun\SMS\DataContracts\SentSMSOutputDTO;
use Amiriun\SMS\Exceptions\DeliverSMSException;

class StoreSMSDataRepository
{
    private $storage;

    public function __construct(StorageInterface $connection)
    {
        $this->storage = $connection;
    }

    /**
     * @param SentSMSOutputDTO $DTO
     *
     * @throws \Exception
     */
    public function storeSendSMSLog(SentSMSOutputDTO $DTO)
    {
        $store = $this->storage
            ->insert(
                [
                    'message_id'    => $DTO->messageId,
                    'message'       => $DTO->messageResult,
                    'sender_number' => $DTO->senderNumber,
                    'to'            => $DTO->to,
                    'delivered_at'  => null,
                    'connector'     => $DTO->connectorName,
                    'status'        => $DTO->status,
                    'type'          => 'send',
                ]
            );
        if (!$store) {
            throw new \Exception("Error in store receive data.");
        }
    }

    /**
     * @param ReceiveSMSDTO $DTO
     *
     * @throws \Exception
     * @return void
     */
    public function storeReceiveSMSLog(ReceiveSMSDTO $DTO)
    {
        $store = $this->storage
            ->insert(
                [
                    'message_id'    => $DTO->messageId,
                    'message'       => $DTO->message,
                    'sender_number' => $DTO->senderNumber,
                    'to'            => $DTO->to,
                    'connector'     => $DTO->connectorName,
                    'sent_at'       => $DTO->sentAt,
                    'type'          => 'receive',
                ]
            );
        if (!$store) {
            throw new \Exception("Error in store receive data.");
        }
    }

    public function deliver(DeliverSMSDTO $DTO)
    {

        $getRecord = $this->storage->table(config('sms.logging.send_logs.table_name'))
            ->where(function ($q) use ($DTO) {
                $q->where('message_id', $DTO->messageId);
                $q->where('connector', $DTO->connectorName);
            });
        if (!$getRecord->exists()) {
            throw new DeliverSMSException("Record( messageId: {$DTO->messageId} ) for delivering is not exist.");
        }
        $getRecord->update(['delivered_at' => date('Y-m-d H:i:s')]);
    }

}