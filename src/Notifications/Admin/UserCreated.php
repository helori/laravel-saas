<?php

namespace Helori\LaravelSaas\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;


class UserCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->from(config('app.name'))
            ->content("*Nouvel utilisateur*")
            ->attachment(function ($attachment) {
                $attachment->fields([
                    'Nom' => $this->user->firstname." ".$this->user->lastname,
                    'Email' => $this->user->email,
                ]);
            })
            ->success();
    }
}
