<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $teamName,
        public string $token,
        public string $role
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        Log::info("Pregătim mail pentru {$notifiable->email} cu token {$this->token}");
        $url = route('filament.admin.auth.invitation-register', ['token' => $this->token]);

        $roleText = $this->role === 'admin' ? 'Administrator' : 'Utilizator';

        return (new MailMessage)
            ->subject('Invitație echipă - ' . $this->teamName)
            ->line('Ai fost invitat să te alături echipei ' . $this->teamName . ' ca ' . $roleText . '.')
            ->action('Acceptă invitația și creează cont', $url)
            ->line('Dacă nu te așteptai la această invitație, poți ignora acest email.');
    }
}