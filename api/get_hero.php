<?php
// Explicit CORS headers (allow all origins for development)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Vary: Origin');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

require_once('api.php');

try {
    $sql = "SELECT * FROM hero_section ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception($conn->error);
    }

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Return the relative image path - frontend will handle URL construction
        if (!empty($row['image_path'])) {
            $row['image'] = 'uploads/' . $row['image_path'];
        }
        sendResponse($row);
    } else {
        sendError('No data found', 404);
    }
} catch (Exception $e) {
    sendError('Database error: ' . $e->getMessage());
}
?>
