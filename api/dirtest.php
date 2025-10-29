<?php
require_once __DIR__ . '/api.php';

try {
    $uploadDirs = [
        'uploads/books',
        'uploads/gallery',
        'uploads/highlights',
        'uploads/media',
        'uploads/ventures'
    ];

    $results = [];
    foreach ($uploadDirs as $dir) {
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $dir;
        $results[$dir] = [
            'exists' => is_dir($fullPath),
            'writable' => is_writable($fullPath),
            'permissions' => substr(sprintf('%o', fileperms($fullPath)), -4),
            'absolute_path' => $fullPath
        ];
    }

    sendResponse([
        'upload_directories' => $results,
        'document_root' => $_SERVER['DOCUMENT_ROOT'],
        'script_path' => __DIR__
    ]);
} catch (Exception $e) {
    sendError('Directory check failed: ' . $e->getMessage());
}