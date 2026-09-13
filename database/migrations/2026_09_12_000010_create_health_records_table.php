<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained('pets')->cascadeOnDelete();
            $table->foreignId('vet_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('record_date');
            $table->string('record_type')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['pet_id', 'record_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_records');
    }
};
