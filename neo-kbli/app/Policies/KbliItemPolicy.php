<?php

namespace App\Policies;

use App\Models\User;
use App\Models\KbliItem;
use App\Helpers\RoleList;
use Illuminate\Auth\Access\HandlesAuthorization;

class KbliItemPolicy
{
    use HandlesAuthorization;

    private function getAdminRoles(): array
    {
        return explode(',', str_replace('role:', '', RoleList::ADMIN_ACCESS));
    }

    public function createDirect(User $user): bool
    {
        return $user->hasAnyRole($this->getAdminRoles());
    }

    public function updateDirect(User $user, KbliItem $kbliItem): bool
    {
        return $user->hasAnyRole($this->getAdminRoles());
    }

    public function deleteDirect(User $user, KbliItem $kbliItem): bool
    {
        return $user->hasAnyRole($this->getAdminRoles());
    }

    public function import(User $user): bool
    {
        return $user->hasAnyRole($this->getAdminRoles());
    }

    public function bulkUpdate(User $user): bool
    {
        return $user->hasAnyRole($this->getAdminRoles());
    }

    public function export(User $user): bool
    {
        return $user->hasAnyRole($this->getAdminRoles());
    }
}
