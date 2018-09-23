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
        if (! $to = $notifiable->routeNotificationFor('sms')) {
            return;
        }
        /**
         * @var SendSMSDTO
         */
        $getDTO = $notification->toSms($notifiable);
        $getDTO->to = $to;

        $this->smsService->send($getDTO);
    }

}