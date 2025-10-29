<?php
require_once __DIR__ . '/api.php';

try {
    // Ensure we have DB connection
    if (!$conn) {
        throw new Exception(mysqli_connect_error());
    }

    $sql = "SELECT title, subtitle, description, button1_text, button1_link, button2_text, button2_link FROM final_cta LIMIT 1";
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception($conn->error);
    }

    if ($result->num_rows > 0) {
        sendResponse($result->fetch_assoc());
    } else {
        sendError("No CTA data found", 404);
    }
} catch (Exception $e) {
    sendError($e->getMessage(), 500);
}
?>
