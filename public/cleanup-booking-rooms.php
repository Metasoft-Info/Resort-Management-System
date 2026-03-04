<?php
/**
 * Clean up orphaned booking_rooms entries
 */
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BookingRoom;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

echo "<h2>Cleaning Orphaned BookingRoom Entries</h2>";

// Find BookingRoom entries where the booking doesn't exist
$orphanedCount = BookingRoom::whereNotIn('booking_id', function($query) {
    $query->select('id')->from('bookings');
})->count();

echo "<p>Found {$orphanedCount} orphaned entries</p>";

if (isset($_GET['fix']) && $_GET['fix'] === '1') {
    $deleted = BookingRoom::whereNotIn('booking_id', function($query) {
        $query->select('id')->from('bookings');
    })->delete();
    
    echo "<div style='background: green; color: white; padding: 10px; border-radius: 5px;'>";
    echo "✅ Deleted {$deleted} orphaned entries!";
    echo "</div>";
} else {
    echo "<p><a href='?fix=1' style='background: red; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;'>Click to DELETE orphaned entries</a></p>";
}

// Also show current state
echo "<h2>Current BookingRoom Entries</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Booking ID</th><th>Booking Exists?</th><th>Room ID</th><th>Room Number</th><th>Created</th></tr>";

$entries = BookingRoom::with('room')->orderByDesc('id')->get();
foreach ($entries as $br) {
    $bookingExists = Booking::where('id', $br->booking_id)->exists();
    $bgColor = $bookingExists ? '#e8f5e9' : '#ffebee';
    echo "<tr style='background: {$bgColor}'>";
    echo "<td>{$br->id}</td>";
    echo "<td>#{$br->booking_id}</td>";
    echo "<td>" . ($bookingExists ? '✅ Yes' : '❌ NO - ORPHAN') . "</td>";
    echo "<td>{$br->room_id}</td>";
    echo "<td>" . ($br->room->room_number ?? 'N/A') . "</td>";
    echo "<td>{$br->created_at}</td>";
    echo "</tr>";
}
echo "</table>";
