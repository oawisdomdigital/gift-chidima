<?php
require_once(__DIR__ . '/../db.php');

// Create speaking topics table
$create_topics = "CREATE TABLE IF NOT EXISTS speaking_topics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Create bookme page sections table
$create_sections = "CREATE TABLE IF NOT EXISTS bookme_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(50) NOT NULL UNIQUE,
    title TEXT,
    subtitle TEXT,
    content TEXT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Create booking requests table
$create_bookings = "CREATE TABLE IF NOT EXISTS booking_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    organization VARCHAR(255),
    event_type VARCHAR(255),
    event_date VARCHAR(255),
    location VARCHAR(255),
    audience_size VARCHAR(100),
    topics TEXT,
    budget VARCHAR(255),
    message TEXT,
    status ENUM('pending', 'approved', 'declined') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Execute the table creations
if ($mysqli->query($create_topics) === FALSE) {
    echo "Error creating speaking_topics table: " . $mysqli->error . "\n";
}

if ($mysqli->query($create_sections) === FALSE) {
    echo "Error creating bookme_sections table: " . $mysqli->error . "\n";
}

if ($mysqli->query($create_bookings) === FALSE) {
    echo "Error creating booking_requests table: " . $mysqli->error . "\n";
}

// Insert default sections
$default_sections = [
    [
        'section_key' => 'hero',
        'title' => 'Book Dr. Gift',
        'subtitle' => "Let's Connect",
        'content' => json_encode([
            'description' => 'Invite Dr. Gift Chidima Nnamoko Orairu to speak at your event, coach your leadership team, or provide mentorship that transforms lives and organizations.',
            'bullet_points' => [
                'International keynote speaker with proven impact',
                'Executive leadership coach and business mentor',
                'Available for conferences, workshops, and private sessions'
            ]
        ])
    ],
    [
        'section_key' => 'topics',
        'title' => 'Speaking & Coaching Topics',
        'subtitle' => 'Dr. Gift brings transformative insights across multiple disciplines, empowering audiences to lead with purpose and create lasting impact.'
    ],
    [
        'section_key' => 'contact',
        'title' => 'Send a Booking Request',
        'subtitle' => 'Fill out the form below and we\'ll get back to you within 24-48 hours.'
    ],
    [
        'section_key' => 'direct_inquiries',
        'title' => 'Direct Booking Inquiries',
        'content' => json_encode([
            'description' => 'For press inquiries, special requests, or urgent bookings, please reach out directly via email.',
            'email' => 'bookings@drgiftnnamoko.com'
        ])
    ]
];

$stmt = $mysqli->prepare("INSERT IGNORE INTO bookme_sections (section_key, title, subtitle, content) VALUES (?, ?, ?, ?)");

foreach ($default_sections as $section) {
    $content = isset($section['content']) ? $section['content'] : null;
    $stmt->bind_param('ssss', $section['section_key'], $section['title'], $section['subtitle'], $content);
    $stmt->execute();
}

// Insert default speaking topics
$default_topics = [
    ['Lightbulb', 'Leadership & Transformation', 'Strategic leadership principles for lasting impact'],
    ['Users', 'Women in Business', 'Empowering women entrepreneurs and executives'],
    ['Target', 'Purpose & Personal Growth', 'Discovering and fulfilling your God-given purpose'],
    ['Heart', 'Mentorship & Youth Empowerment', 'Building the next generation of leaders'],
    ['Sparkles', 'Faith & Resilience in Leadership', 'Leading with integrity and unwavering faith'],
    ['BookOpen', 'Publishing & Media Innovation', 'Transforming narratives and amplifying voices']
];

$stmt = $mysqli->prepare("INSERT IGNORE INTO speaking_topics (icon, title, description, sort_order) VALUES (?, ?, ?, ?)");

foreach ($default_topics as $index => $topic) {
    $stmt->bind_param('sssi', $topic[0], $topic[1], $topic[2], $index);
    $stmt->execute();
}

echo "Setup completed successfully!";
?>