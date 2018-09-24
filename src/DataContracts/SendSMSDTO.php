<?php

namespace Amiriun\SMS\DataContracts;


class SendSMSDTO
{
    public $to;
    public $senderNumber;
    public $message;

    public function setTo($value)
    {
        $this->to = $value;

        return $this;
    }

    public function setSenderNumber($value)
    {
        $this->senderNumber = $value;

        return $this;
    }

    public function setMessage($value)
    {
        $this->message = $value;

        return $this;
    }

}