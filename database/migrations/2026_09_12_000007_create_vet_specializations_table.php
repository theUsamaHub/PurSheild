<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vet_specializations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vet_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('specialization_id')->constrained('specializations')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['vet_id', 'specialization_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vet_specializations');
    }
};
