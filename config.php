<?php
try {
    // Use the same path as your friend's config
    $conn = new PDO("sqlite:" . __DIR__ . "/../db.sqlite");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}
?>