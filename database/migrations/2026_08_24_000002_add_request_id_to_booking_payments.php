<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('booking_payments') || Schema::hasColumn('booking_payments', 'request_id')) {
            return;
        }

        Schema::table('booking_payments', function (Blueprint $table) {
            $table->uuid('request_id')->nullable()->after('recorded_by_id');
            $table->unique(
                ['booking_id', 'request_id'],
                'booking_payments_booking_request_unique'
            );
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('booking_payments') || ! Schema::hasColumn('booking_payments', 'request_id')) {
            return;
        }

        Schema::table('booking_payments', function (Blueprint $table) {
            $table->dropUnique('booking_payments_booking_request_unique');
            $table->dropColumn('request_id');
        });
    }
};
