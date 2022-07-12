<?php

namespace Helori\LaravelSaas\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;


class ResetPassword extends Notification
{
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail()
    {
        return (new MailMessage)
            ->subject('Votre mot de passe '.env('APP_NAME'))
            ->line([
                'Vous recevez cet email afin de pouvoir vous connecter pour la première fois, ou suite à un oubli de mot de passe.
                Vous pouvez créer votre mot de passe en cliquant sur ce bouton :',
            ])
            ->action('Choisir mon mot de passe', url('/reset-password', ['token' => $this->token]));
    }
}
