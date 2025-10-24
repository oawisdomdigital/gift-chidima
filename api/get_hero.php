<?php
// ✅ Must be at the very top before any output or spaces
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Include database
include('../db.php');

$sql = "SELECT * FROM hero_section ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
  $row = $result->fetch_assoc();
  // Construct absolute image path if present
  if (!empty($row['image_path'])) {
    $row['image'] = 'http://localhost/myapp/uploads/' . $row['image_path'];
  }
  echo json_encode($row);
} else {
  echo json_encode(['error' => 'No data found']);
}
?>
