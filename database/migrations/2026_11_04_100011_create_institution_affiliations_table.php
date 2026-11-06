<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_affiliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('university_id')->nullable()->constrained()->restrictOnDelete();
            $table->enum('affiliation_type', ['affiliated', 'constituent', 'other']);
            $table->string('affiliation_number', 100)->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->enum('status', ['active', 'inactive', 'expired', 'pending'])->default('pending');
            $table->foreignId('source_id')->nullable()->constrained('data_sources')->restrictOnDelete();
            $table->timestamps();

            $table->index(['institution_id', 'status']);
            $table->index(['university_id', 'affiliation_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_affiliations');
    }
};
