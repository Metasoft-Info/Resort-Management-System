<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'discount_status')) {
                $table->string('discount_status', 20)->nullable()->after('discount_reference')->comment('pending, approved, rejected');
            }
            if (!Schema::hasColumn('bookings', 'discount_requested_by')) {
                $table->foreignId('discount_requested_by')->nullable()->after('discount_status')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('bookings', 'discount_approved_by')) {
                $table->foreignId('discount_approved_by')->nullable()->after('discount_requested_by')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('bookings', 'discount_approved_at')) {
                $table->timestamp('discount_approved_at')->nullable()->after('discount_approved_by');
            }
        });

        Schema::table('convention_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('convention_bookings', 'discount_status')) {
                $table->string('discount_status', 20)->nullable()->after('discount_value')->comment('pending, approved, rejected');
            }
            if (!Schema::hasColumn('convention_bookings', 'discount_requested_by')) {
                $table->foreignId('discount_requested_by')->nullable()->after('discount_status')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('convention_bookings', 'discount_approved_by')) {
                $table->foreignId('discount_approved_by')->nullable()->after('discount_requested_by')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('convention_bookings', 'discount_approved_at')) {
                $table->timestamp('discount_approved_at')->nullable()->after('discount_approved_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['discount_status', 'discount_requested_by', 'discount_approved_by', 'discount_approved_at']);
        });
        Schema::table('convention_bookings', function (Blueprint $table) {
            $table->dropColumn(['discount_status', 'discount_requested_by', 'discount_approved_by', 'discount_approved_at']);
        });
    }
};
