<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('data_source_id')->constrained()->restrictOnDelete();
            $table->string('source_url', 500);
            $table->string('source_identifier', 191)->nullable();
            $table->timestamp('collected_at')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['institution_id', 'data_source_id', 'source_url'],
                'institution_sources_page_unique'
            );
            $table->index(['data_source_id', 'source_identifier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_sources');
    }
};
