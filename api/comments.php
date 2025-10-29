<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/api.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $post_id = intval($_GET['post_id'] ?? 0);

    $stmt = $conn->prepare("SELECT id, name, email, comment, created_at FROM comments WHERE post_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }

    echo json_encode(['success' => true, 'comments' => $comments]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = intval($_POST['post_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

    // Debug log
    error_log("Received POST data: " . json_encode([
        'post_id' => $post_id,
        'name' => $name,
        'email' => $email,
        'comment' => $comment,
        'raw_post' => $_POST
    ]));

    if (!$post_id || !$name || !$email || !$comment) {
        $missing = [];
        if (!$post_id) $missing[] = 'post_id';
        if (!$name) $missing[] = 'name';
        if (!$email) $missing[] = 'email';
        if (!$comment) $missing[] = 'comment';
        
        echo json_encode([
            'success' => false, 
            'message' => 'Missing required fields: ' . implode(', ', $missing),
            'received' => $_POST
        ]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO comments (post_id, name, email, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $post_id, $name, $email, $comment);
    $ok = $stmt->execute();

    if ($ok) {
        echo json_encode([
            'success' => true,
            'comment' => [
                'id' => $conn->insert_id,
                'name' => $name,
                'email' => $email,
                'comment' => $comment,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
