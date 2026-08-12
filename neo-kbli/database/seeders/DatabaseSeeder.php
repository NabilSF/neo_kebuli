<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Initialize system data directly via migrations.
     * Use factory states or dedicated test classes for tests.
     * All seeders are idempotent. Running `php artisan db:seed` multiple
     * times will NOT create duplicate data.
     */
    public function run(): void
    {
        // Production initialization is handled via migrations.
        // Tests needing specific data should setup their own data via factories or explicit seeders.
    }
}
