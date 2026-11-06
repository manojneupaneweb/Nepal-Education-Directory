<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('facility_type_id')->constrained()->restrictOnDelete();
            $table->text('description')->nullable();
            $table->enum('availability_status', ['available', 'unavailable', 'unknown'])->default('unknown');
            $table->foreignId('source_id')->nullable()->constrained('data_sources')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['institution_id', 'facility_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_facilities');
    }
};
