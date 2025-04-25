<?php
session_start();
header('Content-Type: application/json');

// Verify admin session
if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized access']));
}

// Database connection
try {
    $conn = new PDO("sqlite:" . __DIR__ . "/../db.sqlite");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed']));
}

// Handle different request methods
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Fetch all events
        try {
            $stmt = $conn->query("SELECT id, event_name, event_datetime FROM events");
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'events' => $events,
                'actions' => [
                    'add' => 'addEvent.php',
                    'view' => 'viewEvent.php',
                    'edit' => 'editEvent.php',
                    'delete' => 'deleteEvent.php'
                ]
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch events']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}