<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\BookingRoom;
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
}
