<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->enum('contact_type', [
                'phone',
                'email',
                'website',
                'facebook',
                'instagram',
                'youtube',
                'other',
            ]);
            $table->string('label')->nullable();
            $table->string('contact_value');
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_public')->default(true);
            $table->foreignId('source_id')->nullable()->constrained('data_sources')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['institution_id', 'contact_type', 'contact_value'], 'institution_contacts_value_unique');
            $table->index(['institution_id', 'contact_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_contacts');
    }
};
