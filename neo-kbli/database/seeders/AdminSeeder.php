<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed the default admin and operator accounts.
     * Idempotent: safe to run multiple times.
     * IMPORTANT: Change passwords immediately after first production deployment.
     */
    public function run(): void
    {
        $accounts = [
            [
                'name'      => 'Super Administrator',
                'email'     => 'superadmin@neokbli.local',
                'password'  => 'SuperAdmin@2024!',
                'phone'     => null,
                'is_active' => true,
                'role_slug' => 'super-admin',
            ],
            [
                'name'      => 'Administrator',
                'email'     => 'admin@neokbli.local',
                'password'  => 'Admin@2024!',
                'phone'     => null,
                'is_active' => true,
                'role_slug' => 'admin',
            ],
            [
                'name'      => 'Operator',
                'email'     => 'operator@neokbli.local',
                'password'  => 'Operator@2024!',
                'phone'     => null,
                'is_active' => true,
                'role_slug' => 'operator',
            ],
        ];

        foreach ($accounts as $account) {
            $roleslug = $account['role_slug'];
            unset($account['role_slug']);

            $user = User::withTrashed()->firstOrCreate(
                ['email' => $account['email']],
                array_merge($account, ['password' => Hash::make($account['password'])])
            );

            // Restore soft-deleted users if they were previously removed
            if ($user->trashed()) {
                $user->restore();
            }

            // Ensure account is active
            $user->update(['is_active' => true]);

            // Attach role without duplicating
            $role = Role::where('slug', $roleslug)->first();
            if ($role && !$user->roles()->where('roles.id', $role->id)->exists()) {
                $user->roles()->attach($role->id);
            }
        }
    }
}
