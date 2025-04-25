<?php
session_start();

// Database connection (SQLite)
try {
    $dbPath = __DIR__ . '/../db.sqlite';
    $conn = new PDO("sqlite:$dbPath");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle login request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    
    // Validate credentials against database
    try {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ? AND is_admin = 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin && password_verify($password, $admin['password'])) {
            // Successful login
            $_SESSION["admin"] = true;
            $_SESSION["admin_id"] = $admin['id'];
            header("Location: manageEvents.php");
            exit();
        } else {
            throw new Exception("Invalid credentials");
        }
    } catch (Exception $e) {
        http_response_code(401);
        die("Authentication failed: " . $e->getMessage());
    }
}

