<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeEmailNotification extends Notification
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
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Selamat Datang di Neo KBLI')
            ->greeting('Yth. ' . $notifiable->name . ',')
            ->line('Terima kasih telah mendaftar di Portal Neo KBLI. Akun Anda telah berhasil dibuat.')
            ->line('Melalui portal ini, Anda dapat mencari klasifikasi usaha dan mengajukan permohonan KBLI secara daring.')
            ->action('Menuju Beranda', url('/'))
            ->line('Jika Anda memiliki pertanyaan, silakan hubungi tim dukungan kami.')
            ->salutation('Hormat kami, Tim Neo KBLI');
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
