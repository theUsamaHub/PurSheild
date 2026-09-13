<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('care_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shelter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('listing_id')->nullable()->constrained('adoption_listings')->nullOnDelete();
            $table->string('animal_name');
            $table->enum('type', ['feeding', 'grooming', 'medical', 'other']);
            $table->text('notes');
            $table->date('log_date')->useCurrent();
            $table->timestamps();
            $table->index(['shelter_id', 'log_date']);
            $table->index(['shelter_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('care_status_logs');
    }
};
