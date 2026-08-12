<?php

namespace App\Services;

use App\Models\KbliItem;
use App\Helpers\CacheKey;
use Illuminate\Support\Facades\DB;

class KbliItemService
{
    protected ActivityLogService $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function getPaginatedItems(array $filters, string $sortBy = 'code', string $direction = 'asc', int $perPage = 15)
    {
        $query = KbliItem::with('section.category:id,name,code')
            ->select(['id', 'code', 'name', 'section_id', 'is_active', 'description']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['section_id'])) {
            $query->where('section_id', $filters['section_id']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['category_id'])) {
            $categoryId = $filters['category_id'];
            $query->whereHas('section', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        $allowedSorts = ['code', 'name', 'is_active', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $direction);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getSectionsForDropdown()
    {
        return \App\Models\KbliSection::pluck('name', 'id');
    }

    public function getCategoriesForDropdown()
    {
        return \App\Models\KbliCategory::pluck('name', 'id');
    }

    public function createKbliItem(array $data): KbliItem
    {
        return DB::transaction(function () use ($data) {
            $item = KbliItem::create($data);

            $this->activityLogService->logActivity(
                entity: $item,
                action: 'kbli.created',
                notes: 'Admin created KBLI Item directly',
                properties: ['code' => $item->code, 'name' => $item->name]
            );

            CacheKey::invalidateKbliCategories();
            // Invalidate other relevant caches if needed

            return $item;
        });
    }

    public function updateKbliItem(KbliItem $item, array $data): KbliItem
    {
        return DB::transaction(function () use ($item, $data) {
            $oldData = $item->only(['code', 'name', 'description', 'section_id', 'is_active']);
            $item->update($data);

            $this->activityLogService->logActivity(
                entity: $item,
                action: 'kbli.updated',
                notes: 'Admin updated KBLI Item directly',
                properties: ['old' => $oldData, 'new' => $data]
            );

            CacheKey::invalidateKbliCategories();

            return $item;
        });
    }

    public function deleteKbliItem(KbliItem $item): bool
    {
        if ($item->applications()->exists()) {
            throw new \LogicException('KBLI is currently being used by an application and cannot be deleted.');
        }

        return DB::transaction(function () use ($item) {
            $code = $item->code;
            $name = $item->name;
            
            $item->delete();

            $this->activityLogService->logActivity(
                entity: $item, // Keep the instance in memory for log
                action: 'kbli.deleted',
                notes: 'Admin deleted KBLI Item directly',
                properties: ['code' => $code, 'name' => $name]
            );

            CacheKey::invalidateKbliCategories();

            return true;
        });
    }

    public function bulkUpdateStatus(array $ids, string $action): int
    {
        return DB::transaction(function () use ($ids, $action) {
            $isActive = $action === 'activate' ? 1 : 0;
            $affected = KbliItem::whereIn('id', $ids)->update(['is_active' => $isActive]);

            if ($affected > 0) {
                // Fetch basic info for logging without loading full models
                $processedItems = KbliItem::whereIn('id', $ids)->select('id', 'code', 'name')->get()->toArray();
                
                // Create a dummy entity for activity log since it's a bulk operation
                $dummyEntity = new KbliItem(['id' => 0]); 
                // Wait, activity log service requires a valid entity. If dummyEntity doesn't exist in DB it might fail?
                // Let's just use the first item as reference, or use Application generic log if possible.
                // ActivityLogService uses $entity->activityLogs()->create(...), so we can use the first item as the "subject".
                $firstItem = KbliItem::find($ids[0]);

                $this->activityLogService->logActivity(
                    entity: $firstItem,
                    action: $action === 'activate' ? 'kbli.bulk_activated' : 'kbli.bulk_deactivated',
                    notes: "Admin bulk {$action} {$affected} KBLI items",
                    properties: [
                        'affected_count' => $affected,
                        'processed_ids' => array_column($processedItems, 'id'),
                        'processed_codes' => array_column($processedItems, 'code')
                    ]
                );

                CacheKey::invalidateKbliCategories();
            }

            return $affected;
        });
    }
}
