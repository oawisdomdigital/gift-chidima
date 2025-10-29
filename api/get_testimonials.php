<?php
require_once __DIR__ . '/api.php';

try {
    // Ensure we have DB connection
    if (!$conn) {
        throw new Exception(mysqli_connect_error());
    }

    // Fetch the section heading/subheading (id = 1)
    $sectionQuery = $conn->query("SELECT section_heading, section_subheading FROM testimonials WHERE id = 1");
    if (!$sectionQuery) {
        throw new Exception($conn->error);
    }
    $section = $sectionQuery->fetch_assoc();

    // Fetch testimonial cards (id > 1)
    $result = $conn->query("SELECT quote, author, role FROM testimonials WHERE id > 1");
    if (!$result) {
        throw new Exception($conn->error);
    }

    $testimonials = [];
    while ($row = $result->fetch_assoc()) {
        $testimonials[] = $row;
    }

    sendResponse([
        "heading" => $section['section_heading'] ?? '',
        "subheading" => $section['section_subheading'] ?? '',
        "testimonials" => $testimonials
    ]);
} catch (Exception $e) {
    sendError('Database error: ' . $e->getMessage());
}
?>
