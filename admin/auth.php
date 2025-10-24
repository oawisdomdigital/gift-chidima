<?php
// Centralized auth check for admin pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin_logged_in'])) {
    // redirect to login page
    header('Location: login.php');
    exit();
}
?>