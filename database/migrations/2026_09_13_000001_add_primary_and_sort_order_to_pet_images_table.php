<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pet_images', function (Blueprint $table) {
            $table->boolean('is_primary')->default(false)->after('image_path');
            $table->unsignedInteger('sort_order')->default(0)->after('is_primary');
        });
    }

    public function down(): void
    {
        Schema::table('pet_images', function (Blueprint $table) {
            $table->dropColumn(['is_primary', 'sort_order']);
        });
    }
};
