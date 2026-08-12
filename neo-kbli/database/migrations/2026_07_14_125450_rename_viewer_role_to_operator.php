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
        $operatorExists = DB::table('roles')->where('name', 'Operator')->exists();
        if (!$operatorExists) {
            DB::table('roles')
                ->where('name', 'Viewer')
                ->update([
                    'name' => 'Operator',
                ]);
        } else {
            DB::table('roles')->where('name', 'Viewer')->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')
            ->where('name', 'Operator')
            ->update([
                'name' => 'Viewer',
            ]);
    }
};
