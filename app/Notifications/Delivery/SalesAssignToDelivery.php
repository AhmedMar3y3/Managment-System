<?php

namespace App\Notifications\Delivery;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;

class SalesAssignToDelivery extends Notification implements ShouldQueue
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
        return [FcmChannel::class,'database'];
    }
    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New Order Assigned to Delivery')
                    ->greeting('Hello!')
                    ->line('A new order has been assigned to you for delivery.')
                    ->line('Order ID: ' . $this->order->id)
                    ->line('Customer Name: ' . $this->order->customer_name)
                    ->action('View Order', url('/orders/' . $this->order->id))
                    ->line('Thank you for using our application!');
    }

    public function toFcm(object $regonotifiablevable): FcmMessage
    {
        return FcmMessage::create()
            ->notification(
                (new \NotificationChannels\Fcm\Resources\Notification())
                    ->title('New Order')
                    ->body('You have been assigned a new order for delivery')
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Order Assigned to Delivery',
            'body' => 'A new order has been assigned to you for delivery. Order ID: ' . $this->order->id,
        ];
    }
}
