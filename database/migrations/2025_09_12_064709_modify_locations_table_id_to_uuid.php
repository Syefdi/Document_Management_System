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
        // First, we need to handle this with raw SQL due to MySQL limitations
        DB::statement('ALTER TABLE locations MODIFY id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE locations DROP PRIMARY KEY');
        DB::statement('ALTER TABLE locations MODIFY id VARCHAR(36) NOT NULL');
        DB::statement('ALTER TABLE locations ADD PRIMARY KEY (id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            // Revert back to auto-increment integer
            $table->dropPrimary('id');
            $table->id()->change();
        });
    }
};
