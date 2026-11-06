<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_field_evidence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institution_source_id')->nullable()->constrained()->nullOnDelete();
            $table->string('field_name', 100);
            $table->text('extracted_value')->nullable();
            $table->text('normalized_value')->nullable();
            $table->decimal('confidence', 5, 4)->nullable();
            $table->enum('review_status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['institution_id', 'field_name', 'review_status'], 'field_evidence_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_field_evidence');
    }
};
