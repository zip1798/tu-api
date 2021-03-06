<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RegisterConfirmation extends Notification
{
    use Queueable;

    private $user;
    private $data = [
        'email' => '',
        'password' => ''
    ];
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data = [])
    {
        $this->data = array_merge($this->data, $data);
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
            ->subject('Confirmation of registration')
            ->markdown('mail.user.register_confirmation', $this->data);
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
            'user_id' => $notifiable>id,
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
