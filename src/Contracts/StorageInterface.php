<?php
namespace Amiriun\SMS\Contracts;


interface StorageInterface
{
    public function insert(array $data);

    public function isMessageIdExist($messageId, $connectorName);

    public function setDeliveryForLog($messageId, $connectorName, $date);
}