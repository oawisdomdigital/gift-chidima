<?php
require_once __DIR__ . '/api.php';

try {
    $sql = "SELECT * FROM biography_section ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception($conn->error);
    }

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        // Handle any image paths if present
        if (!empty($data['image'])) {
            $data['image'] = 'uploads/' . $data['image'];
        }
        sendResponse($data);
    } else {
        sendResponse(null);
    }
} catch (Exception $e) {
    sendError('Database error: ' . $e->getMessage());
} finally {
    $conn->close();
}
?>
