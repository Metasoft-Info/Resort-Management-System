<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('booking_rooms')) {
            try {
                Schema::create('booking_rooms', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
                    $table->foreignId('room_id')->constrained()->restrictOnDelete();
                    $table->decimal('price_per_night', 10, 2);
                    $table->timestamps();
                });
            } catch (\Exception $e) {
                // Table already exists, migration is complete
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_rooms');
    }
};