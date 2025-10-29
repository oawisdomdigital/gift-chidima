<?php
// Error logging configuration
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/debug.log');
error_reporting(E_ALL);

// Custom error handler
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $error_message = date('Y-m-d H:i:s') . " - Error [$errno]: $errstr in $errfile on line $errline\n";
    error_log($error_message);
    
    // Don't show errors in production
    if (strpos($_SERVER['HTTP_HOST'], 'infinityfree.me') !== false) {
        return true;
    }
    
    return false; // Let PHP handle the error in development
}

// Register the custom error handler
set_error_handler("customErrorHandler");

// Exception handler
function customExceptionHandler($exception) {
    $error_message = date('Y-m-d H:i:s') . " - Uncaught Exception: " . 
                    $exception->getMessage() . " in " . 
                    $exception->getFile() . " on line " . 
                    $exception->getLine() . "\n" . 
                    "Stack trace: " . $exception->getTraceAsString() . "\n";
    error_log($error_message);
    
    // In production, show generic error
    if (strpos($_SERVER['HTTP_HOST'], 'infinityfree.me') !== false) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['error' => 'An unexpected error occurred']);
        exit;
    }
    
    // In development, show detailed error
    throw $exception;
}

// Register the exception handler
set_exception_handler("customExceptionHandler");