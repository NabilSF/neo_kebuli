<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create permissions
        $permissions = [
            'view-applications',
            'review-applications',
            'delete-applications',
            'view-kbli',
            'manage-kbli',
            'view-users',
            'manage-users',
            'view-news',
            'manage-news',
            'download-documents',
            'view-reports',
            'application.view',
            'application.create',
            'application.assign',
            'application.review',
            'application.approve',
            'application.archive',
            'dashboard.view',
            'notification.view',
        ];

        foreach ($permissions as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        // 2. Assign permissions to roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $operator = Role::firstOrCreate(['name' => 'Operator', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        
        $allPermissionNames = Permission::pluck('name')->toArray();
        $superAdmin->syncPermissions($allPermissionNames);
        $admin->syncPermissions($allPermissionNames);
        
        $operatorPermissions = [
            'view-applications',
            'review-applications',
            'view-kbli',
            'view-news',
            'download-documents',
            'application.view',
            'application.review',
            'dashboard.view',
            'notification.view'
        ];
        $operator->syncPermissions($operatorPermissions);

        $userPermissions = [
            'application.view', 
            'application.create', 
            'notification.view'
        ];
        $userRole->syncPermissions($userPermissions);

        // 3. Create default system accounts
        $accounts = [
            [
                'role_name' => 'Super Admin',
                'name' => 'superadmin',
                'email' => 'superadmin@neo-kebuli.local',
                'password_env' => 'INITIAL_SUPERADMIN_PASSWORD'
            ],
            [
                'role_name' => 'Admin',
                'name' => 'admin',
                'email' => 'admin@neo-kebuli.local',
                'password_env' => 'INITIAL_ADMIN_PASSWORD'
            ],
            [
                'role_name' => 'Operator',
                'name' => 'operator',
                'email' => 'operator@neo-kebuli.local',
                'password_env' => 'INITIAL_OPERATOR_PASSWORD'
            ],
            [
                'role_name' => 'user',
                'name' => 'user',
                'email' => 'user@neo-kebuli.local',
                'password_env' => 'INITIAL_USER_PASSWORD'
            ],
        ];

        foreach ($accounts as $account) {
            $roleName = $account['role_name'];
            $role = Role::where('name', $roleName)->first();

            $existingEmail = User::where('email', $account['email'])->first();
            $existingName = User::where('name', $account['name'])->where('email', '!=', $account['email'])->first();

            if ($existingName) {
                throw new \Exception("Data conflict: Username {$account['name']} is already taken by a different email.");
            }

            if ($existingEmail && $existingEmail->name !== $account['name']) {
                throw new \Exception("Data conflict: Email {$account['email']} is already taken by a different username ({$existingEmail->name}).");
            }

            $password = env($account['password_env']);
            if (!$password) {
                throw new \Exception("Missing environment variable for password: {$account['password_env']}");
            }

            // Optional: check complexity
            if (strlen($password) < 8 || !preg_match('/[a-zA-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
                throw new \Exception("Password in {$account['password_env']} must be at least 8 characters long and contain both letters and numbers.");
            }

            $user = User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );
            
            if ($user->wasRecentlyCreated) {
                $user->roles()->sync([$role->id]);
            } else {
                if (!$user->roles()->where('roles.id', $role->id)->exists()) {
                    throw new \Exception("Data conflict: Default system account {$account['email']} already exists but has a different role. Cannot take over safely.");
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Default system accounts are considered permanent reference data,
        // rolling back should not automatically delete them to prevent data loss 
        // in case they have relational data attached.
    }
};
