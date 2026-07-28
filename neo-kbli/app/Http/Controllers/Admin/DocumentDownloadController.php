<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DocumentDownloadController extends Controller
{
    /**
     * Securely download a document.
     * Authorized for Admin, Operator, or the original document owner.
     */
    public function download($mediaId)
    {
        $media = Media::findOrFail($mediaId);
        $user = Auth::user();

        // 1. Authorization Check
        $isOwner = $media->application && $media->application->user_id === $user->id;
        $isStaff = $user->hasRole('admin') || $user->hasRole('operator'); // Laratrust/Spatie role check

        if (!$isOwner && !$isStaff) {
            abort(403, 'Akses ke dokumen tidak diizinkan.');
        }

        // 2. Resolve Application ID for Auditing
        $applicationId = $media->application_id;

        // 3. Audit Logging
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Document Download',
            'subject_type' => Media::class,
            'subject_id' => $media->id,
            'properties' => json_encode(['application_id' => $applicationId, 'media_id' => $media->id]),
            'ip_address' => request()->ip(),
        ]);

        // 4. Stream Private File
        return Storage::download($media->path, $media->original_name);
    }
}
