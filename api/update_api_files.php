<?php
// Script to update all API files with new structure
$files = glob(__DIR__ . '/*.php');

$corsHeaders = <<<'EOD'
<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Explicit CORS headers - Allow from localhost:5173
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

// Set JSON content type
header('Content-Type: application/json; charset=utf-8');

EOD;

foreach ($files as $file) {
    if (basename($file) === 'update_api_files.php' || 
        basename($file) === 'api.php' || 
        basename($file) === 'cors.php') {
        continue;
    }

    $content = file_get_contents($file);
    
    // Skip if already updated
    if (strpos($content, "require_once('api.php')") !== false) {
        continue;
    }

    // Replace PHP opening tag and any existing headers with our CORS headers
    $content = preg_replace('/^<\?php.*?(require_once|include)/s', $corsHeaders . '$1', $content);

    // Replace simple echo json_encode with sendResponse
    $content = preg_replace(
        '/echo json_encode\((.*?)\);/s',
        'sendResponse($1);',
        $content
    );

    // Add basic error handling if not present
    if (strpos($content, 'try {') === false) {
        $content = preg_replace(
            '/(require_once\(\'api\.php\'\);)\s*/',
            "$1\n\ntry {",
            $content
        );
        $content = preg_replace(
            '/\?>/',
            "} catch (Exception \$e) {\n    sendError('Database error: ' . \$e->getMessage());\n}",
            $content
        );
    }

    file_put_contents($file, $content);
}

echo "API files updated successfully.\n";
?>