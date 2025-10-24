<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
include('../db.php');

$section = $conn->query("SELECT section_heading, section_subheading FROM awards WHERE id=1")->fetch_assoc();
$awards = $conn->query("SELECT award_title, award_icon FROM awards WHERE award_title IS NOT NULL")->fetch_all(MYSQLI_ASSOC);
$media = $conn->query("SELECT media_logo FROM awards WHERE media_logo IS NOT NULL")->fetch_all(MYSQLI_ASSOC);

echo json_encode([
  'heading' => $section['section_heading'] ?? '',
  'subheading' => $section['section_subheading'] ?? '',
  'awards' => $awards,
  'media' => $media
]);
