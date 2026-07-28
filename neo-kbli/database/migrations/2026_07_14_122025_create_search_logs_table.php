<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * No-op migration — schema ownership transferred to 2026_07_13_212200.
     *
     * This migration was created to correct the schema that 2026_07_13_212200
     * originally defined incorrectly. The first migration has since been
     * corrected. This entry is retained to preserve migration batch history
     * on all existing deployed databases.
     */
    public function up(): void
    {
        // No-op: search_logs table is created by 2026_07_13_212200.
    }

    /**
     * No-op: do not drop search_logs — it is owned by 2026_07_13_212200.
     */
    public function down(): void
    {
        // No-op.
    }
};
