<?php
// add_cors_all.php
// Prepend explicit CORS headers to all API files in this directory (except a safe skip list).
// Creates a .bak backup for each file modified.

$dir = __DIR__;
$files = glob($dir . '/*.php');
$skip = [
    'add_cors_all.php',
    'update_api_files.php',
    'cors.php',
    'api.php'
];

$corsBlock = <<<'PHP'
// Explicit CORS headers inserted by add_cors_all.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

PHP;

foreach ($files as $file) {
    $base = basename($file);
    if (in_array($base, $skip)) {
        echo "Skipping: $base\n";
        continue;
    }
    $content = file_get_contents($file);
    if ($content === false) {
        echo "Failed to read: $base\n";
        continue;
    }
    if (stripos($content, 'Access-Control-Allow-Origin') !== false) {
        echo "Already has CORS header, skipping: $base\n";
        continue;
    }
    $pos = strpos($content, "<?php");
    if ($pos === false) {
        echo "No PHP tag found, skipping: $base\n";
        continue;
    }
    $insertPos = $pos + strlen("<?php");
    if (substr($content, $insertPos, 1) === "\n") {
        $insertPos++;
    }
    $newContent = substr($content, 0, $insertPos) . "\n" . $corsBlock . substr($content, $insertPos);

    // backup
    copy($file, $file . '.bak');
    $res = file_put_contents($file, $newContent);
    if ($res === false) {
        echo "Failed to write: $base\n";
    } else {
        echo "Updated: $base\n";
    }
}

echo "Done. Backups saved as *.bak\n";
