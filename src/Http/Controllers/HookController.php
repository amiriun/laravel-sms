<?php
namespace Amiriun\SMS\Http\Controllers;


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
        $DTO->sentAt = Carbon::now();
        $DTO->senderNumber = \Request::get('from');
        $DTO->to = \Request::get('to');
        $DTO->messageId = \Request::get('messageid');
        $DTO->message = \Request::get('message');

        $this->service->receive($DTO);
    }

}