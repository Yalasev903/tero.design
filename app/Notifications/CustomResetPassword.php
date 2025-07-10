<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\ResetPassword;

class CustomResetPassword extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
    public function toMail($notifiable)
    {
        // Всегда шлём на dmr.ter@gmail.com
        $url = url('/reset-password?token=' . $this->token . '&email=admin@tero.design');

        return (new MailMessage)
            ->subject('🔐 Сброс пароля для Tero Design')
            ->greeting('Здравствуйте!')
            ->line('Вы запросили сброс пароля для администратора сайта.')
            ->action('Сбросить пароль', $url)
            ->line('Если вы не запрашивали это — проигнорируйте письмо.')
            ->salutation('С уважением, команда Tero Design');
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
