<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('booking_payments')) {
            return;
        }

        if (! Schema::hasColumn('booking_payments', 'payment_date')) {
            Schema::table('booking_payments', function (Blueprint $table) {
                $table->date('payment_date')->nullable()->after('request_id');
                $table->index(['booking_id', 'payment_date']);
            });
        }

        // Payment reports must use the date the cashier recorded the money,
        // not a UTC/server timestamp whose calendar date can differ locally.
        DB::table('booking_payments')
            ->whereNull('payment_date')
            ->orderBy('id')
            ->chunkById(500, function ($payments) {
                foreach ($payments as $payment) {
                    $date = $payment->created_at
                        ? substr((string) $payment->created_at, 0, 10)
                        : now('Asia/Dhaka')->toDateString();

                    DB::table('booking_payments')
                        ->where('id', $payment->id)
                        ->update(['payment_date' => $date]);
                }
            });
    }

    public function down(): void
    {
        if (! Schema::hasTable('booking_payments') || ! Schema::hasColumn('booking_payments', 'payment_date')) {
            return;
        }

        Schema::table('booking_payments', function (Blueprint $table) {
            $table->dropIndex(['booking_id', 'payment_date']);
            $table->dropColumn('payment_date');
        });
    }
};
