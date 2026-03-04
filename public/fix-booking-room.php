<?php
/**
 * Fix Booking #3 - Remove incorrectly added room
 * 
 * Run this once to fix the booking where room 203 was incorrectly added
 * when only room 303 should have been booked.
 * 
 * Access via: https://tufanconventionresort.com/fix-booking-room.php?booking_id=3&remove_room=203
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Booking Room</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
        .success { color: green; background: #e8f5e9; padding: 15px; border-radius: 8px; margin: 10px 0; }
        .error { color: red; background: #ffebee; padding: 15px; border-radius: 8px; margin: 10px 0; }
        .info { color: #1565c0; background: #e3f2fd; padding: 15px; border-radius: 8px; margin: 10px 0; }
        .warning { color: #e65100; background: #fff3e0; padding: 15px; border-radius: 8px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f5f5f5; }
        .btn { display: inline-block; padding: 10px 20px; margin: 5px; border-radius: 5px; text-decoration: none; color: white; }
        .btn-danger { background: #dc3545; }
        .btn-primary { background: #007bff; }
        h1 { color: #333; }
    </style>
</head>
<body>
    <h1>🔧 Fix Booking Room Tool</h1>
    
<?php

$bookingId = $_GET['booking_id'] ?? null;
$removeRoom = $_GET['remove_room'] ?? null;
$confirm = $_GET['confirm'] ?? null;

// List all bookings with multiple rooms if no booking_id specified
if (!$bookingId) {
    echo '<div class="info"><strong>ℹ️ Usage:</strong> Add ?booking_id=X to view a specific booking</div>';
    
    // Show bookings with multiple rooms
    $bookingsWithMultipleRooms = Booking::whereHas('bookingRooms', function($q) {}, '>=', 2)->get();
    
    echo '<h2>Bookings with Multiple Rooms</h2>';
    if ($bookingsWithMultipleRooms->isEmpty()) {
        echo '<p>No bookings with multiple rooms found.</p>';
    } else {
        echo '<table>';
        echo '<tr><th>ID</th><th>Customer</th><th>Rooms</th><th>Dates</th><th>Action</th></tr>';
        foreach ($bookingsWithMultipleRooms as $b) {
            $rooms = $b->getAllRooms()->pluck('room_number')->implode(', ');
            echo "<tr>";
            echo "<td>#{$b->id}</td>";
            echo "<td>{$b->customer_name}</td>";
            echo "<td>{$rooms}</td>";
            echo "<td>{$b->check_in_date->format('Y-m-d')} to {$b->check_out_date->format('Y-m-d')}</td>";
            echo "<td><a href='?booking_id={$b->id}' class='btn btn-primary'>View</a></td>";
            echo "</tr>";
        }
        echo '</table>';
    }
    exit;
}

// Load the booking
$booking = Booking::with(['bookingRooms.room'])->find($bookingId);

if (!$booking) {
    echo '<div class="error">❌ Booking #' . htmlspecialchars($bookingId) . ' not found!</div>';
    exit;
}

echo '<h2>Booking #' . $booking->id . ' Details</h2>';
echo '<table>';
echo '<tr><th>Customer</th><td>' . htmlspecialchars($booking->customer_name) . '</td></tr>';
echo '<tr><th>Phone</th><td>' . htmlspecialchars($booking->customer_phone) . '</td></tr>';
echo '<tr><th>Check-in</th><td>' . $booking->check_in_date->format('Y-m-d') . '</td></tr>';
echo '<tr><th>Check-out</th><td>' . $booking->check_out_date->format('Y-m-d') . '</td></tr>';
echo '<tr><th>Total Amount</th><td>৳' . number_format($booking->total_amount) . '</td></tr>';
echo '<tr><th>Remaining</th><td>৳' . number_format($booking->remaining_payment) . '</td></tr>';
echo '</table>';

// Show current rooms
$bookingRooms = $booking->bookingRooms;
$nights = Carbon::parse($booking->check_in_date)->diffInDays(Carbon::parse($booking->check_out_date));
$nights = max(1, $nights);

echo '<h3>Current Rooms (' . $bookingRooms->count() . ')</h3>';
echo '<table>';
echo '<tr><th>Room #</th><th>Price/Night</th><th>Total (' . $nights . ' nights)</th><th>Action</th></tr>';
foreach ($bookingRooms as $br) {
    $roomTotal = $br->price_per_night * $nights;
    echo "<tr>";
    echo "<td><strong>Room {$br->room->room_number}</strong></td>";
    echo "<td>৳" . number_format($br->price_per_night) . "</td>";
    echo "<td>৳" . number_format($roomTotal) . "</td>";
    echo "<td><a href='?booking_id={$bookingId}&remove_room={$br->room->room_number}' class='btn btn-danger'>Remove</a></td>";
    echo "</tr>";
}
echo '</table>';

// Handle room removal
if ($removeRoom) {
    $room = Room::where('room_number', $removeRoom)->first();
    
    if (!$room) {
        echo '<div class="error">❌ Room ' . htmlspecialchars($removeRoom) . ' not found!</div>';
        exit;
    }
    
    $bookingRoomToRemove = BookingRoom::where('booking_id', $bookingId)
        ->where('room_id', $room->id)
        ->first();
    
    if (!$bookingRoomToRemove) {
        echo '<div class="error">❌ Room ' . htmlspecialchars($removeRoom) . ' is not in this booking!</div>';
        exit;
    }
    
    if (!$confirm) {
        // Show confirmation
        $amountToDeduct = $bookingRoomToRemove->price_per_night * $nights;
        $newTotal = $booking->total_amount - $amountToDeduct;
        $newRemaining = $booking->remaining_payment - $amountToDeduct;
        
        echo '<div class="warning">';
        echo '<h3>⚠️ Confirm Room Removal</h3>';
        echo '<p>Are you sure you want to remove <strong>Room ' . htmlspecialchars($removeRoom) . '</strong> from Booking #' . $bookingId . '?</p>';
        echo '<p>Amount to deduct: <strong>৳' . number_format($amountToDeduct) . '</strong></p>';
        echo '<p>New Total: ৳' . number_format($booking->total_amount) . ' → <strong>৳' . number_format($newTotal) . '</strong></p>';
        echo '<p>New Remaining: ৳' . number_format($booking->remaining_payment) . ' → <strong>৳' . number_format(max(0, $newRemaining)) . '</strong></p>';
        echo '<a href="?booking_id=' . $bookingId . '&remove_room=' . $removeRoom . '&confirm=1" class="btn btn-danger">Yes, Remove Room</a>';
        echo '<a href="?booking_id=' . $bookingId . '" class="btn btn-primary">Cancel</a>';
        echo '</div>';
    } else {
        // Perform removal
        try {
            DB::beginTransaction();
            
            $amountToDeduct = $bookingRoomToRemove->price_per_night * $nights;
            
            // Delete the booking room entry
            $bookingRoomToRemove->delete();
            
            // Update booking totals
            $booking->total_amount -= $amountToDeduct;
            $booking->remaining_payment = max(0, $booking->remaining_payment - $amountToDeduct);
            
            // Update notes to reflect the change
            $remainingRooms = $booking->fresh()->getAllRooms()->pluck('room_number')->implode(', ');
            $booking->notes = preg_replace('/\[Rooms:.*?\]/', '', $booking->notes);
            $booking->notes = trim($booking->notes) . " [Rooms: {$remainingRooms}]";
            
            // Recalculate payment status
            if ($booking->advance_payment >= $booking->total_amount) {
                $booking->payment_status = 'paid';
                $booking->remaining_payment = 0;
            }
            
            $booking->save();
            
            DB::commit();
            
            echo '<div class="success">';
            echo '<h3>✅ Room Removed Successfully!</h3>';
            echo '<p>Room ' . htmlspecialchars($removeRoom) . ' has been removed from Booking #' . $bookingId . '</p>';
            echo '<p>New Total: ৳' . number_format($booking->total_amount) . '</p>';
            echo '<p>New Remaining: ৳' . number_format($booking->remaining_payment) . '</p>';
            echo '</div>';
            
            echo '<a href="?booking_id=' . $bookingId . '" class="btn btn-primary">View Updated Booking</a>';
            
        } catch (\Exception $e) {
            DB::rollBack();
            echo '<div class="error">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}
?>

<br><br>
<a href="?" class="btn btn-primary">← Back to All Bookings</a>

</body>
</html>
