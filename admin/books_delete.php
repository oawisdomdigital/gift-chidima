<?php
// Start session before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('../db.php');
require_once(__DIR__ . '/includes/auth.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // First get the book info to delete files
    $stmt = $mysqli->prepare("SELECT cover_image, file_url FROM books WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($book = $result->fetch_assoc()) {
        // Delete physical files if they exist
        if ($book['cover_image']) {
            $coverPath = __DIR__ . '/../' . $book['cover_image'];
            if (file_exists($coverPath)) {
                unlink($coverPath);
            }
        }
        if ($book['file_url']) {
            $filePath = __DIR__ . '/../' . $book['file_url'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
    
    // Delete the database record
    $stmt = $mysqli->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header('Location: books_list.php?msg=deleted');
        exit;
    } else {
        header('Location: books_list.php?error=delete_failed');
        exit;
    }
} else {
    header('Location: books_list.php');
    exit;
}
?>
