<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPassword extends Notification
{
    use Queueable;

    protected $token;

    /**
     * Конструктор — обязательно передаём токен
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Каналы доставки
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Отправка письма
     */
    public function toMail($notifiable)
    {
        $url = url('/reset-password?token=' . $this->token . '&email=admin@tero.design');

        return (new MailMessage)
            ->to('dmr.ter@gmail.com') // <<<<< ВАЖНО: Переопределяем получателя
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
