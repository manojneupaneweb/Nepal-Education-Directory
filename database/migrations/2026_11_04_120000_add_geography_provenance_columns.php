<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('geographic_datasets', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('organization');
            $table->string('source_url');
            $table->string('version')->nullable();
            $table->string('reference_period')->nullable();
            $table->date('retrieved_on')->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->char('file_checksum', 64)->nullable();
            $table->json('levels');
            $table->string('validation_status', 50);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('geographic_aliases', function (Blueprint $table) {
            $table->id();
            $table->morphs('aliasable');
            $table->string('alias');
            $table->string('alias_ne')->nullable();
            $table->string('kind', 50);
            $table->foreignId('geographic_dataset_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->unique(['aliasable_type', 'aliasable_id', 'alias', 'kind'], 'geographic_aliases_unique');
        });

        foreach (['provinces', 'districts', 'municipalities'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('code')->index();
                $table->foreignId('geographic_dataset_id')
                    ->nullable()
                    ->after('is_active')
                    ->constrained()
                    ->nullOnDelete();
            });
        }

        Schema::table('wards', function (Blueprint $table) {
            $table->string('code', 20)->nullable()->after('ward_number');
            $table->boolean('is_active')->default(true)->after('code')->index();
            $table->foreignId('geographic_dataset_id')
                ->nullable()
                ->after('is_active')
                ->constrained()
                ->nullOnDelete();
            $table->unique('code');
        });

        Schema::table('provinces', function (Blueprint $table) {
            $table->string('name_ne')->nullable()->change();
            $table->unique('code');
        });

        Schema::table('districts', function (Blueprint $table) {
            $table->string('name_ne')->nullable()->change();
            $table->unique('code');
        });

        Schema::table('municipalities', function (Blueprint $table) {
            $table->unique('code');
        });
    }

    public function down(): void
    {
        Schema::table('wards', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropConstrainedForeignId('geographic_dataset_id');
            $table->dropColumn(['code', 'is_active']);
        });

        Schema::table('municipalities', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropConstrainedForeignId('geographic_dataset_id');
            $table->dropColumn('is_active');
        });

        Schema::table('districts', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropConstrainedForeignId('geographic_dataset_id');
            $table->dropColumn('is_active');
            $table->string('name_ne')->nullable(false)->change();
        });

        Schema::table('provinces', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropConstrainedForeignId('geographic_dataset_id');
            $table->dropColumn('is_active');
            $table->string('name_ne')->nullable(false)->change();
        });

        Schema::dropIfExists('geographic_aliases');
        Schema::dropIfExists('geographic_datasets');
    }
};
