<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Uploaded document paths are stored as JSON arrays. A 255-character
     * VARCHAR is not large enough for multiple uploaded files.
     */
    public function up(): void
    {
        if (!Schema::hasTable('bookings')) {
            Log::warning('Booking document column expansion skipped: bookings table is missing.');
            return;
        }

        // Production is MySQL. Leave other drivers untouched rather than
        // issuing MySQL-specific ALTER syntax in SQLite-based test suites.
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        $documentColumns = array_values(array_filter(
            ['customer_photo', 'customer_nid_document', 'passport_document', 'visiting_card'],
            fn (string $column) => Schema::hasColumn('bookings', $column)
        ));

        if (!$documentColumns) {
            Log::warning('Booking document column expansion skipped: no document columns were found.');
            return;
        }

        $changes = implode(', ', array_map(
            fn (string $column) => "MODIFY `{$column}` LONGTEXT NULL",
            $documentColumns
        ));

        DB::statement("ALTER TABLE `bookings` {$changes}");
    }

    public function down(): void
    {
        // Keep the wider columns: narrowing them could truncate already-saved
        // document paths and restore the production booking failure.
    }
};
