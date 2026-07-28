<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $operatorExists = DB::table('roles')->where('slug', 'operator')->exists();
        if (!$operatorExists) {
            DB::table('roles')
                ->where('slug', 'viewer')
                ->update([
                    'slug' => 'operator',
                    'name' => 'Operator',
                ]);
        } else {
            // If operator exists, and viewer also exists, we might want to delete viewer to avoid duplicates or re-assign users.
            // For now, just delete viewer since we can't rename it to operator.
            DB::table('roles')->where('slug', 'viewer')->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')
            ->where('slug', 'operator')
            ->update([
                'slug' => 'viewer',
                'name' => 'Viewer',
            ]);
    }
};
