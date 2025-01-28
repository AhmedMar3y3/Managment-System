<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ManagerApproved extends Notification
{
    use Queueable;

    public $manager;

    /**
     * Create a new notification instance.
     */
    public function __construct($manager)
    {
        $this->manager = $manager;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Approval Notification')
                    ->greeting('Hello ' . $this->manager->first_name . ',')
                    ->line('We are pleased to inform you that you have been approved as a manager.')
                    ->action('View Dashboard', url('/dashboard'))
                    ->line('Thank you for being a part of our team!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
