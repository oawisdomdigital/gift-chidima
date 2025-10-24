<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON data
    $raw_data = file_get_contents("php://input");
    $data = json_decode($raw_data, true);

    if ($data === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data received']);
        exit;
    }

    // Map form fields to database fields
    $booking_data = [
        'name' => $data['fullName'] ?? '',
        'email' => $data['email'] ?? '',
        'organization' => $data['organization'] ?? '',
        'event_type' => $data['eventType'] ?? '',
        'event_date' => $data['preferredDate'] ?? '',
        'message' => $data['message'] ?? '',
        'location' => $data['location'] ?? '',
        'audience_size' => $data['audienceSize'] ?? '',
        'topics' => $data['topics'] ?? '',
        'budget' => $data['budget'] ?? ''
    ];

    // Validate required fields
    $required_fields = ['name', 'email', 'message'];
    foreach ($required_fields as $field) {
        if (empty($booking_data[$field])) {
            echo json_encode(['success' => false, 'message' => 'Missing required field: ' . $field]);
            exit;
        }
    }

    // Prepare the SQL statement
    $stmt = $mysqli->prepare("INSERT INTO booking_requests (name, email, organization, event_type, event_date, location, audience_size, topics, budget, message) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database preparation error: ' . $mysqli->error]);
        exit;
    }

    $stmt->bind_param("ssssssssss", 
        $booking_data['name'],
        $booking_data['email'],
        $booking_data['organization'],
        $booking_data['event_type'],
        $booking_data['event_date'],
        $booking_data['location'],
        $booking_data['audience_size'],
        $booking_data['topics'],
        $booking_data['budget'],
        $booking_data['message']
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Booking request submitted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
    
    $stmt->close();
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request method']);
?>