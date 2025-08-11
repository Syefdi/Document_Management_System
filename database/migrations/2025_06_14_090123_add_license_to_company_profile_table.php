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
        Schema::table('companyProfile', function (Blueprint $table) {
            $table->string('licenseKey')->nullable();
            $table->string('purchaseCode')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companyProfile', function (Blueprint $table) {
            $table->dropColumn('licenseKey');
            $table->dropColumn('purchaseCode');
        });
    }
};
