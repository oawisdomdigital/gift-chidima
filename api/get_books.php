<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include('../db.php');

$base_url = "http://localhost/myapp/"; // adjust to your app's base URL

$sql = "SELECT * FROM books ORDER BY id DESC";
$result = $conn->query($sql);

$books = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['key_lessons'] = json_decode($row['key_lessons'], true) ?: [];

        // Make the cover_image and file_url fully accessible
        $row['cover_image'] = $row['cover_image'] ? $base_url . $row['cover_image'] : null;
        $row['file_url'] = $row['file_url'] ? $base_url . $row['file_url'] : null;

        $books[] = $row;
    }
}

echo json_encode($books);
$conn->close();
?>
