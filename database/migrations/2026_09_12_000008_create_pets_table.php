<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('species_id')->constrained('species')->restrictOnDelete();
            $table->foreignId('breed_id')->nullable()->constrained('breeds')->nullOnDelete();
            $table->string('name');
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('color')->nullable();
            $table->text('description')->nullable();
            $table->string('profile_image')->nullable();
            $table->boolean('is_neutered')->default(false);
            $table->string('microchip_number')->nullable();
            $table->decimal('adoption_fee', 8, 2)->nullable();
            $table->timestamps();
            $table->index('owner_id');
            $table->index('species_id');
            $table->index('breed_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
