<?php
require_once('../db.php');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$type = isset($_GET['type']) ? $_GET['type'] : null;
$current_date = date('Y-m-d');

// Base query for active advertisements
$query = "SELECT id, name, type, content, image_url, link_url 
          FROM advertisements 
          WHERE status = 'active' 
          AND (start_date IS NULL OR start_date <= ?) 
          AND (end_date IS NULL OR end_date >= ?)";

// Add type filter if specified
if ($type) {
    $query .= " AND type = ?";
}

$query .= " ORDER BY RAND() LIMIT 1";

$stmt = $mysqli->prepare($query);

if ($type) {
    $stmt->bind_param('sss', $current_date, $current_date, $type);
} else {
    $stmt->bind_param('ss', $current_date, $current_date);
}

$stmt->execute();
$result = $stmt->get_result();
$ad = $result->fetch_assoc();

if ($ad) {
    echo json_encode([
        'success' => true,
        'data' => $ad
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No active advertisements found'
    ]);
}