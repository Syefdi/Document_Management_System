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
        Schema::table('locations', function (Blueprint $table) {
            $table->boolean('isDeleted')->default(0)->after('address');
            $table->string('createdBy')->nullable()->after('isDeleted');
            $table->string('modifiedBy')->nullable()->after('createdBy');
            $table->string('deletedBy')->nullable()->after('modifiedBy');
            $table->dateTime('createdDate')->nullable()->after('deletedBy');
            $table->dateTime('modifiedDate')->nullable()->after('createdDate');
            $table->softDeletes()->nullable()->after('modifiedDate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['isDeleted', 'createdBy', 'modifiedBy', 'deletedBy', 'createdDate', 'modifiedDate']);
            $table->dropSoftDeletes();
        });
    }
};