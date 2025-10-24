<?php
// ✅ Must be at the very top before any output or spaces
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include('../db.php');

// Fetch section heading/subheading (id = 1)
$section = $conn->query("SELECT section_heading, section_subheading FROM key_highlights WHERE id = 1")->fetch_assoc();

// Fetch all highlight cards (id > 1)
$result = $conn->query("SELECT icon, title, description FROM key_highlights WHERE id > 1");
$highlights = [];
while ($row = $result->fetch_assoc()) {
    $highlights[] = $row;
}

echo json_encode([
    "heading" => $section['section_heading'] ?? null,
    "subheading" => $section['section_subheading'] ?? null,
    "highlights" => $highlights
]);
$conn->close();
?>
