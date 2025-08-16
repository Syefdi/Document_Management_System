<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('allowFileExtensions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->tinyInteger('fileType'); // Enum representation
            $table->string('extensions');
        });
    }

    public function down()
    {
        Schema::dropIfExists('allowFileExtensions');
    }
};

