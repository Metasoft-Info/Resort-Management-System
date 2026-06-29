<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change payment_method from enum to varchar to support bkash
        DB::statement("ALTER TABLE convention_bookings MODIFY COLUMN payment_method VARCHAR(50) NOT NULL DEFAULT 'cash'");

        Schema::table('convention_bookings', function (Blueprint $table) {
            $table->string('bkash_number')->nullable()->after('payment_method');
            $table->string('bank_name')->nullable()->after('bkash_number');
            $table->json('customer_photo')->nullable()->after('customer_address');
            $table->json('customer_nid_document')->nullable()->after('customer_photo');
            $table->json('passport_document')->nullable()->after('customer_nid_document');
            $table->json('visiting_card')->nullable()->after('passport_document');
        });

        // Change convention_payments payment_method to varchar too
        DB::statement("ALTER TABLE convention_payments MODIFY COLUMN payment_method VARCHAR(50) NOT NULL DEFAULT 'cash'");

        Schema::table('convention_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('convention_payments', 'bkash_number')) {
                $table->string('bkash_number')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('convention_payments', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('bkash_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('convention_bookings', function (Blueprint $table) {
            $table->dropColumn(['bkash_number', 'bank_name', 'customer_photo', 'customer_nid_document', 'passport_document', 'visiting_card']);
        });

        DB::statement("ALTER TABLE convention_bookings MODIFY COLUMN payment_method ENUM('cash','card','mfs') NOT NULL");

        Schema::table('convention_payments', function (Blueprint $table) {
            $table->dropColumn(['bkash_number', 'bank_name']);
        });

        DB::statement("ALTER TABLE convention_payments MODIFY COLUMN payment_method ENUM('cash','card','mfs') NOT NULL");
    }
};
