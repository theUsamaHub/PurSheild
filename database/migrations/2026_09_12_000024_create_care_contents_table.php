<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('care_contents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('category', ['feeding','hygiene','exercise','health','training']);
            $table->enum('content_type', ['article','video','faq']);
            $table->longText('content')->nullable();
            $table->string('media_url')->nullable();
            $table->string('thumbnail')->nullable();
            $table->enum('status', ['active','inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('care_contents');
    }
};
