<?php
require_once(__DIR__ . '/api.php');

try {
    $sql = "SELECT * FROM footer_content LIMIT 1";
    $result = $mysqli->query($sql);
    
    if (!$result) {
        throw new Exception($mysqli->error);
    }

    $footer = $result->fetch_assoc();
    
    if (!$footer) {
        throw new Exception("Footer content not found");
    }

    sendResponse(['footer' => $footer]);
} catch (Exception $e) {
    sendError('Error fetching footer content: ' . $e->getMessage());
}