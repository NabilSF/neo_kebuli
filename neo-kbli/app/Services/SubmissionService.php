<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationHistory;
use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SubmissionService
{
    /**
     * Process the full submission lifecycle of a product application.
     *
     * @param array $data Validated application data
     * @param UploadedFile|null $document The product document
     * @param \App\Models\User|null $user The authenticated user submitting the application
     * @return Application
     */
    public function submitApplication(array $data, ?UploadedFile $document, ?\App\Models\User $user = null): Application
    {
        return DB::transaction(function () use ($data, $document, $user) {
            
            if ($user) {
                $data['user_id'] = $user->id;
            }

            // 1. Generate core application attributes
            $data['tracking_code'] = $this->generateTrackingCode();
            $data['status'] = \App\Enums\WorkflowState::SUBMITTED->value;
            $data['submitted_at'] = now();

            // 2. Persist the main application record
            $application = Application::create($data);

            // 3. Create initial submission history trace & activity log
            $this->createInitialHistory($application, $user?->id);

            // 4. Process and persist the document if provided
            if ($document) {
                $this->processDocument($application->id, $document);
            }

            return $application;
        });
    }

    private function generateTrackingCode(): string
    {
        $dateStr = now()->format('Ymd');
        do {
            $randomStr = strtoupper(Str::random(8));
            $code = "KBLI-{$dateStr}-{$randomStr}";
        } while (Application::where('tracking_code', $code)->exists());
        
        return $code;
    }

    private function createInitialHistory(Application $application, ?int $changedBy = null): void
    {
        ApplicationHistory::create([
            'application_id' => $application->id,
            'status' => \App\Enums\WorkflowState::SUBMITTED->value,
            'notes' => 'Application submitted successfully by applicant.',
            'created_by' => $changedBy,
        ]);

        app(ActivityLogService::class)->logActivity(
            entity: $application,
            action: 'Permohonan diajukan oleh pemohon',
            oldState: null,
            newState: \App\Enums\WorkflowState::SUBMITTED,
            notes: 'Permohonan berhasil disimpan ke sistem.',
            userId: $changedBy
        );
    }

    private function processDocument(int $applicationId, UploadedFile $file): void
    {
        $checksum = hash_file('sha256', $file->getRealPath());
        $path = $file->store('applications/documents', 'private');
        
        $media = Media::create([
            'application_id' => $applicationId,
            'path' => $path,
            'stored_name' => basename($path),
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'checksum' => $checksum,
        ]);
    }
}
