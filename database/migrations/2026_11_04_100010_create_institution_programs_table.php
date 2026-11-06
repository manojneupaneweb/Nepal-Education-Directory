<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->constrained()->restrictOnDelete();
            $table->string('duration', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('admission_information')->nullable();
            $table->foreignId('source_id')->nullable()->constrained('data_sources')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['institution_id', 'program_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_programs');
    }
};
