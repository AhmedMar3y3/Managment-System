<?php

namespace App\Notifications\Manager;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;

class SendToManager extends Notification implements ShouldQueue
{
    use Queueable;

public  $order; 

    public function __construct($order)
    {
        $this->order=$order;
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
        ->line(':المدير:لقد وصلنا  طلبك ' . $this->order->order_id);
    //     ->action('المدير', url('/manager/' . $this->order)) 
    //     ->line('شكراً');
    }


    public function toFcm(object $regonotifiablevable): FcmMessage
    {
        return FcmMessage::create()
            ->notification(
                (new \NotificationChannels\Fcm\Resources\Notification())
                    ->title('New Order')
                    ->body('There is a new order to check')
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
            'title' => 'New Order',
            'body'  => 'There is a new order to check. Order ID: ' . $this->order->id,
        ];
    }
}
