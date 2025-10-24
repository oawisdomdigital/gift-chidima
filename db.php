<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "gift_chidima";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
// Provide $mysqli alias for legacy code that expects $mysqli
$mysqli = $conn;

// Ensure proper charset
$mysqli->set_charset('utf8mb4');
?>
