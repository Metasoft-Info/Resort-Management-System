<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'company_name')) {
                $table->string('company_name')->nullable()->after('customer_address');
            }
            if (!Schema::hasColumn('bookings', 'bkash_number')) {
                $table->string('bkash_number')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('bookings', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('bkash_number');
            }
            if (!Schema::hasColumn('bookings', 'extra_charges_data')) {
                $table->json('extra_charges_data')->nullable()->after('extra_charges_description');
            }
            if (!Schema::hasColumn('bookings', 'discount_reference')) {
                $table->string('discount_reference')->nullable()->after('discount_type');
            }
        });

        Schema::table('additional_guests', function (Blueprint $table) {
            if (!Schema::hasColumn('additional_guests', 'company_name')) {
                $table->string('company_name')->nullable()->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'bkash_number', 'bank_name', 'extra_charges_data', 'discount_reference']);
        });
        Schema::table('additional_guests', function (Blueprint $table) {
            $table->dropColumn('company_name');
        });
    }
};
