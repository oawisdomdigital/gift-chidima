<?php
require_once(__DIR__ . '/../db.php');

$create = "CREATE TABLE IF NOT EXISTS footer_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    heading TEXT,
    description TEXT,
    email VARCHAR(255),
    linkedin_url VARCHAR(255),
    twitter_url VARCHAR(255),
    instagram_url VARCHAR(255),
    facebook_url VARCHAR(255),
    privacy_url VARCHAR(255),
    terms_url VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($mysqli->query($create) === FALSE) {
    die('Failed to create footer_content table: ' . $mysqli->error);
}

// Insert default content if table is empty
$check = $mysqli->query("SELECT id FROM footer_content LIMIT 1");
if ($check->num_rows === 0) {
    $default = $mysqli->prepare("INSERT INTO footer_content (
        heading, 
        description, 
        email,
        linkedin_url,
        twitter_url,
        instagram_url,
        facebook_url,
        privacy_url,
        terms_url
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $heading = "Dr. Gift Chidima Nnamoko Orairu";
    $description = "Empowering a new generation of African leaders through purpose, mentorship, and transformation.";
    $email = "contact@drgift.com";
    $linkedin = "#";
    $twitter = "#";
    $instagram = "#";
    $facebook = "#";
    $privacy = "/privacy";
    $terms = "/terms";

    $default->bind_param("sssssssss", 
        $heading, $description, $email, 
        $linkedin, $twitter, $instagram, $facebook,
        $privacy, $terms
    );

    if (!$default->execute()) {
        die('Failed to insert default footer content: ' . $default->error);
    }

    echo "Footer content table created and initialized with default content.\n";
} else {
    echo "Footer content table already exists with data.\n";
}
?>