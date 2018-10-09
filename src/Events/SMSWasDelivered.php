<?php
namespace Amiriun\Sms\Events;


use Amiriun\SMS\DataContracts\DeliverSMSDTO;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


class SMSWasDelivered
{
    use SerializesModels;

    public $deliverSMSDTO;

    public function __construct(DeliverSMSDTO $deliverSMSDTO)
    {
        $this->deliverSMSDTO = $deliverSMSDTO;
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
