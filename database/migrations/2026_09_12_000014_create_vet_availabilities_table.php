<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vet_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vet_id')->constrained('users')->cascadeOnDelete();
            $table->enum('day_of_week', ['monday','tuesday','wednesday','thursday','friday','saturday','sunday']);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->index(['vet_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vet_availabilities');
    }
};
