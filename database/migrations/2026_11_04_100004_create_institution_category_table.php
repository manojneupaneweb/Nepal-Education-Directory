<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institution_category_id')->constrained()->restrictOnDelete();
            $table->timestamps();

            $table->unique(['institution_id', 'institution_category_id'], 'institution_category_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_category');
    }
};
