<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            try {
                $table->dropIndex(['is_active', 'published_at']);
            } catch (\Throwable $e) {}
            try {
                $table->dropIndex(['is_active', 'unpublish_at']);
            } catch (\Throwable $e) {}
            try {
                $table->dropIndex(['published_at']);
            } catch (\Throwable $e) {}
            try {
                $table->dropIndex(['unpublish_at']);
            } catch (\Throwable $e) {}
            if (Schema::hasColumn('categories', 'published_at')) {
                $table->dropColumn(['published_at', 'unpublish_at']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable()->after('body');
            $table->timestamp('unpublish_at')->nullable()->after('published_at');
        });
    }
};