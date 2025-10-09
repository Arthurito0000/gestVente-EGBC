<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class PasswordChangedNotification extends Notification
{
    use Queueable;

    protected $ipAddress;
    protected $userAgent;
    protected $timestamp;

    /**
     * Create a new notification instance.
     */
    public function __construct($ipAddress = null, $userAgent = null)
    {
        $this->ipAddress = $ipAddress ?? request()->ip();
        $this->userAgent = $userAgent ?? request()->userAgent();
        $this->timestamp = Carbon::now();
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
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🔒 Mot de passe modifié - Sales Manager EGBC')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Votre mot de passe a été modifié avec succès.')
            ->line('**Détails de la modification :**')
            ->line('📅 **Date :** ' . $this->timestamp->format('d/m/Y à H:i:s'))
            ->line('🌐 **Adresse IP :** ' . $this->ipAddress)
            ->line('💻 **Navigateur :** ' . $this->getBrowserInfo())
            ->line('')
            ->line('Si vous n\'êtes pas à l\'origine de cette modification, contactez immédiatement l\'administrateur système.')
            ->action('Accéder à votre compte', route('login'))
            ->line('Pour votre sécurité, nous vous recommandons de :')
            ->line('• Utiliser un mot de passe unique et complexe')
            ->line('• Ne jamais partager vos identifiants')
            ->line('• Vous déconnecter après utilisation')
            ->line('')
            ->line('Merci d\'utiliser Sales Manager EGBC.')
            ->salutation('L\'équipe Sales Manager EGBC');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'password_changed',
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'timestamp' => $this->timestamp->toISOString(),
        ];
    }

    /**
     * Extract browser information from user agent
     */
    private function getBrowserInfo(): string
    {
        $userAgent = $this->userAgent;
        
        // Détection simple du navigateur
        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Google Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            return 'Mozilla Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            return 'Microsoft Edge';
        } else {
            return 'Navigateur inconnu';
        }
    }
}
