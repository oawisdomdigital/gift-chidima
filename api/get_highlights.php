<?php
require_once __DIR__ . '/api.php';

try {
    // Fetch section heading/subheading (id = 1)
    $sectionQuery = $conn->query("SELECT section_heading, section_subheading FROM key_highlights WHERE id = 1");
    if (!$sectionQuery) {
        throw new Exception($conn->error);
    }
    $section = $sectionQuery->fetch_assoc();

    // Fetch all highlight cards (id > 1)
    $result = $conn->query("SELECT icon, title, description FROM key_highlights WHERE id > 1");
    if (!$result) {
        throw new Exception($conn->error);
    }
    
    $highlights = [];
    while ($row = $result->fetch_assoc()) {
        $highlights[] = $row;
    }

    sendResponse([
        "heading" => $section['section_heading'] ?? null,
        "subheading" => $section['section_subheading'] ?? null,
        "highlights" => $highlights
    ]);
} catch (Exception $e) {
    sendError('Database error: ' . $e->getMessage());
} finally {
    $conn->close();
}
?>
