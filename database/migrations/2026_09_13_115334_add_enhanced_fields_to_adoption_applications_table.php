<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adoption_applications', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('message');
            $table->string('address')->nullable()->after('phone');
            $table->string('home_type')->nullable()->after('address');
            $table->boolean('has_yard')->nullable()->after('home_type');
            $table->string('living_situation')->nullable()->after('has_yard');
            $table->boolean('has_other_pets')->nullable()->after('living_situation');
            $table->text('other_pets_details')->nullable()->after('has_other_pets');
            $table->boolean('has_children')->nullable()->after('other_pets_details');
            $table->string('children_ages')->nullable()->after('has_children');
            $table->string('work_schedule')->nullable()->after('children_ages');
            $table->text('pet_experience')->nullable()->after('work_schedule');
            $table->text('why_adopt')->nullable()->after('pet_experience');
        });
    }

    public function down(): void
    {
        Schema::table('adoption_applications', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'address', 'home_type', 'has_yard', 'living_situation',
                'has_other_pets', 'other_pets_details', 'has_children', 'children_ages',
                'work_schedule', 'pet_experience', 'why_adopt',
            ]);
        });
    }
};
