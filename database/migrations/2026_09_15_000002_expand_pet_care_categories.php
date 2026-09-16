<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema,DB};
return new class extends Migration {
    public function up(): void {
        Schema::table('care_contents', fn(Blueprint $table)=>$table->enum('category',['feeding','grooming','hygiene','exercise','vaccination','health','training'])->change());
    }
    public function down(): void {
        DB::table('care_contents')->where('category','grooming')->update(['category'=>'hygiene']);
        DB::table('care_contents')->where('category','vaccination')->update(['category'=>'health']);
        Schema::table('care_contents', fn(Blueprint $table)=>$table->enum('category',['feeding','hygiene','exercise','health','training'])->change());
    }
};
