<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPassword extends Notification
{
    use Queueable;

    public function __construct(
        public string $token
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    protected function resetUrl($notifiable): string
    {
        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->from(config('mail.from.address'), 'GestiónCash')
            ->subject('Restablecé tu contraseña — GestiónCash')
            ->view('emails.password-reset', [
                'user' => $notifiable,
                'url' => $this->resetUrl($notifiable),
                'expire' => config(
                    'auth.passwords.' . config('auth.defaults.passwords') . '.expire'
                ),
            ]);
    }
}