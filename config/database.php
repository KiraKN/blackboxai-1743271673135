<?php
define('DB_PATH', __DIR__ . '/../db/window_manufacturer.db');

function getDBConnection() {
    try {
        $conn = new PDO('sqlite:' . DB_PATH);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Enable WAL mode for better concurrency
        $conn->exec('PRAGMA journal_mode=WAL;');
        // Set busy timeout
        $conn->exec('PRAGMA busy_timeout=3000;');
        return $conn;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?>