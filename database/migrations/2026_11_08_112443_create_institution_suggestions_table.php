<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_suggestions', function (Blueprint $table) {
            $table->id();
            $table->string('institution_name');
            $table->foreignId('institution_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
            $table->string('location')->nullable();
            $table->string('website')->nullable();
            $table->string('submitter_name');
            $table->string('submitter_email');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_suggestions');
    }
};
