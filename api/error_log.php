<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/debug.log');

// Function to log debugging info
function debug_log($message) {
    error_log(print_r($message, true));
}
?>