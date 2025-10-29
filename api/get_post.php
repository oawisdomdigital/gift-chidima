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

$id = isset($_GET['id']) ? trim($_GET['id']) : null;   // numeric id
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : null;

if (!$id && !$slug) {
    echo json_encode(['success' => false, 'error' => 'No id or slug provided']);
    exit;
}

if ($id) {
    $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->bind_param("i", $id);
} else {
    $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE slug = ?");
    $stmt->bind_param("s", $slug);
}

$stmt->execute();
$res = $stmt->get_result();
$post = $res->fetch_assoc();

if (!$post) {
    echo json_encode(['success' => false, 'error' => 'Post not found']);
    exit;
}

$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}" . dirname($_SERVER['SCRIPT_NAME'], 2) . "/";

$tags = [];
if (!empty($post['tags'])) {
    $decoded = json_decode($post['tags'], true);
    if (is_array($decoded)) $tags = $decoded;
}

$featuredImage = null;
if (!empty($post['featured_image'])) {
    if (preg_match('/^https?:\\/\\//', $post['featured_image'])) {
        $featuredImage = $post['featured_image'];
    } else {
        $featuredImage = $baseUrl . ltrim($post['featured_image'], '/');
    }
}

$publishDate = $post['publish_date'] ? date('Y-m-d', strtotime($post['publish_date'])) : null;

$result = [
    'id' => (string)$post['id'],
    'slug' => $post['slug'],
    'title' => $post['title'],
    'excerpt' => $post['excerpt'],
    'content' => $post['content'],
    'featuredImage' => $featuredImage,
    'category' => $post['category'],
    'tags' => $tags,
    'author' => $post['author'],
    'publishDate' => $publishDate,
    'readTime' => $post['read_time'],
    'featured' => (bool)$post['featured']
];

echo json_encode(['success' => true, 'post' => $result], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
