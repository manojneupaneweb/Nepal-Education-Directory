<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        Schema::table('institution_types', function (Blueprint $table) {
            $table->string('name_ne')->nullable()->after('name');
        });

        Schema::table('institution_categories', function (Blueprint $table) {
            $table->string('name_ne')->nullable()->after('name');
            $table->foreignId('parent_id')->nullable()->after('slug')->constrained('institution_categories')->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('description');
            $table->unsignedSmallInteger('sort_order')->default(0)->after('is_active');
        });

        Schema::table('institutions', function (Blueprint $table) {
            $table->enum('management_type', [
                'government_managed',
                'community_managed',
                'privately_managed',
                'trust_managed',
                'other',
                'unknown',
            ])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->string('management_type', 100)->nullable()->change();
        });

        Schema::table('institution_categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
            $table->dropColumn(['name_ne', 'is_active', 'sort_order']);
        });

        Schema::table('institution_types', function (Blueprint $table) {
            $table->dropColumn('name_ne');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
