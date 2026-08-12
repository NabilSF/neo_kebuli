<?php

namespace App\Services;

use App\Models\News;
use App\Models\NewsCategory;
use App\Enums\NewsStatus;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class NewsService
{
    /**
     * Get paginated published news.
     */
    public function getPaginatedNews(int $perPage = 10, ?string $search = null, ?string $categorySlug = null): LengthAwarePaginator
    {
        return News::with(['category', 'author'])
            ->where('status', NewsStatus::PUBLISHED->value)
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->when($categorySlug, function ($query, $categorySlug) {
                $query->whereHas('category', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get latest published news.
     */
    public function getLatestNews(int $limit = 5): Collection
    {
        return News::select(['id', 'title', 'slug', 'created_at', 'status'])
            ->where('status', NewsStatus::PUBLISHED->value)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get categories with published news count.
     */
    public function getCategoriesWithCount(): Collection
    {
        return NewsCategory::select(['id', 'name', 'slug'])
            ->withCount(['news' => function ($q) {
                $q->where('status', NewsStatus::PUBLISHED->value);
            }])
            ->get();
    }

    /**
     * Get related news excluding the current one.
     */
    public function getRelatedNews(int $excludeId, int $limit = 3): Collection
    {
        return News::with(['category', 'author'])
            ->where('status', NewsStatus::PUBLISHED->value)
            ->where('id', '!=', $excludeId)
            ->latest()
            ->limit($limit)
            ->get();
    }
}
