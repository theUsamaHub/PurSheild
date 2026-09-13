<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained('pets')->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vet_id')->constrained('users')->cascadeOnDelete();
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->text('reason')->nullable();
            $table->enum('status', ['pending','approved','rescheduled','completed','cancelled','rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['vet_id', 'appointment_date']);
            $table->index(['owner_id', 'appointment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
