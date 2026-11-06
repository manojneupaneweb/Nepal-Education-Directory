<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ne');
            $table->string('slug')->unique();
            $table->string('code', 20)->nullable();
            $table->timestamps();
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('name_ne');
            $table->string('slug');
            $table->string('code', 20)->nullable();
            $table->timestamps();

            $table->unique('slug');
        });

        Schema::create('municipalities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('name_ne');
            $table->string('slug');
            $table->enum('municipality_type', [
                'metropolitan',
                'sub_metropolitan',
                'municipality',
                'rural_municipality',
            ]);
            $table->string('code', 20)->nullable();
            $table->timestamps();

            $table->unique(['district_id', 'slug']);
        });

        Schema::create('wards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipality_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('ward_number');
            $table->string('name')->nullable();
            $table->string('name_ne')->nullable();
            $table->timestamps();

            $table->unique(['municipality_id', 'ward_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wards');
        Schema::dropIfExists('municipalities');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('provinces');
    }
};
