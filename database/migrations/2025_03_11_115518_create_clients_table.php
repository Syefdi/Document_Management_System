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
        Schema::create('clients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('companyName');
            $table->string('contactPerson')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('phoneNumber')->nullable();
            $table->text('address')->nullable();
            $table->uuid('createdBy')->nullable(false);
            $table->foreign('createdBy')->references('id')->on('users');
            $table->string('modifiedBy');
            $table->string('deletedBy');
            $table->boolean('isDeleted');
            $table->dateTime('createdDate');
            $table->dateTime('modifiedDate');
            $table->softDeletes()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
