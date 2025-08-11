<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentShareableLink', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('documentId')->nullable(false);
            $table->foreign('documentId')->references('id')->on('documents');
            $table->dateTime('linkExpiryTime')->nullable();
            $table->string('password')->nullable();
            $table->string('linkCode')->nullable();
            $table->boolean('isAllowDownload');
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
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documentShareableLink');
    }
};
