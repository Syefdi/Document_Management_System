<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('racks', function (Blueprint $table) {
            // Drop the existing unique constraint on name
            $table->dropUnique('racks_name_unique');
            // Create a composite unique constraint that includes isDeleted
            // This allows same name for active (isDeleted=0) and deleted (isDeleted=1) records
            $table->unique(['name', 'isDeleted'], 'racks_name_isdeleted_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('racks', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('racks_name_isdeleted_unique');
            // Restore the original unique constraint
            $table->unique('name', 'racks_name_unique');
        });
    }
};
