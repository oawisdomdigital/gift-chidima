<?php
include('../db.php');

// Collect POST data
$title = $_POST['title'];
$subtitle = $_POST['subtitle'];
$description = $_POST['description'];
$button1_text = $_POST['button1_text'];
$button1_link = $_POST['button1_link'];
$button2_text = $_POST['button2_text'];
$button2_link = $_POST['button2_link'];

// Check if CTA exists
$check = $conn->query("SELECT id FROM final_cta LIMIT 1");

if ($check->num_rows > 0) {
    // Update existing record
    $row = $check->fetch_assoc();
    $id = $row['id'];

    $stmt = $conn->prepare("UPDATE final_cta 
        SET title=?, subtitle=?, description=?, 
            button1_text=?, button1_link=?, 
            button2_text=?, button2_link=? 
        WHERE id=?");
    $stmt->bind_param("sssssssi", $title, $subtitle, $description, $button1_text, $button1_link, $button2_text, $button2_link, $id);
    $stmt->execute();

    echo "<script>alert('CTA updated successfully!'); window.location='final_cta_form.php';</script>";

} else {
    // Insert new record
    $stmt = $conn->prepare("INSERT INTO final_cta 
        (title, subtitle, description, button1_text, button1_link, button2_text, button2_link) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $title, $subtitle, $description, $button1_text, $button1_link, $button2_text, $button2_link);
    $stmt->execute();

    echo "<script>alert('CTA added successfully!'); window.location='final_cta_form.php';</script>";
}
?>
