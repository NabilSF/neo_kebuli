<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * No-op: the legacy PII columns (applicant_*, business_*, risk_level,
     * classification, licensing_requirement, is_active) this migration would
     * drop were never included in the canonical 0001_01_01_000000 foundational
     * migration. Preserved for migration batch history.
     */
    public function up(): void
    {
        // No-op: legacy columns do not exist on fresh installations.
    }

    public function down(): void
    {
        // No-op.
    }
};
