<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include('../db.php');

// Fetch CTA data (you can have a single row for the CTA content)
$sql = "SELECT title, subtitle, description, button1_text, button1_link, button2_text, button2_link FROM final_cta LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(["error" => "No CTA data found"]);
}
?>
