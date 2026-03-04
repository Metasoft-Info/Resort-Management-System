<?php
/**
 * Debug room IDs and check what's happening
 */
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Room;
use App\Models\BookingRoom;

echo "<h2>Room Information</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Room ID</th><th>Room Number</th><th>Type</th><th>Price</th></tr>";

$rooms = Room::with('roomType')->orderBy('room_number')->get();
foreach ($rooms as $room) {
    echo "<tr>";
    echo "<td><strong>{$room->id}</strong></td>";
    echo "<td>{$room->room_number}</td>";
    echo "<td>" . ($room->roomType->name ?? 'N/A') . "</td>";
    echo "<td>৳" . number_format($room->roomType->base_price ?? 0) . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>Recent BookingRoom Entries</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Booking ID</th><th>Room ID</th><th>Room Number</th><th>Price</th><th>Created At</th></tr>";

$bookingRooms = BookingRoom::with('room')->orderByDesc('id')->limit(20)->get();
foreach ($bookingRooms as $br) {
    echo "<tr>";
    echo "<td>{$br->id}</td>";
    echo "<td>#{$br->booking_id}</td>";
    echo "<td>{$br->room_id}</td>";
    echo "<td>" . ($br->room->room_number ?? 'N/A') . "</td>";
    echo "<td>৳" . number_format($br->price_per_night) . "</td>";
    echo "<td>{$br->created_at}</td>";
    echo "</tr>";
}
echo "</table>";

// Check Laravel log for recent booking entries
echo "<h2>Recent Log Entries (last 50 lines)</h2>";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = file($logFile);
    $lastLines = array_slice($lines, -50);
    echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 400px; overflow: auto;'>";
    foreach ($lastLines as $line) {
        // Highlight room-related entries
        if (stripos($line, 'room') !== false || stripos($line, 'booking') !== false) {
            echo "<span style='background: yellow;'>" . htmlspecialchars($line) . "</span>";
        } else {
            echo htmlspecialchars($line);
        }
    }
    echo "</pre>";
} else {
    echo "<p>Log file not found</p>";
}
