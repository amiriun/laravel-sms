<?php

namespace Amiriun\SMS\Repositories;


use Amiriun\SMS\Contracts\StorageInterface;
use Amiriun\SMS\DataContracts\DeliverSMSDTO;
use Amiriun\SMS\DataContracts\ReceiveSMSDTO;
use Amiriun\SMS\DataContracts\SentSMSOutputDTO;
use Amiriun\SMS\Exceptions\DeliverSMSException;
use Carbon\Carbon;

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
        if (!config('sms.logging.send_logs.need_log')) {
            return;
        }
        $store = $this->storage
            ->insert(
                [
                    'message_id'    => $DTO->messageId,
                    'message'       => $DTO->messageResult,
                    'sender_number' => $DTO->senderNumber,
                    'to'            => $DTO->to,
                    'delivered_at'  => null,
                    'sent_at'       => Carbon::now(),
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
        if (!config('sms.logging.receive_logs.need_log')) {
            return;
        }
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
        if (!config('sms.logging.send_logs.need_log')) {
            return;
        }
        if (!$this->storage->isMessageIdExist($DTO->messageId, $DTO->connectorName)) {
            throw new DeliverSMSException("Record( messageId: {$DTO->messageId} ) for delivering is not exist.");
        }
        $this->storage->setDeliveryForLog($DTO->messageId, $DTO->connectorName, date('Y-m-d H:i:s'));
    }

}