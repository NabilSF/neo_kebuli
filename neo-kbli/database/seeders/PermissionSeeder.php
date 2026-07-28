<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Seed the permissions table and assign them to roles.
     * Idempotent: safe to run multiple times.
     */
    public function run(): void
    {
        $permissions = [
            // Application management
            ['name' => 'View Applications',   'slug' => 'view-applications'],
            ['name' => 'Review Applications', 'slug' => 'review-applications'],
            ['name' => 'Delete Applications', 'slug' => 'delete-applications'],

            // KBLI management
            ['name' => 'View KBLI',           'slug' => 'view-kbli'],
            ['name' => 'Manage KBLI',         'slug' => 'manage-kbli'],

            // User management
            ['name' => 'View Users',          'slug' => 'view-users'],
            ['name' => 'Manage Users',        'slug' => 'manage-users'],

            // News management
            ['name' => 'View News',           'slug' => 'view-news'],
            ['name' => 'Manage News',         'slug' => 'manage-news'],

            // Document management
            ['name' => 'Download Documents',  'slug' => 'download-documents'],

            // Reports
            ['name' => 'View Reports',        'slug' => 'view-reports'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['slug' => $perm['slug']],
                ['name' => $perm['name']]
            );
        }

        // Assign all permissions to super-admin and admin
        $allPermissionIds = Permission::pluck('id');

        $superAdmin = Role::where('slug', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->permissions()->syncWithoutDetaching($allPermissionIds);
        }

        $admin = Role::where('slug', 'admin')->first();
        if ($admin) {
            $admin->permissions()->syncWithoutDetaching($allPermissionIds);
        }

        // Assign limited permissions to operator
        $operatorSlugs = [
            'view-applications',
            'review-applications',
            'view-kbli',
            'view-news',
            'download-documents',
        ];
        $operatorPermissionIds = Permission::whereIn('slug', $operatorSlugs)->pluck('id');

        $operator = Role::where('slug', 'operator')->first();
        if ($operator) {
            $operator->permissions()->syncWithoutDetaching($operatorPermissionIds);
        }
    }
}
