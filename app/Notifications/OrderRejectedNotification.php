<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderRejectedNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    { 
        return (new MailMessage)
            ->subject('إشعار رفض الطلب')
            ->line(' تاكد من انك تريد اعاده ارسال الطلب'.' تم رفض طلبك  ' . $this->order->order_type)
            ->action('عرض الطلب', url('/orders/' . $this->order->order_type))
            ->line('شكرًا لاستخدامك تطبيقنا!');
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'message' => 'تم رفض طلبك ' . $this->order->order_type,
        ];
    }
}
