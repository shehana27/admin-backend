<?php
include "config.php";
session_start();

// Verify admin session
if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized access']));
}

// Only accept DELETE or POST requests
if (!in_array($_SERVER['REQUEST_METHOD'], ['DELETE', 'POST'])) {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed']));
}

// Get event ID from URL parameter
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
if (!$id) {
    http_response_code(400);
    die(json_encode(['error' => 'Event ID required']));
}

try {
    // Check if event exists
    $stmt = $conn->prepare("SELECT id FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$event) {
        http_response_code(404);
        die(json_encode(['error' => 'Event not found']));
    }

    // Check for existing bookings
    $stmt = $conn->prepare("SELECT COUNT(*) as booking_count FROM bookings WHERE event_id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['booking_count'] > 0) {
        http_response_code(409);
        die(json_encode(['error' => 'Cannot delete event with existing bookings']));
    }

    // Delete the event
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$id]);
    
    http_response_code(200);
    echo json_encode(['success' => true, 'redirect' => 'manageEvents.php']);
    
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database error: ' . $e->getMessage()]));
}