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
        Schema::create('convention_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hall_id')->constrained('convention_halls')->onDelete('cascade');
            $table->string('customer_name');
            $table->string('customer_nid')->nullable();
            $table->string('customer_phone');
            $table->string('customer_whatsapp')->nullable();
            $table->string('customer_email')->nullable();
            $table->text('customer_address')->nullable();
            $table->date('event_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('time_slot')->nullable();
            $table->string('event_type');
            $table->string('organization_name')->nullable();
            $table->text('event_description')->nullable();
            $table->integer('number_of_guests');
            $table->unsignedBigInteger('food_package_id')->nullable();
            $table->decimal('food_cost', 10, 2)->default(0);
            $table->json('selected_addons')->nullable();
            $table->json('addon_quantities')->nullable();
            $table->decimal('addons_cost', 10, 2)->default(0);
            $table->decimal('hall_rent', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->enum('discount_type', ['flat', 'percentage'])->default('flat');
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->decimal('vat_amount', 10, 2)->default(0);
            $table->decimal('vat_percentage', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('advance_payment', 10, 2)->default(0);
            $table->decimal('remaining_payment', 10, 2)->default(0);
            $table->enum('payment_method', ['cash', 'card', 'mfs']);
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'refunded'])->default('pending');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->enum('program_status', ['pending', 'confirmed', 'running', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('created_by_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('convention_bookings');
    }
};
