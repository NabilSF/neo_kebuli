<?php

namespace App\Http\Controllers\Api;

use App\Models\News;
use App\Http\Resources\NewsResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NewsApiController extends BaseApiController
{
    /**
     * Get list of published news articles.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 10);
        $query = News::with(['category', 'author'])
            ->where('status', 'published');

        if ($search = $request->input('q')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        if ($categorySlug = $request->input('category')) {
            $query->whereHas('category', function($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        $news = $query->orderBy('published_at', 'desc')->paginate($perPage);

        return $this->success(
            NewsResource::collection($news),
            'Daftar berita berhasil diambil'
        );
    }

    /**
     * Get detail of a published news article by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $article = News::with(['category', 'author'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$article) {
            return $this->notFound('Berita tidak ditemukan');
        }

        $article->is_detail = true;

        return $this->success(
            new NewsResource($article),
            'Detail berita berhasil diambil'
        );
    }
}
