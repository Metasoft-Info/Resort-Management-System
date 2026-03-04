<?php
/**
 * Quick fix for booking total amount
 */
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;

$bookingId = $_GET['booking_id'] ?? null;

if (!$bookingId) {
    echo "Usage: ?booking_id=3";
    exit;
}

$booking = Booking::find($bookingId);
if (!$booking) {
    echo "Booking not found";
    exit;
}

// Recalculate total from rooms
$nights = \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date));
$nights = max(1, $nights);

$roomsTotal = 0;
foreach ($booking->bookingRooms as $br) {
    $roomsTotal += $br->price_per_night * $nights;
}

// Update amounts
$oldTotal = $booking->total_amount;
$booking->total_amount = $roomsTotal;
$booking->remaining_payment = max(0, $roomsTotal - $booking->advance_payment);

// Update payment status
if ($booking->advance_payment >= $roomsTotal) {
    $booking->payment_status = 'paid';
    $booking->remaining_payment = 0;
} elseif ($booking->advance_payment > 0) {
    $booking->payment_status = 'partial';
} else {
    $booking->payment_status = 'pending';
}

$booking->save();

echo "<h2>Booking #{$bookingId} Fixed!</h2>";
echo "<p>Old Total: ৳" . number_format($oldTotal) . "</p>";
echo "<p>New Total: ৳" . number_format($booking->total_amount) . "</p>";
echo "<p>Remaining: ৳" . number_format($booking->remaining_payment) . "</p>";
echo "<p>Status: {$booking->payment_status}</p>";
echo "<p>Rooms: " . $booking->getAllRooms()->pluck('room_number')->implode(', ') . "</p>";
