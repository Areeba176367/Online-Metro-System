<?php
// Database configuration - matches your XAMPP MySQL setup
 $host = 'localhost';
 $dbname = 'oms.db';
 $username = 'root';
 $password = '';  // Default XAMPP MySQL has no password

 // pdo php data objects
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
// handle database error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// fetch data
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['passenger_id']);
}

// Helper function to redirect
function redirect($url) {
    header("Location: $url");
    exit();
}
?>