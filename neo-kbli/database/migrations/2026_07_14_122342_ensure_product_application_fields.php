<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * No-op: product_name, product_material, production_process,
     * product_description, and saker_address are defined in the canonical
     * 0001_01_01_000000 foundational migration. Preserved for history.
     */
    public function up(): void
    {
        // No-op: product fields are defined in 0001_01_01_000000.
    }

    public function down(): void
    {
        // No-op.
    }
};
