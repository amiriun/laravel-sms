<?php

namespace Amiriun\SMS\DataContracts;


class SendSMSDTO
{
    public $to;
    public $from;
    public $message;

    public function setTo($value)
    {
        $this->to = $value;

        return $this;
    }

    public function setFrom($value)
    {
        $this->from = $value;

        return $this;
    }

    public function setMessage($value)
    {
        $this->message = $value;

        return $this;
    }

}