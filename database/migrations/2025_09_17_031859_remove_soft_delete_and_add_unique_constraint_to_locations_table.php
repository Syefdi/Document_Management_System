<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            // Check if unique constraint doesn't exist before adding it
            $indexExists = DB::select("SHOW INDEX FROM locations WHERE Key_name = 'locations_name_unique'");
            if (empty($indexExists)) {
                $table->unique('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            // Drop unique constraint
            $table->dropUnique(['name']);
        });
    }
};
