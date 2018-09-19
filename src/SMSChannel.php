<?php
namespace Amiriun\SMS;

use Amiriun\SMS\DataContracts\SendSMSDTO;
use Amiriun\SMS\Services\SMSService;
use Illuminate\Notifications\Notification;

class SMSChannel
{
    /**
     * @var SMSService
     */
    private $smsService;

    public function __construct(SMSService $SMSService)
    {
        $this->smsService = $SMSService;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $to = $notifiable->routeNotificationFor('toSms')) {
            return;
        }

        $message = $notification->toSms($notifiable);

//        if (is_string($message)) {
//            $message = new NexmoMessage($message);
//        }

        $getDTO = $this->prepareDTO($to, $message);

        $this->smsService->send($getDTO);
    }

    /**
     * @param $to
     * @param $message
     *
     * @return SendSMSDTO
     */
    private function prepareDTO($to, $message)
    {
        $DTO = new SendSMSDTO();
        $DTO->from = 'xxxx';
        $DTO->to = $to;
        $DTO->text = $message;

        return $DTO;
    }

}