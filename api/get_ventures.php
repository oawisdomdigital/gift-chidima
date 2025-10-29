<?php
require_once __DIR__ . '/api.php';

try {
    // Ensure we have DB connection
    if (!$conn) {
        throw new Exception(mysqli_connect_error());
    }

    // Fetch section heading/subheading (id = 1)
    $sectionQuery = $conn->query("SELECT section_heading, section_subheading FROM ventures WHERE id = 1");
    if (!$sectionQuery) {
        throw new Exception($conn->error);
    }
    $section = $sectionQuery->fetch_assoc();

    // Fetch venture cards (id > 1)
    $result = $conn->query("SELECT logo, name, description FROM ventures WHERE id > 1");
    if (!$result) {
        throw new Exception($conn->error);
    }

    $ventures = [];
    while ($row = $result->fetch_assoc()) {
        if (isset($row['logo'])) {
            $row['logo'] = getMediaUrl($row['logo']);
        }
        $ventures[] = $row;
    }

    sendResponse([
        "heading" => $section['section_heading'] ?? '',
        "subheading" => $section['section_subheading'] ?? '',
        "ventures" => $ventures
    ]);
} catch (Exception $e) {
    sendError($e->getMessage(), 500);
}
?>
