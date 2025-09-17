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
        // The problematic composite unique constraint has already been removed
        // MySQL doesn't support partial unique indexes with WHERE clause
        // We'll rely on application-level validation for uniqueness among active records
        // This allows multiple deleted records with the same name
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the composite unique constraint
        Schema::table('locations', function (Blueprint $table) {
            $table->unique(['name', 'isDeleted'], 'locations_name_isdeleted_unique');
        });
    }
};
