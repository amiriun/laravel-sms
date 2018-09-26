<?php
namespace Amiriun\Sms\Events;


use Amiriun\SMS\DataContracts\ReceiveSMSDTO;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


class SMSWasReceived
{
    use SerializesModels;

    public $receiveSMSDTO;

    public function __construct(ReceiveSMSDTO $receiveSMSDTO)
    {
        $this->receiveSMSDTO = $receiveSMSDTO;
    }


    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
