<?php
error_reporting(0);
header('Content-Type: text/html; charset=utf-8');

function parseEnv($path) {
    $env = [];
    if (file_exists($path)) {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $env[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
            }
        }
    }
    return $env;
}

$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) $envPath = __DIR__ . '/.env';
$env = parseEnv($envPath);

try {
    $pdo = new PDO("mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']};charset=utf8mb4", $env['DB_USERNAME'], $env['DB_PASSWORD']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("DB Error: " . $e->getMessage());
}

$bookingId = $_GET['booking_id'] ?? null;
$roomNumber = $_GET['room_number'] ?? null;
$action = $_GET['action'] ?? null;

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Add Room to Booking</title>
<style>body{font-family:Arial;margin:20px;}table{margin:10px 0;}th{background:#333;color:white;}td,th{padding:8px;}
.success{color:green;font-weight:bold;}.error{color:red;}.warn{color:orange;}</style></head><body>";
echo "<h1>🏨 Add Room to Booking</h1>";

// Process add
if ($action === 'add' && $bookingId && $roomNumber) {
    $stmt = $pdo->prepare("SELECT id, price_per_night FROM rooms WHERE room_number = ?");
    $stmt->execute([$roomNumber]);
    $room = $stmt->fetch(PDO::FETCH_OBJ);
    
    $stmt = $pdo->prepare("SELECT id FROM bookings WHERE id = ?");
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch(PDO::FETCH_OBJ);
    
    if (!$booking) {
        echo "<p class='error'>❌ Booking #{$bookingId} not found!</p>";
    } elseif (!$room) {
        echo "<p class='error'>❌ Room {$roomNumber} not found!</p>";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM booking_rooms WHERE booking_id = ? AND room_id = ?");
        $stmt->execute([$bookingId, $room->id]);
        
        if ($stmt->fetch()) {
            echo "<p class='warn'>⚠️ Room {$roomNumber} already in Booking #{$bookingId}</p>";
        } else {
            $stmt = $pdo->prepare("INSERT INTO booking_rooms (booking_id, room_id, price_per_night, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
            $stmt->execute([$bookingId, $room->id, $room->price_per_night]);
            
            $stmt = $pdo->prepare("SELECT SUM(price_per_night) as total FROM booking_rooms WHERE booking_id = ?");
            $stmt->execute([$bookingId]);
            $total = $stmt->fetch(PDO::FETCH_OBJ)->total;
            
            $stmt = $pdo->prepare("SELECT advance_amount FROM bookings WHERE id = ?");
            $stmt->execute([$bookingId]);
            $advance = $stmt->fetch(PDO::FETCH_OBJ)->advance_amount ?? 0;
            
            $due = max(0, $total - $advance);
            $stmt = $pdo->prepare("UPDATE bookings SET total_amount = ?, due_amount = ? WHERE id = ?");
            $stmt->execute([$total, $due, $bookingId]);
            
            echo "<p class='success'>✅ Room {$roomNumber} added to Booking #{$bookingId}! New total: ৳" . number_format($total) . "</p>";
        }
    }
    echo "<hr>";
}

// Show bookings
echo "<h2>📋 Current Bookings</h2>";
echo "<table border='1' style='border-collapse:collapse;'>";
echo "<tr><th>ID</th><th>Customer</th><th>Phone</th><th>Dates</th><th>Rooms</th><th>Amount</th><th>Status</th></tr>";

$stmt = $pdo->query("SELECT * FROM bookings ORDER BY id DESC");
$bookings = $stmt->fetchAll(PDO::FETCH_OBJ);

foreach ($bookings as $b) {
    $roomStmt = $pdo->prepare("SELECT r.room_number FROM booking_rooms br JOIN rooms r ON br.room_id = r.id WHERE br.booking_id = ?");
    $roomStmt->execute([$b->id]);
    $rooms = $roomStmt->fetchAll(PDO::FETCH_COLUMN);
    
    $roomStr = !empty($rooms) ? implode(', ', $rooms) : "<span class='error'>NO ROOMS!</span>";
    $name = $b->guest_name ?? 'N/A';
    $phone = $b->guest_phone ?? '';
    $checkIn = $b->check_in_date ?? 'N/A';
    $checkOut = $b->check_out_date ?? '';
    
    echo "<tr><td><strong>#{$b->id}</strong></td><td>{$name}</td><td>{$phone}</td><td>{$checkIn}<br>to<br>{$checkOut}</td><td>{$roomStr}</td><td>৳" . number_format($b->total_amount) . "<br>Adv: ৳" . number_format($b->advance_amount ?? 0) . "</td><td>{$b->status}</td></tr>";
}
echo "</table>";

// Rooms
echo "<h2>🛏️ Available Rooms</h2>";
echo "<table border='1' style='border-collapse:collapse;'>";
echo "<tr><th>Room</th><th>Price</th></tr>";
$stmt = $pdo->query("SELECT * FROM rooms ORDER BY room_number");
foreach ($stmt->fetchAll(PDO::FETCH_OBJ) as $room) {
    echo "<tr><td><strong>{$room->room_number}</strong></td><td>৳" . number_format($room->price_per_night) . "</td></tr>";
}
echo "</table>";

// Form
echo "<h2>➕ Add Room to Booking</h2>";
echo "<form method='get' style='background:#eee;padding:20px;display:inline-block;border-radius:10px;'>";
echo "<label>Booking ID: </label><input type='number' name='booking_id' required style='padding:8px;width:80px;'><br><br>";
echo "<label>Room Number: </label><input type='text' name='room_number' placeholder='201' required style='padding:8px;width:80px;'><br><br>";
echo "<input type='hidden' name='action' value='add'>";
echo "<button type='submit' style='padding:12px 25px;background:green;color:white;border:none;cursor:pointer;font-size:16px;border-radius:5px;'>➕ Add Room</button>";
echo "</form></body></html>";
