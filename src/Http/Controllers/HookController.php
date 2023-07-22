<?php
namespace Amiriun\SMS\Http\Controllers;


use Amiriun\SMS\DataContracts\DeliverSMSDTO;
use Amiriun\SMS\DataContracts\ReceiveSMSDTO;
use Amiriun\SMS\Services\SMSService;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class HookController extends Controller
{
    private $service;

    public function __construct(SMSService $SMSService)
    {
        $this->service = $SMSService;
    }

    public function receiveKavenegar(){
        $DTO = new ReceiveSMSDTO();
        $DTO->connectorName = 'kavenegar';
        $DTO->sentAt = Carbon::now();
        $DTO->senderNumber = \Request::get('from');
        $DTO->to = \Request::get('to');
        $DTO->messageId = (int)\Request::get('messageid');
        $DTO->message = \Request::get('message');

        $this->service->receive($DTO);

        $eventInstanceName = config('sms.events.after_receiving_sms');
        event(new $eventInstanceName($DTO));
    }

    public function deliverKavenegar(){
        $DTO = new DeliverSMSDTO();
        $DTO->connectorName = 'kavenegar';
        $DTO->messageId = (int)\Request::get('messageid');

        $this->service->deliver($DTO);

        $eventInstanceName = config('sms.events.after_delivering_sms');
        event(new $eventInstanceName($DTO));
    }

    public function receiveMediana(){
        $DTO = new ReceiveSMSDTO();
        $DTO->connectorName = 'mediana';
        $DTO->sentAt = Carbon::now();
        $DTO->senderNumber = preg_replace("/^98/", "0", \Request::get('from'));
        $DTO->to = preg_replace("/^98/", "0", \Request::get('to'));
        $DTO->message = \Request::get('message');

        $this->service->receive($DTO);

        $eventInstanceName = config('sms.events.after_receiving_sms');
        event(new $eventInstanceName($DTO));
    }

    public function deliverMediana(){
        $DTO = new DeliverSMSDTO();
        $DTO->connectorName = 'mediana';
        $DTO->messageId = (int)\Request::get('messageid');

        $this->service->deliver($DTO);

        $eventInstanceName = config('sms.events.after_delivering_sms');
        event(new $eventInstanceName($DTO));
    }

}
