<?php
session_start();
require_once('auth.php');
require_once('../db.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Get the ad first to delete the image if it exists
    $query = "SELECT image_url FROM advertisements WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ad = $result->fetch_assoc();
    
    // Delete the image file if it exists
    if ($ad && $ad['image_url'] && file_exists('../' . $ad['image_url'])) {
        unlink('../' . $ad['image_url']);
    }
    
    // Delete the ad from database
    $query = "DELETE FROM advertisements WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        header('Location: ads_list.php?deleted=1');
    } else {
        header('Location: ads_list.php?error=1');
    }
} else {
    header('Location: ads_list.php');
}
exit();