<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('booking_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignUuid('booking_group_id')->nullable()->constrained('booking_groups')->nullOnDelete();
            $table->boolean('is_complimentary')->default(false)->index();
            $table->text('complimentary_reason')->nullable();
            $table->timestamp('complimentary_at')->nullable();
            $table->foreignId('complimentary_by_id')->nullable()->constrained('users')->nullOnDelete();
        });
        Schema::table('booking_financial_snapshots', function (Blueprint $table) {
            $table->decimal('complimentary', 12, 2)->default(0);
        });
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

    public function down(): void
    {
        Schema::dropIfExists('booking_room_shifts');
        Schema::table('booking_financial_snapshots', fn (Blueprint $table) => $table->dropColumn('complimentary'));
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('complimentary_by_id');
            $table->dropForeign(['booking_group_id']);
            $table->dropColumn(['booking_group_id', 'is_complimentary', 'complimentary_reason', 'complimentary_at']);
        });
        Schema::dropIfExists('booking_groups');
    }
};
