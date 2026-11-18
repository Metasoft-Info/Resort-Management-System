<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('booking_extra_charge_requests')) {
            return;
        }

        Schema::create('booking_extra_charge_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->uuid('request_id');
            $table->char('payload_hash', 64);
            $table->timestamps();
            $table->unique(['booking_id', 'request_id'], 'booking_extra_charge_request_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_extra_charge_requests');
    }
};
