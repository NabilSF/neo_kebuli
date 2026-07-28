<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * No-op: columns added here (user_id, product_name, product_material,
     * production_process, saker_address, additional_information) are now
     * defined in the canonical 0001_01_01_000000 foundational migration.
     *
     * Preserved to maintain migration batch history on all existing databases.
     */
    public function up(): void
    {
        // No-op: applications table schema is fully defined in 0001_01_01_000000.
    }

    public function down(): void
    {
        // No-op.
    }
};
