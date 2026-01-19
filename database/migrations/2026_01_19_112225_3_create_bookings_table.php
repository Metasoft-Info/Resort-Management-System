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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->string('customer_name');
            $table->string('customer_nid');
            $table->string('customer_photo')->nullable();
            $table->string('customer_nid_document')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('passport_document')->nullable();
            $table->string('visiting_card')->nullable();
            $table->string('customer_phone');
            $table->string('reference_name')->nullable();
            $table->string('reference_phone')->nullable();
            $table->string('customer_whatsapp')->nullable();
            $table->string('customer_email');
            $table->text('customer_address')->nullable();
            $table->date('check_in_date');
            $table->time('check_in_time')->nullable();
            $table->date('check_out_date');
            $table->time('check_out_time')->nullable();
            $table->integer('number_of_guests');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('advance_payment', 10, 2)->default(0);
            $table->decimal('remaining_payment', 10, 2)->default(0);
            $table->enum('payment_method', ['cash', 'card', 'mfs']);
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'refunded'])->default('pending');
            $table->enum('status', ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'])->default('pending');
            $table->decimal('extra_charges', 10, 2)->default(0);
            $table->text('extra_charges_description')->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->enum('discount_type', ['none', 'percentage', 'flat'])->default('none');
            $table->unsignedBigInteger('food_package_id')->nullable();
            $table->integer('food_package_guests')->default(0);
            $table->decimal('food_package_cost', 10, 2)->default(0);
            $table->json('selected_addons')->nullable();
            $table->decimal('addons_cost', 10, 2)->default(0);
            $table->json('extras')->nullable();
            $table->json('additional_guests')->nullable();
            $table->text('notes')->nullable();
            $table->string('ac_preference')->default('ac');
            $table->boolean('vat_enabled')->default(false);
            $table->decimal('vat_amount', 10, 2)->default(0);
            $table->foreignId('created_by_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
