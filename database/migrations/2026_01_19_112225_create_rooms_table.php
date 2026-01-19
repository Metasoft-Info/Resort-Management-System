<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->string('name');
            $table->enum('type', ['standard', 'deluxe', 'suite', 'family']);
            $table->text('description')->nullable();
            $table->decimal('price_per_night', 10, 2);
            $table->boolean('has_ac')->default(true);
            $table->decimal('ac_price', 10, 2)->default(0);
            $table->decimal('non_ac_price', 10, 2)->default(0);
            $table->integer('max_guests')->nullable();
            $table->integer('number_of_beds')->nullable();
            $table->json('amenities')->nullable();
            $table->json('images')->nullable();
            $table->enum('status', ['available', 'booked', 'maintenance'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
