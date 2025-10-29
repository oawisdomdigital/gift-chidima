<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Use local database configuration in development
if (file_exists(__DIR__ . '/db.local.php')) {
    require_once(__DIR__ . '/db.local.php');
    return;
}

// Production database configuration
$host = "sql213.infinityfree.com";
$user = "if0_40258130";
$pass = "@Alid_91075800";
$dbname = "if0_40258130_gift";

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Test the connection
    if (!$conn->ping()) {
        throw new Exception("Database connection lost");
    }
} catch (Exception $e) {
    // Log the error
    error_log("Database connection error: " . $e->getMessage());
    
    // If this is an API request, send JSON error
    if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Database connection error']);
        exit;
    }
    
    // For non-API requests, show generic error
    die("Database connection error. Please try again later.");
}

// Provide $mysqli alias for legacy code that expects $mysqli
$mysqli = $conn;

// Ensure proper charset
$mysqli->set_charset('utf8mb4');
?>
