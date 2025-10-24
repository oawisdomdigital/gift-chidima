<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
include('../db.php');

// Get the latest banner (or you can select a specific one by ID)
$bannerQuery = $conn->query("SELECT * FROM store_banner ORDER BY id DESC LIMIT 1");
$banner = $bannerQuery->fetch_assoc();

try {
    if ($banner) {
        // Fetch all books linked to this banner
        $booksQuery = $conn->prepare("SELECT id, title, cover_label, cover_image FROM store_books WHERE banner_id = ?");
        if (!$booksQuery) {
            throw new Exception("Query preparation failed: " . $conn->error);
        }

        $booksQuery->bind_param("i", $banner['id']);
        if (!$booksQuery->execute()) {
            throw new Exception("Query execution failed: " . $booksQuery->error);
        }

        $booksResult = $booksQuery->get_result();
        $books = [];

        while ($row = $booksResult->fetch_assoc()) {
            // Handle the cover image path correctly
            if (!empty($row['cover_image'])) {
                // Remove any '../' and leading slashes
                $row['cover_image'] = trim(str_replace('../', '', $row['cover_image']), '/');
                
                // Verify if file exists
                $fullPath = __DIR__ . '/../' . $row['cover_image'];
                if (!file_exists($fullPath)) {
                    error_log("Image not found: " . $fullPath);
                    $row['cover_image'] = 'uploads/default_cover.png';
                }
            } else {
                $row['cover_image'] = 'uploads/default_cover.png';
            }
            $books[] = $row;
        }

        echo json_encode([
            "success" => true,
            "banner" => $banner,
            "books" => $books
        ], JSON_PRETTY_PRINT);
    } else {
        echo json_encode([
            "success" => false,
            "error" => "No banner found"
        ]);
    }
} catch (Exception $e) {
    error_log("Store Banner API Error: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "error" => "An error occurred while fetching the banner data"
    ]);
}
?>
