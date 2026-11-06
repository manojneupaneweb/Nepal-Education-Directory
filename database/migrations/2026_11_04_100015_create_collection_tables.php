<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_source_id')->constrained()->restrictOnDelete();
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending')->index();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('total_discovered')->default(0);
            $table->unsignedInteger('total_processed')->default(0);
            $table->unsignedInteger('total_failed')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('collection_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('data_source_id')->constrained()->restrictOnDelete();
            $table->string('source_url', 500);
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'scheduled_at']);
        });

        Schema::create('source_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_job_id')->constrained()->cascadeOnDelete();
            $table->string('document_type', 50);
            $table->string('source_url', 500);
            $table->string('storage_path')->nullable();
            $table->char('content_hash', 64)->nullable()->unique();
            $table->enum('extraction_status', ['pending', 'completed', 'failed', 'skipped'])->default('pending')->index();
            $table->timestamp('fetched_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_extraction_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_document_id')->constrained()->cascadeOnDelete();
            $table->string('provider', 50);
            $table->string('model', 100);
            $table->string('prompt_version', 50);
            $table->json('raw_response')->nullable();
            $table->json('parsed_data')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending')->index();
            $table->decimal('confidence_score', 5, 4)->nullable();
            $table->json('token_usage')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_extraction_results');
        Schema::dropIfExists('source_documents');
        Schema::dropIfExists('collection_jobs');
        Schema::dropIfExists('collection_runs');
    }
};
