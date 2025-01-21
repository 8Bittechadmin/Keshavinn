<?php
// Debugging start
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', '', 'hotel_booking');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $check_in = $_POST['check_in'] ?? null;
    $check_out = $_POST['check_out'] ?? null;

    if (!$check_in || !$check_out) {
        echo json_encode(['error' => 'Missing check-in or check-out date']);
        exit;
    }

    echo "Received check-in: $check_in, check-out: $check_out"; // Debug
    $sql = "SELECT * FROM rooms WHERE room_id NOT IN (
                SELECT room_id FROM bookings 
                WHERE ('$check_in' < check_out AND '$check_out' > check_in)
            )";
    $result = $conn->query($sql);

    $available_rooms = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $available_rooms[] = $row;
        }
    }

    echo json_encode($available_rooms);
}
?>
