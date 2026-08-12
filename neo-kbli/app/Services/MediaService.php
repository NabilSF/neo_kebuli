<?php

namespace App\Services;

use App\Helpers\CacheKey;
use App\Models\Application;
use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    private const DISK = 'private';
    private const BASE_PATH = 'applications/documents';
    private const MAX_FILES_PER_APPLICATION = 10;

    /**
     * Upload a file and attach it to an application.
     */
    public function upload(Application $application, UploadedFile $file): Media
    {
        $currentCount = $application->media()->count();

        if ($currentCount >= self::MAX_FILES_PER_APPLICATION) {
            throw new \App\Exceptions\MediaLimitExceededException(
                'Jumlah lampiran maksimal (' . self::MAX_FILES_PER_APPLICATION . ') telah tercapai.'
            );
        }

        $checksum = hash_file('sha256', $file->getRealPath());
        $path = $file->store(self::BASE_PATH, self::DISK);

        $media = Media::create([
            'application_id' => $application->id,
            'path'           => $path,
            'stored_name'    => basename($path),
            'original_name'  => $file->getClientOriginalName(),
            'mime_type'      => $file->getMimeType(),
            'size'           => $file->getSize(),
            'checksum'       => $checksum,
        ]);

        $this->invalidateRelatedCaches($application);

        return $media;
    }

    /**
     * Delete a media record and its physical file.
     */
    public function delete(Media $media): void
    {
        $application = $media->application;

        if (Storage::disk(self::DISK)->exists($media->path)) {
            Storage::disk(self::DISK)->delete($media->path);
        }

        $media->delete();

        if ($application) {
            $this->invalidateRelatedCaches($application);
        }
    }

    /**
     * Stream-download a media file.
     */
    public function download(Media $media): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!Storage::disk(self::DISK)->exists($media->path)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk(self::DISK);
        return $disk->download($media->path, $media->original_name);
    }

    /**
     * Invalidate all caches related to the application owner.
     */
    private function invalidateRelatedCaches(Application $application): void
    {
        if ($application->user_id) {
            Cache::forget(CacheKey::userDashboard($application->user_id));
            CacheKey::invalidateUserApplicationsIndex($application->user_id);
        }

        CacheKey::invalidateUserApplicationShow($application->id);
    }
}
