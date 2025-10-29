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

$category = isset($_GET['category']) ? trim($_GET['category']) : null;
$featured = isset($_GET['featured']) ? (int)$_GET['featured'] : null; // 1 or 0
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
$q = isset($_GET['q']) ? trim($_GET['q']) : null;
$order = "publish_date DESC";

// Build base query
$sql = "SELECT id, slug, title, excerpt, featured_image, category, tags, author, publish_date, read_time, featured
        FROM blog_posts WHERE 1=1";
$params = [];
$types = "";

// category filter
if ($category && $category !== 'All') {
    $sql .= " AND category = ?";
    $types .= "s";
    $params[] = $category;
}

// featured filter
if ($featured === 1 || $featured === 0) {
    $sql .= " AND featured = ?";
    $types .= "i";
    $params[] = $featured;
}

// search query (title or excerpt)
if ($q) {
    $sql .= " AND (title LIKE ? OR excerpt LIKE ?)";
    $types .= "ss";
    $params[] = "%$q%";
    $params[] = "%$q%";
}

$sql .= " ORDER BY $order";

if ($limit && $limit > 0) {
    $sql .= " LIMIT ?";
    $types .= "i";
    $params[] = $limit;
}

// Prepare statement
$stmt = $conn->prepare($sql);
if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}" . dirname($_SERVER['SCRIPT_NAME'], 2) . "/";

// Collect rows
$posts = [];
while ($row = $result->fetch_assoc()) {
    // decode tags JSON if present
    $tags = [];
    if (!empty($row['tags'])) {
        $decoded = json_decode($row['tags'], true);
        if (is_array($decoded)) $tags = $decoded;
    }

    // convert date to ISO-like string
    $publishDate = $row['publish_date'] ? date('Y-m-d', strtotime($row['publish_date'])) : null;

    // featured_image: convert to absolute URL if present
    $featuredImage = null;
    if (!empty($row['featured_image'])) {
        // If featured_image already an absolute URL, use it; otherwise compose base path
        if (preg_match('/^https?:\\/\\//', $row['featured_image'])) {
            $featuredImage = $row['featured_image'];
        } else {
            // ensure no leading ../
            $featuredImage = $baseUrl . ltrim($row['featured_image'], '/');
        }
    }

    $posts[] = [
        'id' => (string)$row['id'],
        'slug' => $row['slug'],
        'title' => $row['title'],
        'excerpt' => $row['excerpt'],
        'featuredImage' => $featuredImage,
        'category' => $row['category'],
        'tags' => $tags,
        'author' => $row['author'],
        'publishDate' => $publishDate,
        'readTime' => $row['read_time'],
        'featured' => (bool)$row['featured'],
    ];
}

echo json_encode(['success' => true, 'posts' => $posts], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
