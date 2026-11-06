<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->string('abbreviation', 50)->nullable()->after('name_ne');
            $table->json('alternative_names')->nullable()->after('abbreviation');
            $table->string('ownership', 50)->nullable()->after('university_type');
            $table->string('ugc_category', 150)->nullable()->after('ownership');
            $table->string('established_year_bs', 10)->nullable()->after('ugc_category');
            $table->unsignedSmallInteger('established_year_ad')->nullable()->after('established_year_bs');
            $table->text('history')->nullable()->after('description');
            $table->text('vision')->nullable();
            $table->text('mission')->nullable();
            $table->text('objectives')->nullable();
            $table->string('recognition_status', 100)->nullable();
            $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('municipality_id')->nullable()->constrained()->nullOnDelete();
            $table->string('location_label')->nullable();
            $table->string('address')->nullable();
            $table->string('postal_address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('contact_page_url')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('logo_path')->nullable();
            $table->json('social_links')->nullable();
            $table->text('facilities')->nullable();
            $table->text('admission_information')->nullable();
            $table->text('scholarship_information')->nullable();
            $table->text('examination_information')->nullable();
            $table->text('academic_calendar')->nullable();
            $table->text('research_summary')->nullable();
            $table->unsignedInteger('ugc_constituent_colleges')->nullable();
            $table->unsignedInteger('ugc_affiliated_colleges')->nullable();
            $table->unsignedInteger('ugc_community_colleges')->nullable();
            $table->unsignedInteger('ugc_total_colleges')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->string('verification_status', 40)->default('unverified');
            $table->boolean('is_aggregate')->default(false);
            $table->timestamp('researched_at')->nullable();
            $table->index('university_type');
            $table->index('ownership');
            $table->index('verification_status');
        });

        Schema::create('university_faculties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('name_ne')->nullable();
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('source_url')->nullable();
            $table->string('verification_status', 40)->default('unverified');
            $table->timestamps();
            $table->unique(['university_id', 'slug']);
        });

        Schema::create('university_leaders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('designation');
            $table->string('profile_url')->nullable();
            $table->string('source_url')->nullable();
            $table->string('verification_status', 40)->default('unverified');
            $table->timestamps();
            $table->index(['university_id', 'designation']);
        });

        Schema::create('university_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->string('category', 40);
            $table->string('url');
            $table->string('title')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('retrieved_at')->nullable();
            $table->timestamps();
            $table->index(['university_id', 'category']);
        });

        Schema::create('university_research_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->string('status', 40);
            $table->string('approval_status', 40)->default('pending');
            $table->json('proposed_changes')->nullable();
            $table->text('summary')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['approval_status', 'university_id'], 'uni_research_status_idx');
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->foreignId('university_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('university_faculty_id')->nullable()->after('university_id')->constrained('university_faculties')->nullOnDelete();
            $table->string('discipline')->nullable()->after('duration');
            $table->text('eligibility')->nullable();
            $table->text('admission_requirements')->nullable();
            $table->string('specialization')->nullable();
            $table->string('curriculum_url')->nullable();
            $table->string('admission_url')->nullable();
            $table->string('program_status', 40)->default('listed');
            $table->string('source_url')->nullable();
            $table->string('verification_status', 40)->default('unverified');
            $table->string('seo_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->index('university_id');
            $table->index('discipline');
            $table->index('verification_status');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('university_faculty_id');
            $table->dropConstrainedForeignId('university_id');
            $table->dropColumn([
                'discipline',
                'eligibility',
                'admission_requirements',
                'specialization',
                'curriculum_url',
                'admission_url',
                'program_status',
                'source_url',
                'verification_status',
                'seo_title',
                'meta_description',
            ]);
        });

        Schema::dropIfExists('university_research_histories');
        Schema::dropIfExists('university_sources');
        Schema::dropIfExists('university_leaders');
        Schema::dropIfExists('university_faculties');

        Schema::table('universities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('municipality_id');
            $table->dropConstrainedForeignId('district_id');
            $table->dropConstrainedForeignId('province_id');
            $table->dropColumn([
                'abbreviation',
                'alternative_names',
                'ownership',
                'ugc_category',
                'established_year_bs',
                'established_year_ad',
                'history',
                'vision',
                'mission',
                'objectives',
                'recognition_status',
                'location_label',
                'address',
                'postal_address',
                'email',
                'phone',
                'contact_page_url',
                'latitude',
                'longitude',
                'logo_path',
                'social_links',
                'facilities',
                'admission_information',
                'scholarship_information',
                'examination_information',
                'academic_calendar',
                'research_summary',
                'ugc_constituent_colleges',
                'ugc_affiliated_colleges',
                'ugc_community_colleges',
                'ugc_total_colleges',
                'seo_title',
                'meta_description',
                'verification_status',
                'is_aggregate',
                'researched_at',
            ]);
        });
    }
};
