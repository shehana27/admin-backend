<?php
header('Content-Type: application/json');

// Database configuration
$config = [
    'db_path' => __DIR__ . '/../db.sqlite', // Path to your SQLite database file
    'timeout' => 5 // Connection timeout in seconds
];

try {
    // Attempt SQLite connection
    $db = new PDO("sqlite:" . $config['db_path'], null, null, [
        PDO::ATTR_TIMEOUT => $config['timeout'],
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Test connection with a simple query
    $version = $db->query('SELECT sqlite_version()')->fetchColumn();
    $encoding = $db->query('PRAGMA encoding')->fetchColumn();
    
    // Get database stats
    $fileSize = filesize($config['db_path']);
    $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);

    // Prepare success response
    $response = [
        'success' => true,
        'message' => 'SQLite connection successful',
        'database' => [
            'path' => $config['db_path'],
            'version' => $version,
            'encoding' => $encoding,
            'file_size' => $fileSize,
            'tables' => $tables
        ]
    ];

    // Close connection (PDO doesn't need explicit close)
    $db = null;

    // Send success response
    http_response_code(200);
    echo json_encode($response, JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    // Prepare error response
    $response = [
        'success' => false,
        'error' => [
            'code' => $e->getCode(),
            'message' => $e->getMessage()
        ],
        'config' => [
            'db_path' => $config['db_path']
        ]
    ];

    // Send error response
    http_response_code(500);
    echo json_encode($response, JSON_PRETTY_PRINT);
}