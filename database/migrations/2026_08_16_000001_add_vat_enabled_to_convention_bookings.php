<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('convention_bookings', 'vat_enabled')) {
            Schema::table('convention_bookings', function (Blueprint $table) {
                $table->boolean('vat_enabled')->default(false)->after('vat_percentage');
            });
        }

        // Preserve the financial state of old records. New and updated records
        // use the explicit checkbox value instead of inferring it from the rate.
        DB::table('convention_bookings')
            ->where('vat_amount', '>', 0)
            ->where('vat_percentage', '>', 0)
            ->update(['vat_enabled' => true]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('convention_bookings', 'vat_enabled')) {
            Schema::table('convention_bookings', function (Blueprint $table) {
                $table->dropColumn('vat_enabled');
            });
        }
    }
};
