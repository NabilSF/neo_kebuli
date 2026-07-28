<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Seeding order matters due to foreign key constraints:
     * 1. RoleSeeder          — must exist before AdminSeeder assigns roles
     * 2. PermissionSeeder    — must exist before role assignments
     * 3. AdminSeeder         — depends on roles
     * 4. NewsCategorySeeder  — independent
     * 5. KbliSeeder          — independent (categories → sections → items)
     *
     * All seeders are idempotent. Running `php artisan db:seed` multiple
     * times will NOT create duplicate data.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            AdminSeeder::class,
            NewsCategorySeeder::class,
            KbliSeeder::class,
        ]);
    }
}
