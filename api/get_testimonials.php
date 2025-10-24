<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include('../db.php');

// Fetch the section heading/subheading (id = 1)
$section = $conn->query("SELECT section_heading, section_subheading FROM testimonials WHERE id = 1")->fetch_assoc();

// Fetch testimonial cards (id > 1)
$result = $conn->query("SELECT quote, author, role FROM testimonials WHERE id > 1");
$testimonials = [];

while ($row = $result->fetch_assoc()) {
    $testimonials[] = $row;
}

echo json_encode([
    "heading" => $section['section_heading'] ?? '',
    "subheading" => $section['section_subheading'] ?? '',
    "testimonials" => $testimonials
]);

$conn->close();
?>
