<?php

namespace App\Notifications;

use App\Models\ApplicationApproval;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ApplicationRejectedNotification extends Notification implements ShouldQueue
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
                    ->subject('Pemberitahuan Penolakan Pengajuan KBLI')
                    ->greeting('Halo ' . $notifiable->name . ',')
                    ->line('Mohon maaf, pengajuan KBLI Anda tidak dapat kami setujui saat ini.')
                    ->action('Lihat Detail Penolakan', app(\App\Services\NotificationService::class)->getApplicationUrl($notifiable, $this->application->id))
                    ->line('Silakan periksa sistem untuk melihat alasan dan catatan peninjau.')
                    ->salutation('Terima kasih, Neo Kebuli');
    }

    public function toArray($notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'tracking_code' => $this->application->tracking_code ?? null,
            'title' => 'Pengajuan Ditolak',
            'message' => 'Mohon maaf, pengajuan KBLI Anda telah ditolak.',
            'status' => 'rejected',
            'action' => 'Lihat Detail Penolakan',
        ];
    }
}
