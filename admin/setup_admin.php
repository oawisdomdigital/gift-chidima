<?php
require_once(__DIR__ . '/../db.php');

// WARNING: Run this once locally to create admin_users table and seed a default admin.
// After running, delete or protect this file.

$create = "CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($mysqli->query($create) === FALSE) {
    echo 'Failed to create table: ' . $mysqli->error;
    exit;
}

// Insert default admin if not exists
$defaultUser = 'admin';
$defaultPass = 'password'; // change after setup
$hash = password_hash($defaultPass, PASSWORD_DEFAULT);

$stmt = $mysqli->prepare("INSERT IGNORE INTO admin_users (username, password_hash, name) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $defaultUser, $hash, $defaultUser);
if ($stmt->execute()) {
    echo "Admin user ensured. Username: {$defaultUser}, Password: {$defaultPass}\n";
    echo "Please change the password immediately by editing the database or through an admin UI.\n";
} else {
    echo 'Failed to insert default admin: ' . $stmt->error;
}

?>