<?php

namespace Helori\LaravelSaas\Notifications\Auth;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Notifications\Messages\MailMessage;


class VerifyEmail extends VerifyEmailBase
{
    /**
     * Get the verify email notification mail message for the given URL.
     *
     * @param  string  $url
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject('Confirmez votre adresse email pour '.env('APP_NAME').':')
            ->line("Merci de cliquer sur le bouton ci-dessous pour confirmer votre adresse email")
            ->action('Confirmer mon adresse email', $url)
            ->line("Si vous n'avez pas créé de compte ".env('APP_NAME').", alors aucune action n'est requise de votre part.");
    }
}
