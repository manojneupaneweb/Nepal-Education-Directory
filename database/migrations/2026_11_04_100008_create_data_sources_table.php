<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('source_type', [
                'government',
                'university',
                'educational_board',
                'institution_website',
                'municipality',
                'other',
            ]);
            $table->string('base_url')->nullable();
            $table->unsignedTinyInteger('authority_level')->default(0);
            $table->enum('access_method', ['manual', 'http', 'api', 'file']);
            $table->boolean('is_active')->default(true);
            $table->string('crawl_frequency', 50)->nullable();
            $table->timestamp('last_collected_at')->nullable();
            $table->json('configuration')->nullable();
            $table->timestamps();

            $table->index(['source_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_sources');
    }
};
