<?php

namespace App\Notifications;

use App\Models\ApplicationAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReviewerAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [60, 180];

    public function __construct(
        public ApplicationAssignment $assignment,
        public \App\Models\Application $application
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Penugasan Peninjauan Pengajuan KBLI')
                    ->greeting('Halo ' . $notifiable->name . ',')
                    ->line('Anda telah ditugaskan sebagai Peninjau (Reviewer) untuk sebuah pengajuan KBLI.')
                    ->line('Tugas ini memerlukan evaluasi dan rekomendasi dari Anda.')
                    ->action('Lihat Penugasan', app(\App\Services\NotificationService::class)->getApplicationUrl($notifiable, $this->application->id))
                    ->salutation('Terima kasih, Neo Kebuli');
    }

    public function toArray($notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'tracking_code' => $this->application->tracking_code ?? null,
            'title' => 'Penugasan Peninjauan',
            'message' => 'Anda telah ditugaskan untuk meninjau sebuah pengajuan KBLI.',
            'status' => 'assigned',
            'action' => 'Lihat Penugasan',
        ];
    }
}
