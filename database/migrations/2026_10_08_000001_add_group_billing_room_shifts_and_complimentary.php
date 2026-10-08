<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('booking_groups')) {
            Schema::create('booking_groups', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // Some production databases may already contain pieces of this schema,
        // for example after a previous migration stopped midway on MySQL.
        // Keep those objects and add only fields that are still missing.
        foreach (['id', 'created_by_id', 'created_at', 'updated_at'] as $column) {
            if (!Schema::hasColumn('booking_groups', $column)) {
                throw new RuntimeException("The existing booking_groups table is missing the required '{$column}' column. No schema was changed; inspect the table before retrying.");
            }
        }

        if (!Schema::hasColumn('bookings', 'booking_group_id')) {
            Schema::table('bookings', fn (Blueprint $table) => $table->foreignUuid('booking_group_id')->nullable()->constrained('booking_groups')->nullOnDelete());
        }
        if (!Schema::hasColumn('bookings', 'is_complimentary')) {
            Schema::table('bookings', fn (Blueprint $table) => $table->boolean('is_complimentary')->default(false)->index());
        }
        if (!Schema::hasColumn('bookings', 'complimentary_reason')) {
            Schema::table('bookings', fn (Blueprint $table) => $table->text('complimentary_reason')->nullable());
        }
        if (!Schema::hasColumn('bookings', 'complimentary_at')) {
            Schema::table('bookings', fn (Blueprint $table) => $table->timestamp('complimentary_at')->nullable());
        }
        if (!Schema::hasColumn('bookings', 'complimentary_by_id')) {
            Schema::table('bookings', fn (Blueprint $table) => $table->foreignId('complimentary_by_id')->nullable()->constrained('users')->nullOnDelete());
        }

        if (!Schema::hasColumn('booking_financial_snapshots', 'complimentary')) {
            Schema::table('booking_financial_snapshots', fn (Blueprint $table) => $table->decimal('complimentary', 12, 2)->default(0));
        }

        if (!Schema::hasTable('booking_room_shifts')) {
            Schema::create('booking_room_shifts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
                $table->foreignId('from_room_id')->nullable()->constrained('rooms')->nullOnDelete();
                $table->foreignId('to_room_id')->nullable()->constrained('rooms')->nullOnDelete();
                $table->string('from_room_number');
                $table->string('to_room_number');
                $table->date('occupied_from');
                $table->date('shift_date');
                $table->decimal('previous_rate', 12, 2);
                $table->decimal('new_rate', 12, 2);
                $table->unsignedInteger('billed_nights');
                $table->decimal('billed_amount', 12, 2);
                $table->text('reason')->nullable();
                $table->foreignId('shifted_by_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        foreach (['booking_id', 'from_room_id', 'to_room_id', 'from_room_number', 'to_room_number', 'occupied_from', 'shift_date', 'previous_rate', 'new_rate', 'billed_nights', 'billed_amount', 'reason', 'shifted_by_id', 'created_at', 'updated_at'] as $column) {
            if (!Schema::hasColumn('booking_room_shifts', $column)) {
                throw new RuntimeException("The existing booking_room_shifts table is missing the required '{$column}' column. No schema was changed; inspect the table before retrying.");
            }
        }
    }

    public function down(): void
    {
        // The up migration intentionally supports databases where some objects
        // pre-exist. We cannot safely identify ownership during rollback, so do
        // not drop tables or columns that may contain production data.
    }
};
