<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vet_profiles', function (Blueprint $t) {
            $t->string('city')->nullable();
            $t->decimal('latitude', 10, 7)->nullable();
            $t->decimal('longitude', 10, 7)->nullable();
            $t->boolean('online_consultation')->default(false);
            $t->boolean('emergency_services')->default(false);
        });
        Schema::table('care_contents', fn (Blueprint $t) => $t->unsignedBigInteger('views_count')->default(0));
        if (! Schema::hasColumn('medical_documents', 'mime_type')) {
            Schema::table('medical_documents', fn (Blueprint $t) => $t->string('mime_type')->nullable());
        }
        Schema::create('owner_favorites', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('kind', 10);
            $t->unsignedBigInteger('target_id');
            $t->timestamps();
            $t->unique(['user_id', 'kind', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('owner_favorites');
        Schema::table('vet_profiles', fn (Blueprint $t) => $t->dropColumn(['city', 'latitude', 'longitude', 'online_consultation', 'emergency_services']));
        Schema::table('care_contents', fn (Blueprint $t) => $t->dropColumn('views_count'));
    }
};
