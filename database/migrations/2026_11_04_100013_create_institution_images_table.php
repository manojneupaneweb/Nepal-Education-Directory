<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->enum('image_type', ['logo', 'campus', 'classroom', 'infrastructure', 'other']);
            $table->string('file_path');
            $table->string('alt_text')->nullable();
            $table->string('alt_text_ne')->nullable();
            $table->string('caption')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('source_id')->nullable()->constrained('data_sources')->restrictOnDelete();
            $table->timestamps();

            $table->index(['institution_id', 'image_type', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_images');
    }
};
