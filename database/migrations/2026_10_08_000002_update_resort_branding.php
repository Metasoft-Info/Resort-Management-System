<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

return new class extends Migration {
    public function up(): void
    {
        $branding = ['resort_name' => 'Tufan Resort', 'phone' => '01958216728', 'updated_at' => now()];
        if (DB::table('resort_info')->exists()) {
            DB::table('resort_info')->update($branding);
        } else {
            DB::table('resort_info')->insert($branding + ['created_at' => now()]);
        }
        Cache::forget('global_resort_info');
    }

    public function down(): void
    {
        // Keep the client's current name/contact when reverting schema changes.
    }
};
