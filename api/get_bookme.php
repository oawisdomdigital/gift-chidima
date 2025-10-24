<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once('../db.php');

// Get all sections
$sections_result = $mysqli->query("SELECT * FROM bookme_sections");
$sections = [];
while ($row = $sections_result->fetch_assoc()) {
    $sections[$row['section_key']] = $row;
    if ($row['content']) {
        $sections[$row['section_key']]['content'] = json_decode($row['content'], true);
    }
}

// Get speaking topics
$topics_result = $mysqli->query("SELECT * FROM speaking_topics ORDER BY sort_order");
$topics = [];
while ($row = $topics_result->fetch_assoc()) {
    $topics[] = $row;
}

echo json_encode([
    'success' => true,
    'data' => [
        'sections' => $sections,
        'topics' => $topics
    ]
]);
?>