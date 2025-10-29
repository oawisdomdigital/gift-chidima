<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

// Function to require login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /admin/login.php');
        exit;
    }
}

// Function to safely handle database operations
function dbQuery($conn, $query, $params = [], $types = '') {
    try {
        $stmt = $conn->prepare($query);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        return $stmt->get_result();
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

// Function to safely handle file uploads
function handleUpload($file, $uploadDir, $allowedTypes = ['image/jpeg', 'image/png', 'image/gif']) {
    try {
        // Validate file
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new Exception('Invalid file parameter');
        }

        // Check upload errors
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new Exception('No file sent');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception('File size exceeds limit');
            default:
                throw new Exception('Unknown file upload error');
        }

        // Check file size (5MB limit)
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception('File too large (max 5MB)');
        }

        // Check MIME type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception('Invalid file type');
        }

        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception("Failed to create upload directory");
            }
        }

        // Generate safe filename
        $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($file['name']));
        $targetPath = $uploadDir . $fileName;

        // Move file
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception("Failed to move uploaded file");
        }

        return $fileName;
    } catch (Exception $e) {
        error_log("Upload error: " . $e->getMessage());
        return false;
    }
}
