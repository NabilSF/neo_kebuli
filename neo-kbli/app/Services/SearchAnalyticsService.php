<?php

namespace App\Services;

use App\Models\SearchLog;

class SearchAnalyticsService
{
    /**
     * Record a search event into the search_logs table.
     *
     * @param string|null $keyword
     * @param int|null $userId
     * @param string|null $ipAddress
     * @param string|null $userAgent
     * @param int|null $kbliItemId
     * @return SearchLog|null
     */
    public function record(
        ?string $keyword,
        ?int $userId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?int $kbliItemId = null
    ): ?SearchLog {
        if (empty(trim((string) $keyword))) {
            return null;
        }

        $log = SearchLog::firstOrCreate(
            ['keyword' => trim($keyword)],
            ['frequency' => 0]
        );
        $log->increment('frequency');
        $log->update(['last_searched_at' => now()]);
        return $log;
    }
}
