<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;


     /**
     * The password reset token.
     *
     * @var string
     */
    public $token;



    /**
     * Create a new notification instance.
      * Create a new notification instance.
     *
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
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
            ->subject('Sol·licitud de restabliment de contrasenya') //agregamos el asunto
            ->greeting('Hola ' . $notifiable->name)// titulo del mensaje
            ->line("Reps aquest email perquè s'ha sol·licitat el restabliment de contrasenya per al teu compte")
            // Action : Texto del botón , url(app.url) la tomará desde el .env  , la ruta reset con el token respectivo
            ->action('Canviar contrasenya', url(config('app.url').route('restaurarContrasenya', $this->token, false)))
            ->line('Si no has realitzat aquesta petició, pots ignorar aquest correu')
            ->salutation('Salutacions'); // Saludo Final
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
