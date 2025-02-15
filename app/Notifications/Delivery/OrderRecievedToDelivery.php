<?php

namespace App\Notifications\Delivery;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;

class OrderRecievedToDelivery extends Notification
{
    use Queueable;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return [FcmChannel::class, 'database'];    }

    public function toMail($notifiable)
    { 
        return (new MailMessage)
            ->subject('إشعار رفض الطلب')
            ->line(' تاكد من انك تريد اعاده ارسال الطلب'.' تم رفض طلبك  ' . $this->order->order_type)
            ->action('عرض الطلب', url('/orders/' . $this->order->order_type))
            ->line('شكرًا لاستخدامك تطبيقنا!');
    }

    public function toFcm(object $notifiable): FcmMessage
    {
        return FcmMessage::create()
            ->setNotification([
                'title' => 'New Order',
                'body'  => 'You have been assigned a new order for delivery',
            ])
            ->setAndroid([
                'notification' => [
                    'color' => '#0A0A0A',
                ],
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'New Order',
            'body'  => 'You have been assigned a new order for delivery',
        ];
    }
}
