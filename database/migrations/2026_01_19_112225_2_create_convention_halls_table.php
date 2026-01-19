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
        Schema::create('convention_halls', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('dimensions', 10, 2)->nullable()->comment('in sq ft');
            $table->integer('max_capacity')->nullable();
            $table->decimal('price_per_day', 10, 2)->default(0);
            $table->boolean('is_available')->default(true);
            $table->json('amenities')->nullable();
            $table->json('images')->nullable();
            $table->json('event_types')->nullable();
            $table->json('time_slots')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('convention_halls');
    }
};
