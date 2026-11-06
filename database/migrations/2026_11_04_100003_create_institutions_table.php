<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_institution_id')->nullable();
            $table->foreignId('institution_type_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('name_ne')->nullable();
            $table->string('short_name', 100)->nullable();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('description_ne')->nullable();
            $table->string('registration_number', 100)->nullable()->index();
            $table->string('registration_authority')->nullable();
            $table->unsignedSmallInteger('established_year')->nullable();
            $table->enum('ownership_type', [
                'government',
                'community',
                'private',
                'public',
                'nonprofit',
                'other',
                'unknown',
            ])->nullable();
            $table->string('management_type', 100)->nullable();
            $table->foreignId('province_id')->constrained()->restrictOnDelete();
            $table->foreignId('district_id')->constrained()->restrictOnDelete();
            $table->foreignId('municipality_id')->constrained()->restrictOnDelete();
            $table->foreignId('ward_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('address')->nullable();
            $table->string('address_ne')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('official_website')->nullable();
            $table->string('logo_path')->nullable();
            $table->enum('status', [
                'draft',
                'pending',
                'published',
                'rejected',
                'archived',
            ])->default('draft')->index();
            $table->enum('verification_status', [
                'unverified',
                'source_matched',
                'admin_reviewed',
                'institution_verified',
            ])->default('unverified')->index();
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'province_id']);
            $table->index(['status', 'district_id']);
            $table->index(['status', 'municipality_id']);
            $table->index(['status', 'verification_status']);
        });

        Schema::table('institutions', function (Blueprint $table) {
            $table->foreign('parent_institution_id')
                ->references('id')
                ->on('institutions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};
