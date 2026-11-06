<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('institution_verifications')) {
            Schema::table('institution_verifications', function (Blueprint $table) {
                $table->index(['institution_id', 'verification_status'], 'institution_verifications_lookup');
            });
        } else {
            Schema::create('institution_verifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
                $table->string('verification_type', 50);
                $table->enum('verification_status', [
                    'unverified',
                    'source_matched',
                    'admin_reviewed',
                    'institution_verified',
                ]);
                $table->json('evidence')->nullable();
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();

                $table->index(['institution_id', 'verification_status'], 'institution_verifications_lookup');
            });
        }

        Schema::create('institution_change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('field_name', 100);
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->string('change_reason')->nullable();
            $table->timestamps();

            $table->index(['institution_id', 'field_name'], 'institution_change_logs_lookup');
        });

        Schema::create('institution_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('claimant_name');
            $table->string('claimant_email');
            $table->string('claimant_role', 100)->nullable();
            $table->json('evidence')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['institution_id', 'status'], 'institution_claims_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_claims');
        Schema::dropIfExists('institution_change_logs');
        Schema::dropIfExists('institution_verifications');
    }
};
