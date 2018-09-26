<?php
/**
 * Created by PhpStorm.
 * User: amir
 * Date: 9/26/18
 * Time: 10:05 PM
 */

namespace Amiriun\SMS\Repositories;


use Amiriun\SMS\DataContracts\ReceiveSMSDTO;
use Illuminate\Database\ConnectionInterface;

class StoreSMSDataRepository
{
    private $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function deliver()
    {

    }

    /**
     * @param ReceiveSMSDTO $DTO
     *
     * @throws \Exception
     * @return void
     */
    public function storeReceive(ReceiveSMSDTO $DTO)
    {
        $store = $this->connection
            ->table('sms_replies')
            ->insert(
                [
                    'message_id'    => $DTO->messageId,
                    'message'       => $DTO->message,
                    'sender_number' => $DTO->senderNumber,
                    'to'            => $DTO->to,
                    'connector'     => $DTO->connectorName,
                    'sent_at'       => $DTO->sentAt,
                ]
            );
        if(!$store){
            throw new \Exception("Error in store receive data.");
        }
    }

}