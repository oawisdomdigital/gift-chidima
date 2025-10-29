<?php
// Prevent any output before headers
ob_start();

// Force SSL if not localhost
if (!isset($_SERVER['HTTP_HOST']) || !in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1'])) {
    if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        exit();
    }
}

// Set CORS headers
// In development, allow localhost:5173 (Vite dev server)
if (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1'])) {
    header('Access-Control-Allow-Origin: http://localhost:5173');
} else {
    // In production, allow the main domain
    header('Access-Control-Allow-Origin: https://gift.infinityfree.me');
}

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Vary: Origin');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    ob_end_clean();
    exit();
}

// Ensure proper content type for API responses
header('Content-Type: application/json; charset=utf-8');