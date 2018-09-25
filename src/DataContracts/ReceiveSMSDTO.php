<?php

namespace Amiriun\SMS\DataContracts;


class ReceiveSMSDTO
{
    public $messageId;
    public $message;
    public $senderNumber;
    public $to;
    public $sentAt;
}