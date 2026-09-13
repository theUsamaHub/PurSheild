<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained('pets')->cascadeOnDelete();
            $table->string('provider_name')->nullable();
            $table->string('policy_number')->nullable();
            $table->date('start_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('policy_document')->nullable();
            $table->string('claim_document')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_policies');
    }
};
