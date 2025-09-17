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
        // Drop any existing constraints using proper MySQL syntax
        try {
            DB::statement('ALTER TABLE racks DROP INDEX racks_name_isdeleted_unique');
        } catch (Exception $e) {
            // Index might not exist
        }
        
        try {
            DB::statement('ALTER TABLE racks DROP INDEX racks_name_unique_active');
        } catch (Exception $e) {
            // Index might not exist
        }
        
        try {
            DB::statement('DROP INDEX racks_name_unique_active_only ON racks');
        } catch (Exception $e) {
            // Index might not exist
        }
        
        // For MySQL 8.0, we need to use a different approach
        // Create a unique index that only applies to active records
        // We'll use a functional index with a CASE expression
        DB::statement('CREATE UNIQUE INDEX racks_name_unique_active_only ON racks ((CASE WHEN isDeleted = 0 THEN name ELSE CONCAT(name, "_", id) END))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the partial unique index
        DB::statement('DROP INDEX IF EXISTS racks_name_unique_active_only ON racks');
        
        Schema::table('racks', function (Blueprint $table) {
            // Restore original constraint if needed
            try {
                $table->unique(['name', 'isDeleted'], 'racks_name_isdeleted_unique');
            } catch (Exception $e) {
                // Constraint might already exist
            }
        });
    }
};
