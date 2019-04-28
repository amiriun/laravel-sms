<?php

namespace Amiriun\SMS\Repositories\Storage;


use Amiriun\SMS\Contracts\StorageInterface;
use Illuminate\Database\Query\Builder;

class MysqlStorage implements StorageInterface
{
    private $db;

    public function __construct()
    {
        $this->db = \DB::table(config('sms.logging.table_name'));
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function insert(array $data)
    {
        return $this->db->insert($data);
    }

    public function isMessageIdExist($messageId, $connectorName)
    {
        return $this->getLogByMessageId($messageId, $connectorName)->exists();
    }

    /**
     * @param $messageId
     * @param $connectorName
     * @param $date
     *
     * @return bool
     */
    public function setDeliveryForLog($messageId, $connectorName, $date)
    {
        return $this->getLogByMessageId($messageId, $connectorName)
            ->update(['delivered_at' => $date]);
    }

    /**
     * @param $messageId
     * @param $connectorName
     *
     * @return $this|Builder
     */
    private function getLogByMessageId($messageId, $connectorName)
    {
        return $this->db->where(function ($q) use ($messageId, $connectorName) {
            $q->where('message_id', $messageId);
            $q->where('connector', $connectorName);
        });
    }
}