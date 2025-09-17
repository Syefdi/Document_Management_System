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
            // Create a composite unique constraint that includes isDeleted
            // This allows same name for active (isDeleted=0) and deleted (isDeleted=1) records
            $table->unique(['name', 'isDeleted'], 'locations_name_isdeleted_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('locations_name_isdeleted_unique');
            // Restore the original unique constraint
            $table->unique('name');
        });
    }
};
