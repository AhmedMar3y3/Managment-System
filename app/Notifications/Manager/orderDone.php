<?php

namespace App\Notifications\Manager;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;

class orderDone extends Notification implements ShouldQueue
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
                    ->subject('Order Completed')
                    ->line('The order with ID ' . $this->order->id . ' has been completed.')
                    ->line('Chef ID: ' . $this->order->chef_id)
                    ->action('View Order', url('/orders/' . $this->order->id))
                    ->line('Thank you for using our application!');
    }


    public function toFcm(object $regonotifiablevable): FcmMessage
    {
        return FcmMessage::create()
            ->notification(
                (new \NotificationChannels\Fcm\Resources\Notification())
                    ->title('Order Completed')
                    ->body('The order with ID ' . $this->order->id . ' has been completed.')
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
            'title' => 'Order Completed',
            'body'  => 'The order with ID ' . $this->order->id . ' has been completed.',
        ];
    }
}
