<?php
require_once __DIR__ . '/api.php';

try {
    // Ensure we have DB connection
    if (!$conn) {
        throw new Exception(mysqli_connect_error());
    }
    $section = $conn->query("SELECT section_heading, section_subheading FROM awards WHERE id=1");
    if (!$section) {
        throw new Exception($conn->error);
    }
    $sectionData = $section->fetch_assoc();

    $awards = $conn->query("SELECT award_title, award_icon FROM awards WHERE award_title IS NOT NULL");
    if (!$awards) {
        throw new Exception($conn->error);
    }
    $awardsData = $awards->fetch_all(MYSQLI_ASSOC);

    $media = $conn->query("SELECT media_logo FROM awards WHERE media_logo IS NOT NULL");
    if (!$media) {
        throw new Exception($conn->error);
    }
    $mediaData = $media->fetch_all(MYSQLI_ASSOC);

    sendResponse([
        'heading' => $sectionData['section_heading'] ?? '',
        'subheading' => $sectionData['section_subheading'] ?? '',
        'awards' => $awardsData,
        'media' => $mediaData
    ]);
} catch (Exception $e) {
    sendError('Database error: ' . $e->getMessage());
}
