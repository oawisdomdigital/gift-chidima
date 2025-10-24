<?php
session_start();
require_once('../db.php');
if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$section_keys = ['hero', 'topics', 'contact', 'direct_inquiries'];

// POST handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($section_keys as $key) {
        $title = $_POST[$key . '_title'] ?? '';
        $subtitle = $_POST[$key . '_subtitle'] ?? '';
        $content = [];

        switch ($key) {
            case 'hero':
                $content['description'] = $_POST[$key . '_description'] ?? '';
                $content['bullet_points'] = array_filter(explode("\n", $_POST[$key . '_bullet_points'] ?? ''));
                break;
            case 'direct_inquiries':
                $content['description'] = $_POST[$key . '_description'] ?? '';
                $content['email'] = $_POST[$key . '_email'] ?? '';
                break;
        }

        $content_json = !empty($content) ? json_encode($content) : null;
        $stmt = $mysqli->prepare("UPDATE bookme_sections SET title=?, subtitle=?, content=? WHERE section_key=?");
        $stmt->bind_param("ssss", $title, $subtitle, $content_json, $key);
        $stmt->execute();
    }

    header('Location: bookme_sections.php?msg=updated');
    exit;
}

// Fetch sections
$sections = [];
$result = $mysqli->query("SELECT * FROM bookme_sections");
while ($row = $result->fetch_assoc()) {
    $sections[$row['section_key']] = $row;
    if ($row['content']) $sections[$row['section_key']]['content'] = json_decode($row['content'], true);
}

// Helper for escaping output
function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Book Me Sections</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
/* Root variables */
:root {
    --panel-bg: #f9f9f9;
    --text-color: #0f172a;
    --input-bg: #fff;
    --input-border: #ddd;
    --btn-bg: #0B1C3B;
    --btn-hover: #1a3b6d;
    --success-bg: #d4edda;
    --success-text: #155724;
}

/* Dark mode overrides */
body.dark-mode {
    --panel-bg: #1e293b;
    --text-color: #f1f5f9;
    --input-bg: #334155;
    --input-border: #475569;
    --btn-bg: #2563eb;
    --btn-hover: #1d4ed8;
    --success-bg: #064e3b;
    --success-text: #a7f3d0;

    background: var(--panel-bg);
    color: var(--text-color);
}

/* Apply variables */
body {
    background: var(--panel-bg);
    color: var(--text-color);
    font-family: Arial, sans-serif;
    transition: background 0.3s, color 0.3s;
}

.section {
    background: var(--panel-bg);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    transition: background 0.3s;
}

.form-input, .form-textarea {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid var(--input-border);
    border-radius: 8px;
    background: var(--input-bg);
    color: var(--text-color);
    transition: background 0.3s, color 0.3s, border-color 0.3s;
}

textarea.form-textarea { min-height: 120px; }

button.save-btn {
    background: var(--btn-bg);
    color: #fff;
    padding: 0.5rem 1.25rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
}
button.save-btn:hover { background: var(--btn-hover); }

.success {
    background: var(--success-bg);
    color: var(--success-text);
    padding: 0.75rem 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.nav a {
    margin-right: 1rem;
    text-decoration: none;
    color: #2563eb;
}
.nav a:hover { color: #1d4ed8; }

.nav-buttons a {
    display: inline-block;
    margin-right: 0.5rem;
    padding: 0.5rem 1rem;
    background-color: #e2e8f0;
    color: #0B1C3B;
    font-weight: 500;
    border-radius: 8px;
    transition: background-color 0.3s, color 0.3s;
}
.nav-buttons a:hover {
    background-color: #cbd5e1;
}

body.dark-mode .nav-buttons a { background-color: #334155; color: #f1f5f9; }
body.dark-mode .nav-buttons a:hover { background-color: #475569; }
</style>
</head>
<body class="<?= (isset($_SESSION['dark_mode']) && $_SESSION['dark_mode']) ? 'dark-mode' : '' ?>">
<?php include __DIR__ . '/includes/header.php'; ?>

<div class="max-w-5xl mx-auto p-4">
    <div class="nav-buttons mb-6">
        <a href="dashboard.php">← Back to Dashboard</a>
        <a href="bookme_topics.php">Manage Speaking Topics</a>
        <a href="bookme_requests.php">View Booking Requests</a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
        <div class="success">Sections updated successfully!</div>
    <?php endif; ?>

    <h1 class="text-3xl font-bold mb-6">Manage Book Me Sections</h1>

    <form method="POST" class="space-y-6">

        <!-- Hero Section -->
        <div class="section">
            <h2 class="text-2xl font-semibold mb-4">Hero Section</h2>
            <label>Title</label>
            <input type="text" name="hero_title" value="<?= e($sections['hero']['title'] ?? '') ?>" class="form-input mb-2">

            <label>Subtitle</label>
            <input type="text" name="hero_subtitle" value="<?= e($sections['hero']['subtitle'] ?? '') ?>" class="form-input mb-2">

            <label>Description</label>
            <textarea name="hero_description" class="form-textarea mb-2"><?= e($sections['hero']['content']['description'] ?? '') ?></textarea>

            <label>Bullet Points (one per line)</label>
            <textarea name="hero_bullet_points" class="form-textarea"><?= e(implode("\n", $sections['hero']['content']['bullet_points'] ?? [])) ?></textarea>
        </div>

        <!-- Topics Section -->
        <div class="section">
            <h2 class="text-2xl font-semibold mb-4">Topics Section</h2>
            <label>Title</label>
            <input type="text" name="topics_title" value="<?= e($sections['topics']['title'] ?? '') ?>" class="form-input mb-2">

            <label>Subtitle</label>
            <textarea name="topics_subtitle" class="form-textarea"><?= e($sections['topics']['subtitle'] ?? '') ?></textarea>
        </div>

        <!-- Contact Section -->
        <div class="section">
            <h2 class="text-2xl font-semibold mb-4">Contact Form Section</h2>
            <label>Title</label>
            <input type="text" name="contact_title" value="<?= e($sections['contact']['title'] ?? '') ?>" class="form-input mb-2">

            <label>Subtitle</label>
            <textarea name="contact_subtitle" class="form-textarea"><?= e($sections['contact']['subtitle'] ?? '') ?></textarea>
        </div>

        <!-- Direct Inquiries Section -->
        <div class="section">
            <h2 class="text-2xl font-semibold mb-4">Direct Inquiries Section</h2>
            <label>Title</label>
            <input type="text" name="direct_inquiries_title" value="<?= e($sections['direct_inquiries']['title'] ?? '') ?>" class="form-input mb-2">

            <label>Description</label>
            <textarea name="direct_inquiries_description" class="form-textarea mb-2"><?= e($sections['direct_inquiries']['content']['description'] ?? '') ?></textarea>

            <label>Contact Email</label>
            <input type="email" name="direct_inquiries_email" value="<?= e($sections['direct_inquiries']['content']['email'] ?? '') ?>" class="form-input">
        </div>

        <button type="submit" class="save-btn">Save All Changes</button>
    </form>
</div>
</body>
</html>
