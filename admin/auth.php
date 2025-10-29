<?php
// Centralized auth check for admin pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Accept either flag used across the codebase for backward compatibility
// Some pages set `admin_logged_in` while newer includes check `admin_id`.
if (empty($_SESSION['admin_logged_in']) && empty($_SESSION['admin_id'])) {
    // redirect to login page
    header('Location: login.php');
    exit();
}
?>