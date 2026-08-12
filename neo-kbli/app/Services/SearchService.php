<?php

namespace App\Services;

use App\Models\KbliItem;
use App\Models\SearchLog;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchService
{
    /**
     * Perform a search on KBLI items based on a keyword or code.
     *
     * @param string|null $query
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchKbli(?string $query, int $perPage = 12): LengthAwarePaginator
    {
        $itemsQuery = KbliItem::with(['section.category'])->where('is_active', true);

        if (!empty($query)) {
            $itemsQuery->where(function($q) use ($query) {
                $q->where('code', 'like', "%{$query}%")
                  ->orWhere('name', 'like', "%{$query}%");
            });

            $this->logSearch($query);
        }

        return $itemsQuery->orderBy('code', 'asc')->paginate($perPage);
    }

    /**
     * Increment the search frequency for a given keyword.
     */
    private function logSearch(string $query): void
    {
        $keyword = strtolower(trim($query));
        
        if (empty($keyword)) {
            return;
        }

        try {
            SearchLog::upsert(
                [
                    'keyword' => $keyword,
                    'frequency' => 1,
                    'last_searched_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                ['keyword'],
                [
                    'frequency' => \Illuminate\Support\Facades\DB::raw('frequency + 1'),
                    'last_searched_at' => now(),
                    'updated_at' => now(),
                ]
            );
        } catch (\Throwable $e) {
            // Ignore search log failures to prevent search endpoint breakage
        }
    }
}
