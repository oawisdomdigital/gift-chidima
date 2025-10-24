<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

include(__DIR__ . '/../db.php');

$input = json_decode(file_get_contents('php://input'), true);
$email = isset($input['email']) ? trim($input['email']) : null;
$name = isset($input['name']) ? trim($input['name']) : null;

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email']);
    exit;
}

try {
    // Create table if not exists
    $createSql = "CREATE TABLE IF NOT EXISTS newsletter_subscribers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) DEFAULT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $conn->query($createSql);

    // Insert or ignore if exists
    $stmt = $conn->prepare("INSERT INTO newsletter_subscribers (name, email) VALUES (?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name)");
    $stmt->bind_param("ss", $name, $email);
    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    echo json_encode(['success' => true, 'message' => 'Subscribed']);
} catch (Exception $e) {
    error_log('Newsletter subscribe error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
