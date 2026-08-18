<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\BookingPayment;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class BookingRoomAssignmentTest extends TestCase
{
    public function test_normalized_room_rows_are_authoritative_and_deduplicated(): void
    {
        $booking = new Booking(['room_id' => 4]);
        $booking->setRelation('bookingRooms', new Collection([
            new BookingRoom(['room_id' => 19]),
            new BookingRoom(['room_id' => 4]),
        ]));

        $this->assertSame([19, 4], $booking->getAllRoomIds());
    }

    public function test_legacy_room_is_used_only_when_no_normalized_rows_exist(): void
    {
        $booking = new Booking(['room_id' => 4]);
        $booking->setRelation('bookingRooms', new Collection());

        $this->assertSame([4], $booking->getAllRoomIds());
    }

    public function test_stale_legacy_room_does_not_inflate_calculated_total(): void
    {
        $booking = new Booking();
        $booking->setRawAttributes([
            'room_id' => 4,
            'check_in_date' => '2026-08-10',
            'check_out_date' => '2026-08-11',
            'total_amount' => 6000,
        ], true);
        $booking->setRelation('bookingRooms', new Collection([
            (object) [
                'room_id' => 19,
                'price_per_night' => 2000,
                'check_in_date' => '2026-08-10',
                'check_out_date' => '2026-08-11',
            ],
        ]));

        $this->assertEquals(2000, $booking->getCalculatedTotal());
    }

    public function test_extended_room_dates_charge_the_added_night(): void
    {
        $booking = new Booking();
        $booking->setRawAttributes([
            'check_in_date' => '2026-08-10',
            'check_out_date' => '2026-08-13',
            'total_amount' => 8000,
        ], true);
        $booking->setRelation('bookingRooms', new Collection([
            (object) [
                'room_id' => 5,
                'price_per_night' => 4000,
                'check_in_date' => '2026-08-10',
                'check_out_date' => '2026-08-13',
            ],
        ]));

        $this->assertEquals(12000, $booking->getCalculatedTotal());
    }

    public function test_three_nights_at_three_thousand_is_nine_thousand(): void
    {
        $booking = new Booking();
        $booking->setRawAttributes([
            'check_in_date' => '2026-08-14',
            'check_out_date' => '2026-08-17',
            'total_amount' => 0,
        ], true);
        $booking->setRelation('bookingRooms', new Collection([
            (object) [
                'room_id' => 8,
                'price_per_night' => 3000,
                'check_in_date' => '2026-08-14',
                'check_out_date' => '2026-08-17',
            ],
        ]));

        $this->assertSame(3, $booking->getNights());
        $this->assertEquals(9000, $booking->getCalculatedTotal());
    }

    public function test_checkout_date_is_not_charged_as_an_extra_night(): void
    {
        $booking = new Booking();
        $booking->setRawAttributes([
            'check_in_date' => '2026-08-13',
            'check_out_date' => '2026-08-15',
            'total_amount' => 0,
        ], true);
        $booking->setRelation('bookingRooms', new Collection([
            (object) [
                'room_id' => 8,
                'price_per_night' => 3000,
                'check_in_date' => '2026-08-13',
                'check_out_date' => '2026-08-15',
            ],
        ]));

        $this->assertSame(2, $booking->getNights());
        $this->assertEquals(6000, $booking->getCalculatedTotal());
    }

    public function test_current_published_room_rate_replaces_stale_snapshot_rate(): void
    {
        $booking = new Booking();
        $booking->setRawAttributes([
            'check_in_date' => '2026-08-14',
            'check_out_date' => '2026-08-17',
            'total_amount' => 6000,
        ], true);

        $room = new class {
            public float $price_per_night = 3000;

            public function relationLoaded(string $relation): bool
            {
                return $relation === 'roomType';
            }
        };
        $bookingRoom = new BookingRoom();
        $bookingRoom->setRawAttributes([
            'room_id' => 9,
            'price_per_night' => 2000,
            'check_in_date' => '2026-08-14',
            'check_out_date' => '2026-08-16',
        ], true);
        $bookingRoom->setRelation('room', $room);
        $booking->setRelation('bookingRooms', new Collection([$bookingRoom]));

        $this->assertSame(3, $booking->getRoomBreakdown()->first()['nights']);
        $this->assertEquals(9000, $booking->getCalculatedTotal());
    }

    public function test_refunds_reduce_deposited_amount(): void
    {
        $booking = new Booking();
        $booking->setRawAttributes(['advance_payment' => 2500], true);
        $booking->setRelation('payments', new Collection([
            new BookingPayment(['amount' => 2500, 'type' => 'advance']),
            new BookingPayment(['amount' => 1000, 'type' => 'payment']),
            new BookingPayment(['amount' => 500, 'type' => 'refund']),
        ]));

        $this->assertEquals(3000, $booking->getTotalDeposited());
    }

    public function test_financial_breakdown_keeps_bill_and_balance_consistent(): void
    {
        $booking = new Booking();
        $booking->setRawAttributes([
            'check_in_date' => '2026-08-10',
            'check_out_date' => '2026-08-12',
            'discount_type' => 'flat',
            'discount_amount' => 1000,
            'extra_charges' => 175,
            'advance_payment' => 0,
        ], true);

        $booking->setRelation('bookingRooms', new Collection([(object) [
            'room_id' => 6,
            'price_per_night' => 3000,
            'check_in_date' => '2026-08-10',
            'check_out_date' => '2026-08-12',
        ]]));
        $booking->setRelation('payments', new Collection([
            new BookingPayment(['amount' => 4000, 'type' => 'payment']),
        ]));

        $financials = $booking->getFinancialBreakdown();

        $this->assertSame(6000.0, $financials['room_rent']);
        $this->assertSame(1000.0, $financials['discount']);
        $this->assertSame(175.0, $financials['extra_charges']);
        $this->assertSame(5175.0, $financials['grand_total']);
        $this->assertSame(4000.0, $financials['deposited']);
        $this->assertSame(1175.0, $financials['remaining']);
    }

    public function test_due_report_uses_all_deposits_while_activity_report_uses_date_window(): void
    {
        $booking = new Booking();
        $booking->setRawAttributes([
            'check_in_date' => '2026-08-10',
            'check_out_date' => '2026-08-11',
            'created_at' => '2026-08-10 12:00:00',
            'discount_type' => 'none',
            'advance_payment' => 0,
        ], true);

        $booking->setRelation('bookingRooms', new Collection([(object) [
            'room_id' => 6,
            'price_per_night' => 3000,
            'check_in_date' => '2026-08-10',
            'check_out_date' => '2026-08-11',
        ]]));
        $booking->setRelation('payments', new Collection([
            new BookingPayment([
                'amount' => 1000,
                'type' => 'payment',
                'created_at' => '2026-08-11 13:00:00',
            ]),
        ]));

        $due = $booking->getReportFinancials('2026-08-17', '2026-08-17', true);
        $activity = $booking->getReportFinancials('2026-08-17', '2026-08-17');

        $this->assertSame(1000.0, $due['deposited']);
        $this->assertSame(2000.0, $due['remaining']);
        $this->assertEquals(0.0, $activity['deposited']);
        $this->assertEquals(2000.0, $activity['remaining']);
    }
}
