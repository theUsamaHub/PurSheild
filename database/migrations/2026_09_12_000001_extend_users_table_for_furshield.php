<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('profile_image')->nullable()->after('address');
            $table->enum('status', ['active', 'inactive', 'suspended', 'pending_verification'])->default('active')->after('profile_image');
            $table->timestamp('suspended_at')->nullable()->after('status');
            $table->text('suspension_reason')->nullable()->after('suspended_at');
            $table->timestamp('last_login_at')->nullable()->after('suspension_reason');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'address', 'profile_image', 'status',
                'suspended_at', 'suspension_reason', 'last_login_at',
            ]);
        });
    }
};
