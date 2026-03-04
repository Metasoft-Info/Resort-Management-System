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

echo "<h1>🔄 Reset All Bookings</h1>";

if ($action === 'reset') {
    // Delete all related data first
    $pdo->exec("DELETE FROM booking_rooms");
    $pdo->exec("DELETE FROM booking_payments");
    $pdo->exec("DELETE FROM additional_guests");
    $pdo->exec("DELETE FROM bookings");
    
    // Reset auto-increment
    $pdo->exec("ALTER TABLE booking_rooms AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE booking_payments AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE additional_guests AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE bookings AUTO_INCREMENT = 1");
    
    echo "<p style='color:green; font-size:24px;'>✅ All bookings data has been deleted!</p>";
    echo "<p style='color:green;'>✅ booking_rooms - CLEARED</p>";
    echo "<p style='color:green;'>✅ booking_payments - CLEARED</p>";
    echo "<p style='color:green;'>✅ additional_guests - CLEARED</p>";
    echo "<p style='color:green;'>✅ bookings - CLEARED</p>";
    echo "<p style='color:green;'>✅ All auto-increment counters reset to 1</p>";
    echo "<br><a href='/admin/bookings'>← Go to Bookings Page</a>";
} else {
    // Show current count
    $bookingCount = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
    $roomCount = $pdo->query("SELECT COUNT(*) FROM booking_rooms")->fetchColumn();
    $paymentCount = $pdo->query("SELECT COUNT(*) FROM booking_payments")->fetchColumn();
    
    echo "<h2>Current Data:</h2>";
    echo "<p>📋 Bookings: <strong>{$bookingCount}</strong></p>";
    echo "<p>🛏️ Booking Rooms: <strong>{$roomCount}</strong></p>";
    echo "<p>💰 Payments: <strong>{$paymentCount}</strong></p>";
    
    echo "<br><br>";
    echo "<a href='?action=reset' onclick=\"return confirm('⚠️ WARNING: This will DELETE ALL BOOKINGS! Are you sure?')\" style='padding:20px 40px; background:red; color:white; text-decoration:none; font-size:20px; border-radius:10px;'>🗑️ DELETE ALL BOOKINGS</a>";
}
