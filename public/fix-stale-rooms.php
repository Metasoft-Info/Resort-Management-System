<?php
/**
 * Fix all bookings that have duplicate/stale booking_room entries
 */
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BookingRoom;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

echo "<h2>Checking for Stale BookingRoom Entries</h2>";

// Find bookings where booking_rooms were created BEFORE the booking was created
$staleEntries = DB::select("
    SELECT br.id as br_id, br.booking_id, br.room_id, br.created_at as br_created, 
           b.created_at as booking_created, r.room_number
    FROM booking_rooms br
    INNER JOIN bookings b ON br.booking_id = b.id
    INNER JOIN rooms r ON br.room_id = r.id
    WHERE br.created_at < b.created_at
    ORDER BY br.booking_id, br.created_at
");

echo "<p>Found " . count($staleEntries) . " stale entries (booking_room created BEFORE booking)</p>";

if (count($staleEntries) > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>BR ID</th><th>Booking ID</th><th>Room</th><th>BR Created</th><th>Booking Created</th><th>Status</th></tr>";
    
    foreach ($staleEntries as $entry) {
        $bgColor = '#ffebee';
        echo "<tr style='background: {$bgColor}'>";
        echo "<td>{$entry->br_id}</td>";
        echo "<td>#{$entry->booking_id}</td>";
        echo "<td>{$entry->room_number}</td>";
        echo "<td>{$entry->br_created}</td>";
        echo "<td>{$entry->booking_created}</td>";
        echo "<td>⚠️ STALE - will be deleted</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    if (isset($_GET['fix']) && $_GET['fix'] === '1') {
        $deleted = 0;
        foreach ($staleEntries as $entry) {
            BookingRoom::where('id', $entry->br_id)->delete();
            $deleted++;
        }
        
        echo "<div style='background: green; color: white; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "✅ Deleted {$deleted} stale entries!";
        echo "</div>";
        
        // Now recalculate affected bookings
        $affectedBookingIds = array_unique(array_column($staleEntries, 'booking_id'));
        echo "<h3>Recalculating affected bookings...</h3>";
        
        foreach ($affectedBookingIds as $bookingId) {
            $booking = Booking::find($bookingId);
            if ($booking) {
                $nights = \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date));
                $nights = max(1, $nights);
                
                $roomsTotal = 0;
                foreach ($booking->bookingRooms as $br) {
                    $roomsTotal += $br->price_per_night * $nights;
                }
                
                $booking->total_amount = $roomsTotal;
                $booking->remaining_payment = max(0, $roomsTotal - $booking->advance_payment);
                
                if ($booking->advance_payment >= $roomsTotal) {
                    $booking->payment_status = 'paid';
                } elseif ($booking->advance_payment > 0) {
                    $booking->payment_status = 'partial';
                } else {
                    $booking->payment_status = 'pending';
                }
                
                $booking->save();
                echo "<p>✅ Booking #{$bookingId} recalculated - Total: ৳" . number_format($booking->total_amount) . "</p>";
            }
        }
    } else {
        echo "<p><a href='?fix=1' style='background: red; color: white; padding: 15px 30px; border-radius: 5px; text-decoration: none; font-weight: bold;'>Click to FIX all stale entries</a></p>";
    }
} else {
    echo "<div style='background: green; color: white; padding: 15px; border-radius: 5px;'>✅ No stale entries found!</div>";
}
