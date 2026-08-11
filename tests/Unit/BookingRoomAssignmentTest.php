<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\BookingRoom;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class BookingRoomAssignmentTest extends TestCase
{
    public function test_legacy_room_is_merged_with_normalized_room_rows_without_duplicates(): void
    {
        $booking = new Booking(['room_id' => 4]);
        $booking->setRelation('bookingRooms', new Collection([
            new BookingRoom(['room_id' => 19]),
            new BookingRoom(['room_id' => 4]),
        ]));

        $this->assertSame([19, 4], $booking->getAllRoomIds());
    }

    public function test_legacy_room_is_returned_when_it_is_not_in_normalized_rows(): void
    {
        $booking = new Booking(['room_id' => 4]);
        $booking->setRelation('bookingRooms', new Collection([
            new BookingRoom(['room_id' => 19]),
        ]));

        $this->assertSame([19, 4], $booking->getAllRoomIds());
    }
}
