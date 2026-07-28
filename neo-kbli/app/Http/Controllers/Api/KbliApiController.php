<?php

namespace App\Http\Controllers\Api;

use App\Models\KbliCategory;
use App\Models\KbliItem;
use App\Services\SearchService;
use App\Http\Resources\KbliCategoryResource;
use App\Http\Resources\KbliItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class KbliApiController extends BaseApiController
{
    /**
     * Get list of active KBLI Categories (2-digit Golongan Pokok).
     */
    public function categories(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 50);
        $page = (int) $request->input('page', 1);

        $categories = \Illuminate\Support\Facades\Cache::remember("kbli_categories_p{$page}_l{$perPage}", 3600, function () use ($perPage) {
            return KbliCategory::where('is_active', true)
                ->withCount('sections')
                ->orderBy('sort_order', 'asc')
                ->paginate($perPage);
        });

        return $this->success(
            KbliCategoryResource::collection($categories),
            'Daftar kategori KBLI berhasil diambil'
        );
    }

    /**
     * Get detail of KBLI by code (Category or Item).
     */
    public function show(string $code): JsonResponse
    {
        // 1. Try finding Category (2 digit)
        $category = KbliCategory::where('code', $code)
            ->where('is_active', true)
            ->first();

        if ($category) {
            $category->level = 'category';
            $category->parent_data = null;
            $category->children_data = [];
            return $this->success(
                new KbliItemResource($category),
                'Detail KBLI berhasil diambil'
            );
        }

        // 2. Try finding KbliItem (5 digit)
        $item = KbliItem::where('code', $code)
            ->where('is_active', true)
            ->with(['section.category'])
            ->first();

        if ($item) {
            $item->level = 'item';
            return $this->success(
                new KbliItemResource($item),
                'Detail KBLI berhasil diambil'
            );
        }

        return $this->notFound('Kode KBLI tidak ditemukan');
    }

    /**
     * Search KBLI items using SearchService.
     */
    public function search(Request $request, SearchService $searchService): JsonResponse
    {
        $query = $request->input('q');
        if (empty($query) || strlen(trim($query)) < 3) {
            return $this->validationError(
                ['q' => ['Parameter pencarian (q) wajib diisi minimal 3 karakter.']],
                'Validasi gagal'
            );
        }

        $perPage = (int) $request->input('per_page', 15);
        $results = $searchService->searchKbli($query, $perPage);

        return $this->success(
            KbliItemResource::collection($results),
            'Hasil pencarian KBLI'
        );
    }
}
