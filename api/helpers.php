<?php
// Standard response functions
function sendResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
    exit;
}

function sendError($message, $status = 400) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => $message
    ]);
    exit;
}

// Database helper functions
function checkDbConnection($conn) {
    if (!$conn) {
        sendError("Database connection failed: " . mysqli_connect_error(), 500);
    }
}

function handleDbError($conn, $query) {
    sendError("Database error: " . mysqli_error($conn) . "\nQuery: " . $query, 500);
}

// Media URL helper
function getMediaUrl($path) {
    if (empty($path)) return '';
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        return $path;
    }
    return '/myapp/' . ltrim($path, '/');
}