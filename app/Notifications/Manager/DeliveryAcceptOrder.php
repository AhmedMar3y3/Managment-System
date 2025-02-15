<?php

namespace App\Notifications\Manager;

use App\Http\Controllers\FCMController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;

class DeliveryAcceptOrder extends Notification
{
    use Queueable;

    public $order;
    /**
     * Create a new notification instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [FcmChannel::class, 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    public function toFcm(object $notifiable): FcmMessage
    {
        return FcmMessage::create()
            ->setNotification([
                'title' => 'Delivery Accepted Order',
                'body'  => 'The order with ID ' . $this->order->id . ' has been accepted by delivery.',
            ])
            ->setAndroid([
                'notification' => [
                    'color' => '#0A0A0A',
                ],
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Delivery Accepted Order',
            'body'  => 'The order with ID ' . $this->order->id . ' has been accepted by delivery.',
        ];
    }
}
