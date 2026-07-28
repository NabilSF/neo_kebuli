<?php

namespace App\Http\Controllers\Api;

use App\Models\Application;
use App\Models\Media;
use App\Services\SubmissionService;
use App\Http\Resources\ApplicationResource;
use App\Http\Resources\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ApplicationApiController extends BaseApiController
{
    /**
     * List current user's applications with pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $applications = Application::where('user_id', $request->user()->id)
            ->with(['kbliItem', 'histories', 'media'])
            ->latest()
            ->paginate(15);

        return $this->success(
            ApplicationResource::collection($applications),
            'Daftar pengajuan berhasil diambil'
        );
    }

    /**
     * Get detail of a specific application owned by the logged in user.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $application = Application::with(['kbliItem', 'histories', 'media'])->find($id);

        if (!$application) {
            return $this->notFound('Pengajuan tidak ditemukan.');
        }

        if ($application->user_id !== $request->user()->id) {
            return $this->forbidden('Anda tidak memiliki akses ke pengajuan ini.');
        }

        return $this->success(
            new ApplicationResource($application),
            'Detail pengajuan berhasil diambil'
        );
    }

    /**
     * Public tracking endpoint (Accessible without login).
     */
    public function track(string $tracking_code): JsonResponse
    {
        $application = Application::with(['kbliItem', 'histories', 'media'])
            ->where('tracking_code', trim($tracking_code))
            ->first();

        if (!$application) {
            return $this->notFound('Kode tracking tidak ditemukan.');
        }

        return $this->success(
            new ApplicationResource($application),
            'Data tracking berhasil ditemukan'
        );
    }

    /**
     * Submit a new product application.
     */
    public function store(Request $request, SubmissionService $submissionService): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'kbli_item_id'        => 'required|exists:kbli_items,id',
            'product_name'        => 'required|string|max:255',
            'product_material'    => 'nullable|string',
            'production_process'  => 'nullable|string',
            'product_description' => 'nullable|string',
            'saker_address'       => 'nullable|string',
            'document'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors(), 'Validasi pengajuan gagal');
        }

        $application = $submissionService->submitApplication(
            $validator->validated(),
            $request->file('document'),
            $request->user()
        );

        $application->load(['kbliItem', 'histories', 'media']);

        return $this->created(
            new ApplicationResource($application),
            'Pengajuan berhasil dibuat dengan kode tracking ' . $application->tracking_code
        );
    }

    /**
     * Upload additional document attachment to an application.
     */
    public function uploadMedia(Request $request, $id): JsonResponse
    {
        $application = Application::find($id);

        if (!$application) {
            return $this->notFound('Pengajuan tidak ditemukan.');
        }

        if ($application->user_id !== $request->user()->id) {
            return $this->forbidden('Anda tidak berhak mengunggah dokumen untuk pengajuan ini.');
        }

        $validator = Validator::make($request->all(), [
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors(), 'Validasi dokumen gagal');
        }

        $file = $request->file('document');
        $checksum = hash_file('sha256', $file->getRealPath());
        $path = $file->store('applications/documents', 'private');

        $media = Media::create([
            'application_id' => $application->id,
            'path'           => $path,
            'stored_name'    => basename($path),
            'original_name'  => $file->getClientOriginalName(),
            'mime_type'      => $file->getMimeType(),
            'size'           => $file->getSize(),
            'checksum'       => $checksum,
        ]);

        return $this->created(
            new MediaResource($media),
            'Dokumen lampiran berhasil diunggah'
        );
    }

    /**
     * Get reference status values.
     */
    public function statuses(): JsonResponse
    {
        return $this->success([
            'submitted'     => 'Diajukan',
            'under_review'  => 'Diproses / Ditinjau',
            'need_revision' => 'Perlu Perbaikan',
            'approved'      => 'Disetujui',
            'rejected'      => 'Ditolak',
        ], 'Daftar status pengajuan');
    }
}
