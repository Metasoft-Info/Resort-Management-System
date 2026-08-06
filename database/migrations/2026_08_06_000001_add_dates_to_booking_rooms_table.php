<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('booking_rooms')) {
            if (!Schema::hasColumn('booking_rooms', 'check_in_date')) {
                Schema::table('booking_rooms', function (Blueprint $table) {
                    $table->date('check_in_date')->nullable()->after('price_per_night');
                    $table->date('check_out_date')->nullable()->after('check_in_date');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('booking_rooms')) {
            Schema::table('booking_rooms', function (Blueprint $table) {
                $table->dropColumn(['check_in_date', 'check_out_date']);
            });
        }
    }
};
