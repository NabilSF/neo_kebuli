<?php

namespace App\Notifications;

use App\Models\ApplicationApproval;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ApplicationApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [60, 180];

    public function __construct(
        public ApplicationApproval $approval,
        public \App\Models\Application $application
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Selamat! Pengajuan KBLI Disetujui')
                    ->greeting('Halo ' . $notifiable->name . ',')
                    ->line('Kami sampaikan bahwa pengajuan KBLI Anda telah resmi disetujui.')
                    ->line('Keputusan ini telah final dan tercatat dalam sistem.')
                    ->action('Lihat Pengajuan', app(\App\Services\NotificationService::class)->getApplicationUrl($notifiable, $this->application->id))
                    ->salutation('Terima kasih, Neo Kebuli');
    }

    public function toArray($notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'tracking_code' => $this->application->tracking_code,
            'title' => 'Pengajuan Disetujui',
            'message' => 'Selamat! Pengajuan KBLI Anda telah disetujui.',
            'status' => 'approved',
            'action' => 'Lihat Pengajuan',
        ];
    }
}
