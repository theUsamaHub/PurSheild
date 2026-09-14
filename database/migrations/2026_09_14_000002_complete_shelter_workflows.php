<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adoption_applications', function (Blueprint $table) {
            $table->enum('status', ['pending', 'reviewing', 'approved', 'rejected', 'completed'])->default('pending')->change();
            $table->timestamp('decided_at')->nullable();
            $table->timestamp('completed_at')->nullable();
        });
        Schema::table('adoption_listings', function (Blueprint $table) {
            $table->string('health_state')->default('healthy')->index();
        });
        Schema::table('care_status_logs', function (Blueprint $table) {
            $table->enum('type', ['feeding', 'grooming', 'medical', 'vaccination', 'other'])->change();
            $table->time('log_time')->nullable();
            $table->string('staff_name')->nullable();
            $table->string('status')->default('completed')->index();
            $table->date('due_date')->nullable();
        });
        DB::table('adoption_listings')->where(function ($q) {
            $q->whereRaw('LOWER(health_status) LIKE ?', ['%treatment%'])->orWhereRaw('LOWER(health_status) LIKE ?', ['%recover%']);
        })->update(['health_state' => 'under_treatment']);
        DB::table('adoption_listings')->whereRaw('LOWER(health_status) LIKE ?', ['%vaccination due%'])->update(['health_state' => 'vaccination_due']);
        DB::table('adoption_applications')->whereIn('status', ['approved', 'rejected', 'completed'])->update(['decided_at' => DB::raw('updated_at')]);
        DB::table('adoption_applications')->where('status', 'completed')->update(['completed_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        DB::table('adoption_applications')->where('status', 'reviewing')->update(['status' => 'pending']);
        DB::table('care_status_logs')->where('type', 'vaccination')->update(['type' => 'medical']);
        Schema::table('adoption_applications', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending')->change();
            $table->dropColumn(['decided_at', 'completed_at']);
        });
        Schema::table('adoption_listings', fn (Blueprint $table) => $table->dropColumn('health_state'));
        Schema::table('care_status_logs', function (Blueprint $table) {
            $table->enum('type', ['feeding', 'grooming', 'medical', 'other'])->change();
            $table->dropColumn(['log_time', 'staff_name', 'status', 'due_date']);
        });
    }
};
