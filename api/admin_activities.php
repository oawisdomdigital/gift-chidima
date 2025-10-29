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
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Query params
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = intval($_GET['per_page'] ?? 12);
if ($per_page <= 0) $per_page = 12;
$typeFilter = trim($_GET['type'] ?? ''); // e.g. 'ad', 'subscriber', 'post', 'booking', 'gallery'
$days = intval($_GET['days'] ?? 0); // if >0, limit to last N days

$cutoffSql = '';
if ($days > 0) {
    // created_at >= NOW() - INTERVAL ? DAY
    $cutoffSql = " AND created_at >= DATE_SUB(NOW(), INTERVAL " . intval($days) . " DAY) ";
}

$activities = [];

/*
 * For simplicity we fetch a reasonable amount of recent items from each source
 * then merge and sort in PHP and slice for pagination. This is acceptable for
 * small-to-medium admin dashboards.
 *
 * If you have many rows, consider implementing a proper UNION+ORDER+LIMIT
 * query in SQL or a materialized 'activities' table.
 */

// helper to run query and push items
function push_rows($mysqli, $sql, &$activities, $typeKeyMap = []) {
    $res = $mysqli->query($sql);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $activities[] = $row;
        }
    }
}

// ADS
if ($typeFilter === '' || $typeFilter === 'ad') {
    $sql = "SELECT id, name AS title, type AS subtype, status, created_at, updated_at, 'ad' AS type FROM advertisements WHERE 1=1 {$cutoffSql} ORDER BY created_at DESC LIMIT 100";
    push_rows($mysqli, $sql, $activities);
}

// SUBSCRIBERS
if ($typeFilter === '' || $typeFilter === 'subscriber') {
    $sql = "SELECT id, email AS title, name AS subtype, created_at, NULL AS updated_at, 'subscriber' AS type FROM newsletter_subscribers WHERE 1=1 {$cutoffSql} ORDER BY created_at DESC LIMIT 100";
    push_rows($mysqli, $sql, $activities);
}

// POSTS: try both table names (posts or blog_posts)
if ($typeFilter === '' || $typeFilter === 'post') {
    // prefer blog_posts if exists
    $postTable = 'posts';
    $check = $mysqli->query("SHOW TABLES LIKE 'blog_posts'");
    if ($check && $check->num_rows > 0) $postTable = 'blog_posts';

    // guard in case it doesn't exist
    $res = $mysqli->query("SHOW TABLES LIKE '" . $mysqli->real_escape_string($postTable) . "'");
    if ($res && $res->num_rows > 0) {
        $sql = "SELECT id, title AS title, NULL AS subtype, created_at, NULL AS updated_at, 'post' AS type FROM {$postTable} WHERE 1=1 {$cutoffSql} ORDER BY created_at DESC LIMIT 100";
        push_rows($mysqli, $sql, $activities);
    }
}

// BOOKINGS
if ($typeFilter === '' || $typeFilter === 'booking') {
    $sql = "SELECT id, COALESCE(name, email) AS title, email AS subtype, created_at, NULL AS updated_at, 'booking' AS type FROM booking_requests WHERE 1=1 {$cutoffSql} ORDER BY created_at DESC LIMIT 100";
    push_rows($mysqli, $sql, $activities);
}

// GALLERY
if ($typeFilter === '' || $typeFilter === 'gallery') {
    $sql = "SELECT id, COALESCE(title, type, 'Media') AS title, type AS subtype, created_at, NULL AS updated_at, 'gallery' AS type FROM gallery WHERE 1=1 {$cutoffSql} ORDER BY created_at DESC LIMIT 100";
    push_rows($mysqli, $sql, $activities);
}

// Normalise rows to unified structure and parse created_at to timestamp for sorting
$normalized = [];
foreach ($activities as $r) {
    $created_at = $r['created_at'] ?? null;
    $ts = $created_at ? strtotime($created_at) : 0;
    $normalized[] = [
        'type' => $r['type'] ?? ($r['subtype'] ?? 'other'),
        'id' => $r['id'] ?? null,
        'title' => $r['title'] ?? '',
        'subtype' => $r['subtype'] ?? ($r['status'] ?? ''),
        'status' => $r['status'] ?? null,
        'created_at' => $created_at,
        'updated_at' => $r['updated_at'] ?? null,
        'ts' => $ts
    ];
}

// Sort by timestamp desc
usort($normalized, function($a,$b){
    return ($b['ts'] <=> $a['ts']);
});

// Remove ts before sending
$total = count($normalized);

// Pagination slice
$total_pages = (int) ceil($total / $per_page);
if ($page > $total_pages && $total_pages > 0) $page = $total_pages;
$offset = ($page - 1) * $per_page;
$slice = array_slice($normalized, $offset, $per_page);

// strip ts
foreach ($slice as &$s) {
    unset($s['ts']);
}

echo json_encode([
    'success' => true,
    'data' => $slice,
    'meta' => [
        'page' => $page,
        'per_page' => $per_page,
        'total' => $total,
        'total_pages' => $total_pages
    ]
]);
exit();
