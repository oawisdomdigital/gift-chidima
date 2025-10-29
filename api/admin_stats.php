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

// Simple stats: counts of advertisements, posts (if table exists), subscribers
$stats = [
    'ads' => 0,
    'subscribers' => 0,
    'posts' => 0,
];

// Ads count
$res = $mysqli->query("SELECT COUNT(*) AS cnt FROM advertisements");
if ($res) {
    $row = $res->fetch_assoc();
    $stats['ads'] = intval($row['cnt']);
}

// Subscribers count
$res = $mysqli->query("SELECT COUNT(*) AS cnt FROM newsletter_subscribers");
if ($res) {
    $row = $res->fetch_assoc();
    $stats['subscribers'] = intval($row['cnt']);
}

// Posts count (if posts table exists)
$res = $mysqli->query("SELECT COUNT(*) AS cnt FROM posts");
if ($res) {
    $row = $res->fetch_assoc();
    $stats['posts'] = intval($row['cnt']);
}

// Recent ad activity: last created ad date
$res = $mysqli->query("SELECT created_at FROM advertisements ORDER BY created_at DESC LIMIT 1");
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $stats['last_ad_created_at'] = $row['created_at'];
}

echo json_encode(['success' => true, 'data' => $stats]);
