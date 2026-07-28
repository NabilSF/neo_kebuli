<?php

namespace App\Services;

use App\Models\SearchLog;
use App\Models\KbliItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PopularKbliService
{
    /**
     * Get the most popular KBLI items based on search logs.
     *
     * @param int $limit
     * @return Collection
     */
    public function getPopular(int $limit = 6): Collection
    {
        // Graceful fallback: If the table doesn't exist yet, return empty collection safely
        if (!Schema::hasTable('search_logs')) {
            return collect();
        }

        $popularLogs = SearchLog::whereNotNull('kbli_item_id')
            ->select('kbli_item_id', DB::raw('count(*) as views_count'))
            ->groupBy('kbli_item_id')
            ->orderByDesc('views_count')
            ->limit($limit)
            ->get();

        if ($popularLogs->isEmpty()) {
            return collect();
        }

        $itemIds = $popularLogs->pluck('kbli_item_id')->toArray();

        // Fetch the items with eager loading
        $items = KbliItem::whereIn('id', $itemIds)
            ->where('is_active', true)
            ->with(['section.category'])
            ->get()
            ->keyBy('id');

        // Map back to preserve the popularity order based on logs
        $sortedItems = $popularLogs->map(function ($log) use ($items) {
            return $items->get($log->kbli_item_id);
        })->filter();

        return $sortedItems;
    }
}
