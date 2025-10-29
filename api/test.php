<?php
require_once __DIR__ . '/api.php';

try {
    // Basic configuration info
    $config = [
        'php_version' => PHP_VERSION,
        'extensions' => get_loaded_extensions(),
        'error_reporting' => error_reporting(),
        'display_errors' => ini_get('display_errors'),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'max_execution_time' => ini_get('max_execution_time'),
        'document_root' => $_SERVER['DOCUMENT_ROOT'],
        'script_filename' => $_SERVER['SCRIPT_FILENAME'],
        'headers_sent' => headers_sent(),
        'headers_list' => headers_list()
    ];
    
    sendResponse(['config' => $config]);
} catch (Exception $e) {
    sendError('Configuration test failed: ' . $e->getMessage());
}