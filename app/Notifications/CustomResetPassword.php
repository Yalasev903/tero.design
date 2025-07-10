<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPassword extends Notification
{
    use Queueable;

    protected $token;

    /**
     * Принимаем токен сброса пароля
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Каналы доставки уведомления
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Формируем письмо
     */
    public function toMail($notifiable)
    {
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
     * Опционально — массив для логирования
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
