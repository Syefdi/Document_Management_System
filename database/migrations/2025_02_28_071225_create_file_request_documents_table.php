<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('fileRequestDocuments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('url');
            $table->uuid('fileRequestId');
            $table->tinyInteger('fileRequestDocumentStatus')->default(0); // Enum (PENDING = 0, APPROVED = 1, REJECTED = 2)
            $table->dateTime('approvedRejectedDate')->nullable();
            $table->uuid('approvalOrRejectedById')->nullable();
            $table->text('reason')->nullable();
            $table->dateTime('createdDate');
            $table->foreign('fileRequestId')->references('id')->on('fileRequests');
            $table->foreign('approvalOrRejectedById')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fileRequestDocuments');
    }
};
