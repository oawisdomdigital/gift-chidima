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
// Protect admin API: require session-based admin auth
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$type = $_GET['type'] ?? '';

if (!$id || !$type) {
    echo json_encode(['success' => false, 'message' => 'Missing id or type']);
    exit();
}

if ($type === 'ad') {
    $stmt = $mysqli->prepare("SELECT id, name, type, status, body, created_at, updated_at FROM advertisements WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    if ($row) {
        echo json_encode(['success' => true, 'data' => ['type' => 'ad', 'detail' => $row]]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ad not found']);
    }
    exit();
}

if ($type === 'subscriber') {
    $stmt = $mysqli->prepare("SELECT id, name, email, created_at FROM newsletter_subscribers WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    if ($row) {
        echo json_encode(['success' => true, 'data' => ['type' => 'subscriber', 'detail' => $row]]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Subscriber not found']);
    }
    exit();
}

if ($type === 'post') {
    $stmt = $mysqli->prepare("SELECT id, title, body, created_at, author FROM posts WHERE id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        if ($row) {
            echo json_encode(['success' => true, 'data' => ['type' => 'post', 'detail' => $row]]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Post not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'DB error: ' . $mysqli->error]);
    }
    exit();
}

if ($type === 'booking') {
    $stmt = $mysqli->prepare("SELECT id, name, email, organization, event_type, event_date, location, audience_size, topics, budget, message, status, created_at FROM booking_requests WHERE id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        if ($row) {
            echo json_encode(['success' => true, 'data' => ['type' => 'booking', 'detail' => $row]]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Booking not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'DB error: ' . $mysqli->error]);
    }
    exit();
}

if ($type === 'gallery') {
    $stmt = $mysqli->prepare("SELECT id, title, src, thumbnail, type, description, is_embedded, created_at FROM gallery WHERE id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        if ($row) {
            echo json_encode(['success' => true, 'data' => ['type' => 'gallery', 'detail' => $row]]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gallery item not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'DB error: ' . $mysqli->error]);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Unsupported activity type']);
