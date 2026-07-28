<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * No-op: the media table is created with the correct schema (including
     * checksum, without the legacy url column) in the canonical
     * 0001_01_01_000000 foundational migration. Preserved for history.
     */
    public function up(): void
    {
        // No-op: media table with correct schema defined in 0001_01_01_000000.
    }

    public function down(): void
    {
        // No-op.
    }
};
