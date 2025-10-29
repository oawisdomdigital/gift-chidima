<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
     http_response_code(200);
     exit();
}
ini_set('max_execution_time', '0');
ini_set('memory_limit', '-1');

// Use centralized CORS handling for media endpoints
require_once __DIR__ . '/cors.php';

$file = isset($_GET['file']) ? urldecode($_GET['file']) : '';

$basePath = realpath(__DIR__ . '/../uploads/');
$fullPath = realpath($basePath . DIRECTORY_SEPARATOR . $file);

if (!$fullPath || strpos($fullPath, $basePath) !== 0 || !file_exists($fullPath)) {
    error_log("Media not found: " . $file);
    http_response_code(404);
    echo 'File not found';
    exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $fullPath);
finfo_close($finfo);

header("Content-Type: $mimeType");
header("Content-Disposition: inline; filename=\"" . basename($fullPath) . "\"");
header("Content-Length: " . filesize($fullPath));

if (strpos($mimeType, 'video/') === 0) {
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
}

readfile($fullPath);
