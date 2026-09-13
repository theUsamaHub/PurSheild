<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adoption_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shelter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('species_id')->constrained('species')->restrictOnDelete();
            $table->foreignId('breed_id')->nullable()->constrained('breeds')->nullOnDelete();
            $table->string('pet_name');
            $table->string('age')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('health_status')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['available','pending','adopted','inactive'])->default('available');
            $table->timestamps();
            $table->index(['species_id', 'breed_id']);
            $table->index(['shelter_id', 'status']);
        });

        Schema::create('adoption_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained('adoption_listings')->cascadeOnDelete();
            $table->string('image_path');
            $table->string('caption')->nullable();
            $table->timestamps();
        });

        Schema::create('adoption_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained('adoption_listings')->cascadeOnDelete();
            $table->foreignId('applicant_id')->constrained('users')->cascadeOnDelete();
            $table->text('message')->nullable();
            $table->enum('status', ['pending','approved','rejected','completed'])->default('pending');
            $table->text('shelter_response')->nullable();
            $table->timestamps();
            $table->index(['listing_id', 'status']);
            $table->index(['applicant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adoption_applications');
        Schema::dropIfExists('adoption_images');
        Schema::dropIfExists('adoption_listings');
    }
};
