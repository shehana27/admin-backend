<?php
include "config.php";
session_start();

// Set JSON content type
header('Content-Type: application/json');

// Verify admin session
if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized access']));
}

// Only accept PUT or POST requests
if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'POST'])) {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed']));
}

// Get event ID from URL parameter
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
if (!$id) {
    http_response_code(400);
    die(json_encode(['error' => 'Event ID required']));
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!$input || !isset($input['name']) || !isset($input['datetime']) || 
    !isset($input['location']) || !isset($input['price'])) {
    http_response_code(400);
    die(json_encode(['error' => 'All fields are required']));
}

// Sanitize and validate data
$name = trim($input['name']);
$datetime = trim($input['datetime']);
$location = trim($input['location']);
$price = (float)$input['price'];

if (empty($name) || empty($datetime) || empty($location) || $price <= 0) {
    http_response_code(400);
    die(json_encode(['error' => 'Invalid input data']));
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

    // Update the event
    $stmt = $conn->prepare("UPDATE events SET 
                          event_name = ?, 
                          event_datetime = ?, 
                          location = ?, 
                          price = ? 
                          WHERE id = ?");
    $stmt->execute([$name, $datetime, $location, $price, $id]);
    
    http_response_code(200);
    echo json_encode([
        'success' => true, 
        'message' => 'Event updated successfully',
        'redirect' => 'manageEvents.php'
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database error: ' . $e->getMessage()]));
}