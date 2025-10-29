<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/api.php';

$result = $conn->query("SELECT * FROM gallery ORDER BY created_at DESC");
if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => $conn->error]);
    exit;
}

$media = [];
while ($row = $result->fetch_assoc()) {
    $row['is_embedded'] = (int)$row['is_embedded'];
    $row['type'] = $row['type'] ?: (strpos($row['src'], 'youtube.com') !== false || strpos($row['src'], 'youtu.be') !== false ? 'video' : 'image');
    $media[] = $row;
}

echo json_encode($media);
