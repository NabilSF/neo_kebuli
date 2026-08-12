<?php

namespace App\Notifications;

use App\Models\ApplicationApproval;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RevisionRequestedNotification extends Notification implements ShouldQueue
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
                    ->subject('Permintaan Revisi Pengajuan KBLI')
                    ->greeting('Halo ' . $notifiable->name . ',')
                    ->line('Pengajuan KBLI Anda memerlukan revisi sebelum dapat diproses lebih lanjut.')
                    ->line('Terdapat beberapa temuan atau catatan dari tim peninjau yang perlu Anda perbaiki.')
                    ->action('Perbaiki Pengajuan', app(\App\Services\NotificationService::class)->getApplicationUrl($notifiable, $this->application->id))
                    ->salutation('Terima kasih, Neo Kebuli');
    }

    public function toArray($notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'tracking_code' => $this->application->tracking_code ?? null,
            'title' => 'Permintaan Revisi',
            'message' => 'Pengajuan KBLI Anda membutuhkan revisi. Silakan periksa catatan peninjau.',
            'status' => 'revision',
            'action' => 'Perbaiki Pengajuan',
        ];
    }
}
