<?php

namespace App\Notifications;

use App\Event;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EventRegisterNotification extends Notification
{
    use Queueable;

    private $event;
    private $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Event $event, $user_data)
    {
        $this->event = $event;
        $this->user_data = $user_data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('New Event Registration')
            ->markdown('mail.event.register_notification', [
                'event' => $this->event,
                'user'  => $this->user_data
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'user_id' => $notifiable->id,
            'email' => $notifiable->email
        ];
    }

    public function toDatabase($notifiable)
    {
        return [
            'user_id' => $notifiable->id,
            'email' => $notifiable->email
        ];
    }

}
