<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\PremiumBookingController;
use App\Models\{Booking, BookingPayment, Room, User};
use App\Services\ReturningGuestService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Schema};
use Illuminate\Support\Str;
use Tests\TestCase;

class ReturningGuestAndBillCorrectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // These tests use a new in-memory database, never the configured app DB.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Carbon::setTestNow(Carbon::parse('2026-10-15 14:00:00', 'Asia/Dhaka'));

        Schema::create('users', function (Blueprint $table) {
            $table->id(); $table->string('name');
        });
        DB::table('users')->insert(['id' => 1, 'name' => 'Test Admin']);
        Schema::create('room_types', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->decimal('base_price', 10, 2);
        });
        Schema::create('rooms', function (Blueprint $table) {
            $table->id(); $table->string('room_number'); $table->unsignedBigInteger('room_type_id');
            $table->decimal('price_per_night', 10, 2); $table->string('status')->default('available'); $table->timestamps();
        });
        Schema::create('bookings', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('room_id')->nullable();
            foreach (['customer_name', 'customer_phone', 'customer_nid', 'customer_whatsapp', 'customer_email', 'customer_address', 'company_name', 'passport_number', 'reference_name', 'reference_phone', 'booking_purpose', 'notes', 'ac_preference', 'payment_method', 'bkash_number', 'bank_name', 'discount_reference', 'discount_status', 'booking_group_id'] as $field) {
                $table->string($field)->nullable();
            }
            $table->date('check_in_date'); $table->date('check_out_date');
            $table->string('check_in_time')->nullable(); $table->string('check_out_time')->nullable();
            $table->string('status')->default('confirmed'); $table->string('payment_status')->default('pending');
            $table->string('discount_type')->default('none');
            foreach (['total_amount', 'advance_payment', 'remaining_payment', 'discount_amount', 'discount_percentage', 'extra_charges', 'vat_amount', 'food_package_cost', 'addons_cost'] as $field) {
                $table->decimal($field, 12, 2)->default(0);
            }
            $table->string('extra_charges_description')->nullable();
            foreach (['customer_photo', 'customer_nid_document', 'passport_document', 'visiting_card', 'extra_charges_data', 'selected_addons', 'extras', 'additional_guests'] as $field) {
                $table->text($field)->nullable();
            }
            $table->unsignedInteger('number_of_guests')->default(1); $table->unsignedInteger('food_package_guests')->default(0);
            $table->boolean('vat_enabled')->default(false); $table->boolean('is_complimentary')->default(false);
            foreach (['created_by_id', 'updated_by_id', 'discount_requested_by', 'discount_approved_by'] as $field) $table->unsignedBigInteger($field)->nullable();
            $table->timestamp('discount_approved_at')->nullable(); $table->timestamps();
        });
        Schema::create('booking_rooms', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('booking_id'); $table->unsignedBigInteger('room_id');
            $table->decimal('price_per_night', 10, 2); $table->date('check_in_date'); $table->date('check_out_date'); $table->timestamps();
        });
        Schema::create('booking_payments', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('booking_id'); $table->decimal('amount', 10, 2);
            $table->string('type'); $table->string('method')->default('cash'); $table->string('note')->nullable();
            $table->unsignedBigInteger('recorded_by_id')->nullable(); $table->string('request_id')->nullable();
            $table->date('payment_date')->nullable(); $table->timestamps();
        });
        Schema::create('booking_room_shifts', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('booking_id'); $table->unsignedBigInteger('from_room_id')->nullable();
        });
        Schema::create('booking_groups', function (Blueprint $table) {
            $table->string('id')->primary(); $table->unsignedBigInteger('created_by_id')->nullable(); $table->timestamps();
        });
        $snapshotMigration = require database_path('migrations/2026_08_25_000002_create_booking_financial_snapshots_table.php');
        $snapshotMigration->up();
        Schema::table('booking_financial_snapshots', fn (Blueprint $table) => $table->decimal('complimentary', 12, 2)->default(0));

        DB::table('room_types')->insert(['id' => 1, 'name' => 'Standard', 'base_price' => 1000]);
        DB::table('rooms')->insert([
            ['id' => 14, 'room_number' => '306', 'room_type_id' => 1, 'price_per_night' => 1000, 'status' => 'available'],
            ['id' => 15, 'room_number' => '307', 'room_type_id' => 1, 'price_per_night' => 2000, 'status' => 'available'],
        ]);
        $this->actingAs((new User(['name' => 'Test Admin', 'role' => 'superadmin']))->forceFill(['id' => 1]));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function previousStay(array $attributes = []): Booking
    {
        $booking = new Booking();
        $booking->forceFill(array_merge([
            'customer_name' => 'Returning Guest', 'customer_phone' => '01711111111', 'customer_nid' => 'TEST-NID',
            'check_in_date' => '2026-10-12', 'check_out_date' => '2026-10-15',
            'status' => 'checked_out', 'total_amount' => 3000, 'payment_method' => 'cash',
        ], $attributes));
        $booking->saveQuietly();
        $booking->bookingRooms()->create([
            'room_id' => 14, 'price_per_night' => 1000,
            'check_in_date' => $booking->check_in_date->toDateString(), 'check_out_date' => $booking->check_out_date->toDateString(),
        ]);

        return $booking;
    }

    public function test_history_matches_complete_phone_formats_and_returns_recent_stays_with_profiles(): void
    {
        for ($day = 1; $day <= 7; $day++) {
            $this->previousStay([
                'customer_phone' => $day % 2 ? '+88 01711-111111' : '01711111111',
                'customer_name' => 'Guest '.$day,
                'check_in_date' => sprintf('2026-10-%02d', $day),
                'check_out_date' => sprintf('2026-10-%02d', $day + 1),
            ]);
        }
        $this->previousStay(['customer_phone' => '01722222222']);
        $result = app(ReturningGuestService::class)->search('8801711111111');

        $this->assertTrue($result['success']);
        $this->assertSame(7, $result['total_bookings']);
        $this->assertCount(6, $result['history']);
        $this->assertSame('Guest 7', $result['customer']['customer_name']);
        $this->assertSame(['306'], $result['history'][0]['rooms']);
        $this->assertSame(1000.0, $result['history'][0]['bill_total']);
        $this->assertArrayNotHasKey('discount_amount', $result['history'][0]['profile']);
        $this->assertArrayNotHasKey('advance_payment', $result['history'][0]['profile']);
        $this->assertFalse(app(ReturningGuestService::class)->search('111111')['success']);
        $this->assertSame(7, ReturningGuestService::forPhone('01711-111111')->count());
    }

    public function test_bill_correction_preserves_payment_and_updates_current_report_without_duplicate_snapshots(): void
    {
        $booking = $this->previousStay([
            'status' => 'checked_in', 'check_in_date' => '2026-10-11', 'discount_type' => 'flat',
            'discount_amount' => 2000, 'payment_status' => 'paid', 'discount_status' => 'pending',
            'discount_reference' => 'Old reference', 'created_at' => '2026-10-14 11:27:24',
        ]);
        DB::table('bookings')->where('id', $booking->id)->update(['id' => 366]);
        DB::table('booking_rooms')->where('booking_id', $booking->id)->update(['booking_id' => 366]);
        BookingPayment::create(['booking_id' => 366, 'amount' => 2000, 'method' => 'cash', 'type' => 'payment', 'payment_date' => '2026-10-15', 'recorded_by_id' => 1]);
        $paymentBefore = BookingPayment::first()->getAttributes();
        $migration = require database_path('migrations/2026_10_15_000001_clear_booking_366_discount_and_correct_dates.php');
        $migration->up();
        $migration->up();

        $corrected = Booking::findOrFail(366);
        $this->assertSame('2026-10-12', $corrected->check_in_date->toDateString());
        $this->assertSame('2026-10-15', $corrected->check_out_date->toDateString());
        $this->assertSame('none', $corrected->discount_type);
        $this->assertEquals(0, $corrected->discount_amount);
        $this->assertNull($corrected->discount_status);
        $this->assertNull($corrected->discount_reference);
        $this->assertEquals(3000, $corrected->total_amount);
        $this->assertEquals(1000, $corrected->remaining_payment);
        $this->assertSame('partial', $corrected->payment_status);
        $this->assertSame('checked_in', $corrected->status);
        $this->assertSame($paymentBefore, BookingPayment::first()->getAttributes());
        $this->assertSame(1, $corrected->financialSnapshots()->count());
        $report = $corrected->getReportFinancials('2026-10-15', '2026-10-15');
        $this->assertSame(0.0, $report['discount']);
        $this->assertSame(3000.0, $report['grand_total']);
        $this->assertSame(1000.0, $report['remaining']);
    }

    public function test_bill_correction_skips_a_discount_changed_after_the_export(): void
    {
        $booking = $this->previousStay(['discount_type' => 'flat', 'discount_amount' => 1500, 'created_at' => '2026-10-14 11:27:24']);
        DB::table('bookings')->where('id', $booking->id)->update(['id' => 366]);
        DB::table('booking_rooms')->where('booking_id', $booking->id)->update(['booking_id' => 366]);
        $migration = require database_path('migrations/2026_10_15_000001_clear_booking_366_discount_and_correct_dates.php');
        $migration->up();
        $this->assertEquals(1500, Booking::findOrFail(366)->discount_amount);
    }

    private function confirmedReceiptFixture(float $amount = 2000): BookingPayment
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action'); $table->string('entity_type'); $table->unsignedBigInteger('entity_id');
            $table->text('changes')->nullable(); $table->timestamps();
        });
        $booking = $this->previousStay([
            'status' => 'checked_in', 'discount_type' => 'none', 'discount_amount' => 0,
            'payment_status' => 'partial', 'created_at' => '2026-10-14 11:27:24',
        ]);
        DB::table('bookings')->where('id', $booking->id)->update(['id' => 366]);
        DB::table('booking_rooms')->where('booking_id', $booking->id)->update(['booking_id' => 366]);
        $payment = new BookingPayment();
        $payment->forceFill([
            'id' => 347, 'booking_id' => 366, 'amount' => $amount, 'method' => 'cash', 'type' => 'payment',
            'payment_date' => '2026-10-15', 'recorded_by_id' => 1,
            'request_id' => '812da458-01c4-47aa-ad68-878da1b1a573',
        ])->save();

        return $payment;
    }

    public function test_confirmed_fifty_percent_discount_and_actual_receipt_reconcile_all_bill_totals(): void
    {
        $paymentBefore = $this->confirmedReceiptFixture()->getAttributes();
        $earlierMigration = require database_path('migrations/2026_10_15_000001_clear_booking_366_discount_and_correct_dates.php');
        $earlierMigration->up();
        $migration = require database_path('migrations/2026_10_15_000002_correct_booking_366_confirmed_discount_and_receipt.php');
        $migration->up();
        $migration->up();

        $booking = Booking::findOrFail(366);
        $this->assertSame('2026-10-12', $booking->check_in_date->toDateString());
        $this->assertSame('2026-10-15', $booking->check_out_date->toDateString());
        $this->assertSame('checked_in', $booking->status);
        $this->assertSame('percentage', $booking->discount_type);
        $this->assertEquals(50, $booking->discount_percentage);
        $this->assertEquals(1500, $booking->discount_amount);
        $this->assertSame('approved', $booking->discount_status);
        $this->assertSame('paid', $booking->payment_status);
        $this->assertEquals(0, $booking->remaining_payment);
        $financials = $booking->getFinancialBreakdown();
        foreach (['room_rent' => 3000.0, 'discount' => 1500.0, 'grand_total' => 1500.0, 'deposited' => 1500.0, 'remaining' => 0.0] as $field => $expected) {
            $this->assertEquals($expected, $financials[$field]);
        }
        $report = $booking->getReportFinancials('2026-10-15', '2026-10-15');
        foreach (['room_rent' => 3000.0, 'discount' => 1500.0, 'grand_total' => 1500.0, 'deposited' => 1500.0, 'remaining' => 0.0] as $field => $expected) {
            $this->assertEquals($expected, $report[$field]);
        }

        $payment = BookingPayment::findOrFail(347);
        $this->assertEquals(1500, $payment->amount);
        foreach (['id', 'booking_id', 'method', 'type', 'recorded_by_id', 'request_id', 'payment_date', 'created_at'] as $field) {
            $this->assertSame($paymentBefore[$field], $payment->getAttributes()[$field]);
        }
        $this->assertSame(1, substr_count($payment->note, '[Booking 366 correction 2026-10-15]'));
        $this->assertSame(1, BookingPayment::count());
        $this->assertSame(2, $booking->financialSnapshots()->count());
        $this->assertSame(1, DB::table('activity_logs')->count());
        $audit = json_decode(DB::table('activity_logs')->value('changes'), true);
        $this->assertEquals(2000, $audit['previous_payment_amount']);
        $this->assertEquals(1500, $audit['corrected_payment_amount']);
    }

    public function test_confirmed_receipt_correction_skips_when_new_payment_movements_exist(): void
    {
        $this->confirmedReceiptFixture();
        BookingPayment::create(['booking_id' => 366, 'amount' => 500, 'type' => 'payment', 'method' => 'cash', 'recorded_by_id' => 1]);
        $migration = require database_path('migrations/2026_10_15_000002_correct_booking_366_confirmed_discount_and_receipt.php');
        $migration->up();

        $this->assertEquals(2000, BookingPayment::findOrFail(347)->amount);
        $this->assertSame('none', Booking::findOrFail(366)->discount_type);
        $this->assertSame(0, DB::table('activity_logs')->count());
    }

    public function test_report_snapshot_uses_the_latest_effective_bill_with_or_without_eager_loading(): void
    {
        $booking = $this->previousStay(['discount_type' => 'percentage', 'discount_percentage' => 50]);
        foreach ([['2026-10-14', 0, 3000], ['2026-10-15', 1500, 1500]] as [$date, $discount, $total]) {
            $booking->financialSnapshots()->create([
                'effective_date' => $date, 'check_in_date' => '2026-10-12', 'check_out_date' => '2026-10-15',
                'room_rent' => 3000, 'discount' => $discount, 'grand_total' => $total,
            ]);
        }
        $earlier = $booking->getFinancialBreakdownAt('2026-10-14');
        $latest = $booking->getFinancialBreakdownAt('2026-10-15');
        $this->assertSame(0.0, $earlier['discount']);
        $this->assertSame(3000.0, $earlier['grand_total']);
        $this->assertSame(1500.0, $latest['discount']);
        $this->assertSame(1500.0, $latest['grand_total']);
        $booking->load('financialSnapshots');
        $this->assertSame($earlier, $booking->getFinancialBreakdownAt('2026-10-14'));
        $this->assertSame($latest, $booking->getFinancialBreakdownAt('2026-10-15'));
    }

    public function test_confirmed_discount_recovery_handles_an_already_corrected_receipt(): void
    {
        $this->confirmedReceiptFixture(1500);
        $migration = require database_path('migrations/2026_10_15_000002_correct_booking_366_confirmed_discount_and_receipt.php');
        $migration->up();
        $migration->up();

        $booking = Booking::findOrFail(366);
        $this->assertEquals(1500, $booking->getGrandTotal());
        $this->assertEquals(1500, $booking->getTotalDeposited());
        $this->assertSame('paid', $booking->payment_status);
        $this->assertSame(1, $booking->financialSnapshots()->count());
        $this->assertSame(1, DB::table('activity_logs')->count());
    }

    private function checkinRequest(array $overrides = []): Request
    {
        return Request::create('/admin/premium-booking/book', 'POST', array_merge([
            'rooms_data' => json_encode([['roomId' => 14, 'roomNumber' => '306', 'pricePerNight' => 1]]),
            'check_in_date' => '2026-10-15', 'check_out_date' => '2026-10-17',
            'customer_name' => 'Returning Guest', 'customer_phone' => '01711111111',
            'number_of_guests' => 1, 'ac_preference' => 'non-ac', 'status' => 'checked_in',
            'payment_method' => 'cash', 'advance_payment' => 0,
        ], $overrides));
    }

    public function test_direct_checkin_creates_a_new_bill_and_marks_room_occupied(): void
    {
        $previous = $this->previousStay(['check_in_date' => '2026-10-10', 'check_out_date' => '2026-10-11', 'discount_type' => 'flat', 'discount_amount' => 500]);
        $response = app(PremiumBookingController::class)->book($this->checkinRequest());
        $this->assertSame(200, $response->getStatusCode(), $response->getContent());
        $new = Booking::latest('id')->first();
        $this->assertNotSame($previous->id, $new->id);
        $this->assertSame('checked_in', $new->status);
        $this->assertEquals(2000, $new->total_amount);
        $this->assertEquals(0, $new->discount_amount);
        $this->assertEquals(0, $new->getTotalDeposited());
        $this->assertEquals(500, $previous->fresh()->discount_amount);
        $this->assertSame('occupied', Room::findOrFail(14)->status);
    }

    public function test_direct_checkin_rejects_a_future_arrival(): void
    {
        $response = app(PremiumBookingController::class)->book($this->checkinRequest(['check_in_date' => '2026-10-16']));
        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(0, Booking::count());
        $this->assertSame('available', Room::findOrFail(14)->status);
    }

    public function test_direct_checkin_rejects_maintenance_and_conflicting_stays(): void
    {
        Room::findOrFail(14)->update(['status' => 'maintenance']);
        $response = app(PremiumBookingController::class)->book($this->checkinRequest());
        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(0, Booking::count());

        Room::findOrFail(14)->update(['status' => 'occupied']);
        $this->previousStay(['status' => 'checked_in', 'check_in_date' => '2026-10-15', 'check_out_date' => '2026-10-17']);
        $response = app(PremiumBookingController::class)->book($this->checkinRequest());
        $this->assertSame(409, $response->getStatusCode());
        $this->assertSame(1, Booking::count());
    }

    public function test_group_direct_checkin_marks_each_separate_room_booking_occupied(): void
    {
        $response = app(PremiumBookingController::class)->book($this->checkinRequest([
            'billing_mode' => 'group', 'group_request_id' => (string) Str::uuid(),
            'rooms_data' => json_encode([['roomId' => 14], ['roomId' => 15]]),
        ]));
        $this->assertSame(200, $response->getStatusCode(), $response->getContent());
        $this->assertSame(2, Booking::where('status', 'checked_in')->count());
        $this->assertSame(2, Room::where('status', 'occupied')->count());
        $this->assertEquals(6000, Booking::sum('total_amount'));
    }
}
