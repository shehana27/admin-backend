<?php
header('Content-Type: application/json');

// Database connection
try {
    $conn = new PDO("sqlite:" . __DIR__ . "/../db.sqlite");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
}

// Verify admin session
session_start();
if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized access']));
}

try {
    // Prepare and execute SQLite query
    $stmt = $conn->prepare(
        "SELECT b.*, e.event_name, e.event_datetime 
         FROM bookings b 
         JOIN events e ON b.event_id = e.id"
    );
    $stmt->execute();
    
    // Fetch all results
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the data
    $formattedBookings = array_map(function($booking) {
        return [
            'customer' => $booking['customer_name'],
            'email' => $booking['customer_email'],
            'booking_date' => $booking['booking_date'],
            'event' => $booking['event_name'],
            'event_date' => $booking['event_datetime'],
            'tickets' => (int)$booking['quantity'],
            'total' => (float)$booking['total_price']
        ];
    }, $bookings);
    
    // Return successful response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'bookings' => $formattedBookings,
        'count' => count($formattedBookings)
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => $e->getMessage()
    ]);
}