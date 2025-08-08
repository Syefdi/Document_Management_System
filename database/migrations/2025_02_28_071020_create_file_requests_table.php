<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('fileRequests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('subject');
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->integer('maxDocument')->nullable();
            $table->integer('sizeInMb')->nullable();
            $table->string('allowExtension');
            $table->tinyInteger('fileRequestStatus')->default(0); // Enum (CREATED = 0, UPLOADED = 1)
            $table->dateTime('linkExpiryTime')->nullable();
            $table->boolean('isLinkExpired')->default(false);
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

    public function down()
    {
        Schema::dropIfExists('fileRequests');
    }
};
