<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/plain');

// Direct database connection
$host = '127.0.0.1';
$dbname = 'tufanconx_tufan_resort';
$username = 'tufanconx_tufan_resort';
$password = 'Tufan@4682';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== All Bookings ===\n\n";
    
    $stmt = $pdo->query("SELECT id, name, phone, total_amount, status, created_at FROM bookings ORDER BY id");
    $bookings = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    foreach ($bookings as $b) {
        $roomStmt = $pdo->prepare("SELECT r.room_number FROM booking_rooms br JOIN rooms r ON br.room_id = r.id WHERE br.booking_id = ?");
        $roomStmt->execute([$b->id]);
        $rooms = $roomStmt->fetchAll(PDO::FETCH_COLUMN);
        
        $roomStr = !empty($rooms) ? implode(', ', $rooms) : "NO ROOMS!";
        
        echo "Booking #{$b->id}: {$b->name} | Rooms: {$roomStr} | Total: {$b->total_amount}\n";
    }
    
    echo "\n=== booking_rooms Table ===\n\n";
    
    $stmt = $pdo->query("SELECT br.*, r.room_number FROM booking_rooms br LEFT JOIN rooms r ON br.room_id = r.id ORDER BY br.id");
    $brs = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    foreach ($brs as $br) {
        $roomNum = $br->room_number ?? 'N/A';
        echo "BR#{$br->id}: Booking {$br->booking_id}, Room {$roomNum} (ID:{$br->room_id})\n";
    }
    
    if (empty($brs)) {
        echo "No booking_rooms entries!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
