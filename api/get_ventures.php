<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include('../db.php');

// ✅ Fetch section heading/subheading (id = 1)
$section = $conn->query("SELECT section_heading, section_subheading FROM ventures WHERE id = 1")->fetch_assoc();

// ✅ Fetch venture cards (id > 1)
$result = $conn->query("SELECT logo, name, description FROM ventures WHERE id > 1");
$ventures = [];
while ($row = $result->fetch_assoc()) {
    $ventures[] = $row;
}

// ✅ Return JSON
echo json_encode([
    "heading" => $section['section_heading'] ?? '',
    "subheading" => $section['section_subheading'] ?? '',
    "ventures" => $ventures
]);

$conn->close();
?>
