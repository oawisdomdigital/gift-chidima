<?php
// Base API setup - include this in all API files
require_once(__DIR__ . '/error_handler.php');
require_once(__DIR__ . '/../db.php');
require_once(__DIR__ . '/cors.php');

if (!headers_sent()) {
    header('Content-Type: application/json');
}

// Common error handler
function sendError($message, $code = 500) {
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'error' => $message
    ]);
    exit();
}

// Common success response
function sendResponse($data) {
    echo json_encode(array_merge(['success' => true], $data));
    exit();
}