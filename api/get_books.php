<?php
require_once(__DIR__ . '/api.php');

try {
    $sql = "SELECT * FROM books ORDER BY id DESC";
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception($conn->error);
    }

    $books = [];
    while ($row = $result->fetch_assoc()) {
        $row['key_lessons'] = json_decode($row['key_lessons'], true) ?: [];
        
        // Return relative paths - frontend will handle URL construction
        $row['cover_image'] = $row['cover_image'] ?: null;
        $row['file_url'] = $row['file_url'] ?: null;

        $books[] = $row;
    }

    sendResponse(['books' => $books]);
} catch (Exception $e) {
    sendError('Database error: ' . $e->getMessage());
}
