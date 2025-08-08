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
        Schema::create('openaiDocuments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('prompt');
            $table->text('model');
            $table->text('language')->nullable();
            $table->integer('maximumLength')->nullable();
            $table->decimal('creativity', 18, 2)->nullable();
            $table->text('toneOfVoice')->nullable();
            $table->longText('response')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('openaiDocuments');
    }
};
