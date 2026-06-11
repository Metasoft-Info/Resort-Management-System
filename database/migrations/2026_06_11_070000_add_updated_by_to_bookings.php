<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('bookings', 'updated_by_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->unsignedBigInteger('updated_by_id')->nullable()->after('created_by_id');
            });
        }

        if (!Schema::hasColumn('convention_bookings', 'updated_by_id')) {
            Schema::table('convention_bookings', function (Blueprint $table) {
                $table->unsignedBigInteger('updated_by_id')->nullable()->after('created_by_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('bookings', 'updated_by_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('updated_by_id');
            });
        }

        if (Schema::hasColumn('convention_bookings', 'updated_by_id')) {
            Schema::table('convention_bookings', function (Blueprint $table) {
                $table->dropColumn('updated_by_id');
            });
        }
    }
};
