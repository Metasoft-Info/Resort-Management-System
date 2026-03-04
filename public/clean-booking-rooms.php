<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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

$action = $_GET['action'] ?? null;

echo "<h1>🧹 Clean Booking Rooms</h1>";

if ($action === 'clean') {
    // Delete all booking_rooms
    $deleted = $pdo->exec("DELETE FROM booking_rooms");
    
    // Reset all bookings totals - only update total_amount
    $pdo->exec("UPDATE bookings SET total_amount = 0");
    
    // Also reset auto_increment
    $pdo->exec("ALTER TABLE booking_rooms AUTO_INCREMENT = 1");
    
    echo "<p style='color:green; font-size:20px;'>✅ Deleted {$deleted} booking_rooms entries!</p>";
    echo "<p style='color:green; font-size:20px;'>✅ All bookings reset to ৳0!</p>";
    echo "<p><a href='add-room-to-booking.php'>Go to Add Room Page</a></p>";
} else {
    // Show current state
    echo "<h2>Current booking_rooms entries:</h2>";
    
    $stmt = $pdo->query("SELECT br.*, r.room_number FROM booking_rooms br LEFT JOIN rooms r ON br.room_id = r.id ORDER BY br.id");
    $entries = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    if (empty($entries)) {
        echo "<p style='color:green;'>✅ No booking_rooms entries found. Table is clean!</p>";
    } else {
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
        echo "<tr><th>BR ID</th><th>Booking</th><th>Room</th><th>Price</th><th>Created</th></tr>";
        foreach ($entries as $e) {
            echo "<tr>";
            echo "<td>{$e->id}</td>";
            echo "<td>#{$e->booking_id}</td>";
            echo "<td>" . ($e->room_number ?? 'N/A') . "</td>";
            echo "<td>৳" . number_format($e->price_per_night ?? 0) . "</td>";
            echo "<td>" . ($e->created_at ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p>Total: " . count($entries) . " entries</p>";
    }
    
    echo "<br><br>";
    echo "<a href='?action=clean' onclick=\"return confirm('Are you sure? This will delete ALL room assignments!')\" style='padding:15px 30px; background:red; color:white; text-decoration:none; font-size:18px; border-radius:5px;'>🗑️ DELETE ALL</a>";
}
