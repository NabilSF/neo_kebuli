<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ApplicationSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries;
    public array $backoff;

    public function __construct(public Application $application)
    {
        $this->tries = config('workflow.queue.tries', 3);
        $this->backoff = config('workflow.queue.backoff', [60, 180]);
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Pengajuan KBLI Baru')
                    ->greeting('Halo Admin,')
                    ->line('Sebuah pengajuan KBLI baru telah disubmit ke dalam sistem.')
                    ->line('Nomor Registrasi: ' . $this->application->id)
                    ->action('Lihat Pengajuan', app(\App\Services\NotificationService::class)->getApplicationUrl($notifiable, $this->application->id))
                    ->line('Silakan periksa dan tugaskan kepada peninjau yang tersedia.')
                    ->salutation('Terima kasih, Neo Kebuli');
    }

    public function toArray($notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'tracking_code' => $this->application->tracking_code ?? null,
            'title' => 'Pengajuan Baru',
            'message' => 'Pengajuan baru telah diajukan dan menunggu penugasan peninjau.',
            'status' => 'submitted',
            'action' => 'Lihat Pengajuan',
        ];
    }
}
