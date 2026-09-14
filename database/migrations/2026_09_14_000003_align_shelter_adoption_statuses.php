<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Reflect decisions that already exist; do not create or change applications.
        DB::transaction(function () {
            DB::table('adoption_listings')->whereIn('id', function ($query) {
                $query->select('listing_id')->from('adoption_applications')->where('status', 'completed');
            })->update(['status' => 'adopted']);

            DB::table('adoption_listings')->where('status', 'available')->whereIn('id', function ($query) {
                $query->select('listing_id')->from('adoption_applications')->where('status', 'approved');
            })->update(['status' => 'pending']);
        });
    }

    public function down(): void
    {
        // Existing adoption decisions remain valid when the schema is rolled back.
    }
};
