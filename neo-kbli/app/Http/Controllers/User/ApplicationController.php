<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadMediaRequest;
use App\Models\Application;
use App\Models\Media;
use App\Services\MediaService;
use App\Services\UserApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApplicationController extends Controller
{
    public function __construct(
        private UserApplicationService $applicationService,
        private MediaService $mediaService
    ) {}

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Application::class);

        $params = $request->only(['status', 'search', 'page']);
        $data = $this->applicationService->getApplicationsData($request->user()->id, $params);

        return view('user.applications.index', compact('data', 'params'));
    }

    public function show(Application $application)
    {
        Gate::authorize('view', $application);

        $data = $this->applicationService->getApplicationData($application);

        return view('user.applications.show', compact('data'));
    }

    public function uploadMedia(UploadMediaRequest $request, Application $application)
    {
        Gate::authorize('uploadMedia', $application);

        try {
            $this->mediaService->upload($application, $request->file('document'));

            return redirect()
                ->route('user.applications.show', $application)
                ->with('success', 'Dokumen berhasil diunggah.');
        } catch (\App\Exceptions\MediaLimitExceededException $e) {
            return redirect()
                ->route('user.applications.show', $application)
                ->with('error', $e->getMessage());
        }
    }

    public function deleteMedia(Application $application, Media $media)
    {
        Gate::authorize('deleteMedia', $application);

        // Guard: media must belong to this application
        if ($media->application_id !== $application->id) {
            abort(403);
        }

        $this->mediaService->delete($media);

        return redirect()
            ->route('user.applications.show', $application)
            ->with('success', 'Dokumen berhasil dihapus.');
    }

    public function downloadMedia(Application $application, Media $media)
    {
        Gate::authorize('view', $application);

        // Guard: media must belong to this application
        if ($media->application_id !== $application->id) {
            abort(403);
        }

        return $this->mediaService->download($media);
    }
}
