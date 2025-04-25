<?php
header('Content-Type: application/json');

// Database connection
try {
    $conn = new PDO("sqlite:" . __DIR__ . "/../db.sqlite");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed']));
}

// Verify admin session
session_start();
if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized access']));
}

// Get event ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
if (!$id) {
    http_response_code(400);
    die(json_encode(['error' => 'Event ID required']));
}

try {
    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$event) {
        http_response_code(404);
        die(json_encode(['error' => 'Event not found']));
    }
    
    // Format response
    $response = [
        'success' => true,
        'event' => [
            'id' => (int)$event['id'],
            'name' => $event['event_name'],
            'datetime' => $event['event_datetime'],
            'location' => $event['location'],
            'price' => (float)$event['price'],
            'max_tickets' => isset($event['max_tickets']) ? (int)$event['max_tickets'] : null,
            'description' => isset($event['description']) ? $event['description'] : null,
            'image_path' => isset($event['image_path']) ? $event['image_path'] : null
        ]
    ];
    
    http_response_code(200);
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}