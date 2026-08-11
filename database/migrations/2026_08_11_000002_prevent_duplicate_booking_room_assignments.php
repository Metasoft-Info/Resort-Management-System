<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('booking_rooms')) {
            return;
        }

        // Keep the earliest row for any existing duplicate pair before adding
        // the database guard.
        $duplicates = DB::table('booking_rooms')
            ->select('booking_id', 'room_id', DB::raw('MIN(id) as keep_id'))
            ->groupBy('booking_id', 'room_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('booking_rooms')
                ->where('booking_id', $duplicate->booking_id)
                ->where('room_id', $duplicate->room_id)
                ->where('id', '!=', $duplicate->keep_id)
                ->delete();
        }

        try {
            Schema::table('booking_rooms', function (Blueprint $table) {
                $table->unique(['booking_id', 'room_id'], 'booking_rooms_booking_room_unique');
            });
        } catch (\Throwable $e) {
            // The index may already exist on a database deployed manually.
        }

        // The supplied dump contained this table as MyISAM, which ignores
        // transaction boundaries. Convert it when running on MySQL so room
        // assignment and booking totals can commit atomically.
        if (DB::getDriverName() === 'mysql') {
            try {
                DB::statement('ALTER TABLE booking_rooms ENGINE=InnoDB');
            } catch (\Throwable $e) {
                // Do not block deployment if the host disallows conversion.
            }
        }
    }

    public function down(): void
    {
        try {
            Schema::table('booking_rooms', function (Blueprint $table) {
                $table->dropUnique('booking_rooms_booking_room_unique');
            });
        } catch (\Throwable $e) {
            // Best effort rollback for databases where the index was absent.
        }
    }
};
