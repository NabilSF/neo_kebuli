<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update the super admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@neokbli.local'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Admin12345!'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        // Assign a role if the Spatie permission package is installed
        if (method_exists($admin, 'assignRole')) {
            $admin->assignRole('super-admin');
        }
    }
}

