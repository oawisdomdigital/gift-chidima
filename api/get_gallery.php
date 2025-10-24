<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include('../db.php');

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
