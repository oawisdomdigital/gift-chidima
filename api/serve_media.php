<?php
ini_set('max_execution_time', '0');
ini_set('memory_limit', '-1');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Range");
header("Access-Control-Expose-Headers: Content-Length, Content-Range, Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

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
