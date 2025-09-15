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
        // Modify the id column from auto-increment bigint to varchar(36) for UUID
        DB::statement('ALTER TABLE racks MODIFY id VARCHAR(36) NOT NULL');
        DB::statement('ALTER TABLE racks DROP PRIMARY KEY');
        DB::statement('ALTER TABLE racks ADD PRIMARY KEY (id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to auto-increment bigint
        DB::statement('ALTER TABLE racks DROP PRIMARY KEY');
        DB::statement('ALTER TABLE racks MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE racks ADD PRIMARY KEY (id)');
    }
};
