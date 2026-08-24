<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('booking_financial_snapshots')) {
            return;
        }

        Schema::create('booking_financial_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->date('effective_date')->index();
            $table->timestamp('effective_at')->nullable();
            $table->date('check_in_date')->nullable();
            $table->date('check_out_date')->nullable();
            $table->decimal('room_rent', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('extra_charges', 12, 2)->default(0);
            $table->decimal('vat', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->string('reason')->nullable();
            $table->foreignId('recorded_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['booking_id', 'effective_date', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_financial_snapshots');
    }
};
