<?php
// Start session before any output
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// admin/blog_post_form.php
include('../db.php'); // expects $conn as mysqli connection

// Utility functions
function e($v)
{
  return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
function slugify($text)
{
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
  $text = preg_replace('~[^-\w]+~', '', $text);
  $text = trim($text, '-');
  $text = preg_replace('~-+~', '-', $text);
  $text = strtolower($text);
  return $text ?: 'post-' . uniqid();
}

// Upload directory
$uploadDir = __DIR__ . '/../uploads/blog/';
$uploadWebPrefix = 'uploads/blog/'; // stored in DB as this relative path
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Messages
$message = '';
$error = '';

// Handle POST actions: save (create/update) or delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $action = $_POST['action'] ?? 'save';

    if ($action === 'delete' && !empty($_POST['post_id'])) {
      $postId = (int)$_POST['post_id'];
      // fetch featured_image to unlink
      $stmt = $conn->prepare("SELECT featured_image FROM blog_posts WHERE id = ?");
      $stmt->bind_param("i", $postId);
      $stmt->execute();
      $res = $stmt->get_result()->fetch_assoc();
      $stmt->close();

      if ($res && !empty($res['featured_image'])) {
        $path = __DIR__ . '/../' . ltrim($res['featured_image'], '/');
        if (file_exists($path)) @unlink($path);
      }

      $stmt = $conn->prepare("DELETE FROM blog_posts WHERE id = ?");
      $stmt->bind_param("i", $postId);
      $stmt->execute();
      $stmt->close();

      header("Location: blog_post_form.php?deleted=1");
      exit;
    }

    // === Save (create or update) ===
    // Collect & sanitize
    $postId = !empty($_POST['post_id']) ? (int)$_POST['post_id'] : null;
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = $_POST['content'] ?? ''; // allow HTML
    $category = trim($_POST['category'] ?? '');
    $tagsInput = trim($_POST['tags'] ?? ''); // comma separated
    $author = trim($_POST['author'] ?? '');
    $publish_date = trim($_POST['publish_date'] ?? '');
    $read_time = trim($_POST['read_time'] ?? '');
    $featuredFlag = isset($_POST['featured']) ? 1 : 0;

    if ($title === '') throw new Exception('Title is required.');

    if ($slug === '') $slug = slugify($title);
    else $slug = slugify($slug);

    // Build tags JSON
    $tags = [];
    if ($tagsInput !== '') {
      // split by comma or newline
      $parts = preg_split('/[\r\n,]+/', $tagsInput);
      foreach ($parts as $p) {
        $t = trim($p);
        if ($t !== '') $tags[] = $t;
      }
    }
    $tagsJson = json_encode(array_values(array_unique($tags)), JSON_UNESCAPED_UNICODE);

    // Featured image handling
    $featuredImagePath = null;
    $removeExistingImage = isset($_POST['remove_featured_image']) ? true : false;
    $uploaded = $_FILES['featured_image'] ?? null;

    // When updating: fetch existing image path
    $existingImage = '';
    if ($postId) {
      $stmt = $conn->prepare("SELECT featured_image FROM blog_posts WHERE id = ?");
      $stmt->bind_param("i", $postId);
      $stmt->execute();
      $tmp = $stmt->get_result()->fetch_assoc();
      $stmt->close();
      $existingImage = $tmp['featured_image'] ?? '';
    }

    // If file uploaded, validate and move
    if ($uploaded && !empty($uploaded['name'])) {
      // Basic validations
      $allowed = ['jpg', 'jpeg', 'png', 'webp'];
      $extension = strtolower(pathinfo($uploaded['name'], PATHINFO_EXTENSION));
      if (!in_array($extension, $allowed)) throw new Exception('Invalid image type. Allowed: jpg,jpeg,png,webp.');
      if (@getimagesize($uploaded['tmp_name']) === false) throw new Exception('Uploaded file is not a valid image.');

      $safeBase = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($uploaded['name'], PATHINFO_FILENAME));
      $unique = uniqid() . '_' . $safeBase . '.' . $extension;
      $target = $uploadDir . $unique;
      if (!move_uploaded_file($uploaded['tmp_name'], $target)) throw new Exception('Failed to move uploaded image.');

      $featuredImagePath = $uploadWebPrefix . $unique;

      // Delete old image if existed
      if (!empty($existingImage)) {
        $oldFull = __DIR__ . '/../' . ltrim($existingImage, '/');
        if (file_exists($oldFull)) @unlink($oldFull);
      }
    } else {
      // no new upload
      if ($removeExistingImage && !empty($existingImage)) {
        // remove file
        $oldFull = __DIR__ . '/../' . ltrim($existingImage, '/');
        if (file_exists($oldFull)) @unlink($oldFull);
        $featuredImagePath = ''; // clear it
      } else {
        // keep existing if editing
        if ($postId) $featuredImagePath = $existingImage;
      }
    }

    // Ensure slug is unique (except for current post)
    $checkStmt = $conn->prepare("SELECT id FROM blog_posts WHERE slug = ? " . ($postId ? "AND id != ?" : "") . " LIMIT 1");
    if ($postId) $checkStmt->bind_param("si", $slug, $postId);
    else $checkStmt->bind_param("s", $slug);
    $checkStmt->execute();
    $existing = $checkStmt->get_result()->fetch_assoc();
    $checkStmt->close();
    if ($existing) {
      // append unique suffix
      $slug = $slug . '-' . uniqid();
    }

    if ($postId) {
      // UPDATE
      $stmt = $conn->prepare("UPDATE blog_posts
                SET slug=?, title=?, excerpt=?, content=?, featured_image=?, category=?, tags=?, author=?, publish_date=?, read_time=?, featured=?
                WHERE id=?");
      $stmt->bind_param(
        "ssssssssssii",
        $slug,
        $title,
        $excerpt,
        $content,
        $featuredImagePath,
        $category,
        $tagsJson,
        $author,
        $publish_date,
        $read_time,
        $featuredFlag,
        $postId
      );
      $stmt->execute();
      $stmt->close();
      $message = "Post updated successfully.";
    } else {
      // INSERT
      $stmt = $conn->prepare("INSERT INTO blog_posts
                (slug, title, excerpt, content, featured_image, category, tags, author, publish_date, read_time, featured)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param(
        "ssssssssssi",
        $slug,
        $title,
        $excerpt,
        $content,
        $featuredImagePath,
        $category,
        $tagsJson,
        $author,
        $publish_date,
        $read_time,
        $featuredFlag
      );
      $stmt->execute();
      $newId = $stmt->insert_id;
      $stmt->close();
      $message = "Post created successfully.";
      // redirect to edit page for convenience
      header("Location: blog_post_form.php?id=" . (int)$newId . "&created=1");
      exit;
    }
  } catch (Exception $ex) {
    $error = $ex->getMessage();
  }
}

// === GET / display ===
// fetch list of posts for quick navigation
$postsList = $conn->query("SELECT id, title, slug, publish_date FROM blog_posts ORDER BY publish_date DESC, id DESC")->fetch_all(MYSQLI_ASSOC);

// editing post if id provided
$editingId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['post_id']) ? (int)$_POST['post_id'] : null);
$post = null;
if ($editingId) {
  $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE id = ?");
  $stmt->bind_param("i", $editingId);
  $stmt->execute();
  $res = $stmt->get_result();
  $post = $res->fetch_assoc();
  $stmt->close();

  // decode tags back to comma separated for display
  $tagsDisplay = '';
  if ($post && !empty($post['tags'])) {
    $decoded = json_decode($post['tags'], true);
    if (is_array($decoded)) $tagsDisplay = implode(", ", $decoded);
  }
}
?>
<?php
$page_title = 'Blog Post Admin';
include 'includes/head.php';
?>
<style>
  /* Body & dark mode */
  body {
    font-family: Inter, system-ui, -apple-system, sans-serif;
    background: #f0f2f5;
    color: #1e293b;
    min-height: 100vh;
    margin: 0;
    padding: 0;
  }
  .dark-mode {
    background: #0f172a;
    color: #e2e8f0;
  }

  /* Container */
  .container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
    margin-top: 0;
    margin-bottom: 0;
    padding-top: 10px;
    padding-bottom: 10px;
  }

  /* Cards */
  .card {
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid #cbd5e1;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
  }
  .dark-mode .card {
    background: rgba(24,28,37,0.95);
    border-color: rgba(255,255,255,0.1);
  }

  /* Form grid */
  .form {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 1rem;
  }

  /* Inputs, textarea, select */
  input, textarea, select {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid #94a3b8; /* nice visible border */
    border-radius: 0.5rem;
    font-size: 0.875rem;
    transition: all 0.2s;
    background: #fff;
    color: #1e293b;
    margin-top: 0.25rem; /* minimal top margin */
    box-sizing: border-box;
  }
  .dark-mode input,
  .dark-mode textarea,
  .dark-mode select {
    background: #1e293b;
    color: #e2e8f0;
    border-color: #475569;
  }

  input:focus, textarea:focus, select:focus {
    outline: none;
    border-color: #ca9f5c; /* gold accent */
    box-shadow: 0 0 0 2px rgba(202,159,92,0.2);
  }

  /* Labels */
  label {
    display: block;
    font-weight: 600;
    font-size: 0.875rem;
    color: #1e293b;
    margin-bottom: 0.25rem;
  }
  .dark-mode label {
    color: #e2e8f0;
  }

  /* Messages */
  .message {
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
    font-size: 0.875rem;
  }
  .success { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }
  .error { background: #fee2e2; color: #991b1b; border: 1px solid #dc2626; }
  .dark-mode .success { background: #064e3b; color: #a7f3d0; border-color: #10b981; }
  .dark-mode .error { background: #7f1d1d; color: #fecaca; border-color: #dc2626; }

  /* Buttons */
  .btn {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.15s;
    border: none;
  }
  .btn-primary { background: #0a2558; color: #fff; }
  .btn-primary:hover { background: #1d3b6c; }
  .btn-danger { background: #dc2626; color: #fff; }
  .btn-danger:hover { background: #b91c1c; }
  .btn-ghost {
    background: transparent;
    border: 1px solid #94a3b8;
    color: #1e293b;
  }
  .dark-mode .btn-ghost { border-color: #475569; color: #e2e8f0; }
  .btn-ghost:hover { border-color: #ca9f5c; background: rgba(202,159,92,0.1); }

  /* Images */
  .thumb {
    width: 100%;
    max-height: 200px;
    object-fit: cover;
    border-radius: 0.5rem;
    border: 1px solid #94a3b8;
    margin-top: 0.5rem;
  }
  .dark-mode .thumb { border-color: #475569; }

  /* Minor spacing adjustments */
  .meta, .note {
    font-size: 0.75rem;
    color: #64748b;
    margin-top: 0.25rem;
  }
  .dark-mode .meta, .dark-mode .note { color: #94a3b8; }

  /* Form row adjustments */
  .form-row { display: flex; gap: 0.5rem; }
  .form-row > div { flex: 1; }
</style>

</head>

<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <br>
  <div class="container">
    

    <?php if ($message): ?>
      <div class="message success"><?= e($message) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="message error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="form">
      <div class="card">
        <input type="hidden" name="post_id" value="<?= e($editingId) ?>">

        <label>Title *</label>
        <input type="text" name="title" required value="<?= e($post['title'] ?? '') ?>">

        <label>Slug (optional — auto-generated)</label>
        <input type="text" name="slug" value="<?= e($post['slug'] ?? '') ?>">

        <label>Excerpt</label>
        <textarea name="excerpt" rows="3"><?= e($post['excerpt'] ?? '') ?></textarea>

        <label>Content (HTML allowed)</label>
        <textarea name="content" rows="12"><?= $post['content'] ?? '' ?></textarea>

        <label>Category</label>
        <input type="text" name="category" value="<?= e($post['category'] ?? '') ?>">

        <label>Tags (comma-separated)</label>
        <input type="text" name="tags" value="<?= e($tagsDisplay ?? '') ?>">

        <div class="form-row" style="display:flex; gap:12px; margin-top:12px;">
          <div style="flex:1">
            <label>Author</label>
            <input type="text" name="author" value="<?= e($post['author'] ?? '') ?>">
          </div>
          <div style="width:160px">
            <label>Publish Date</label>
            <input type="date" name="publish_date" value="<?= e($post['publish_date'] ?? '') ?>">
          </div>
        </div>

        <div style="display:flex; gap:12px;">
          <div style="flex:1">
            <label>Read Time (e.g. "6 min read")</label>
            <input type="text" name="read_time" value="<?= e($post['read_time'] ?? '') ?>">
          </div>
          <div style="width:120px">
            <label style="display:block; margin-top:22px;">
              <input type="checkbox" name="featured" value="1" <?= (!empty($post['featured']) ? 'checked' : '') ?>> Featured
            </label>
          </div>
        </div>

      </div>

      <div class="card" style="min-width:320px;">
        <label>Featured Image</label>

        <?php if (!empty($post['featured_image'])): ?>
          <img src="../<?= ltrim(e($post['featured_image']), '/') ?>" class="thumb" alt="Featured image">
          <div class="meta">Current file: <code><?= e($post['featured_image']) ?></code></div>
          <label style="margin-top:8px;"><input type="checkbox" name="remove_featured_image" value="1"> Remove existing image</label>
        <?php else: ?>
          <div class="small">No featured image uploaded yet.</div>
        <?php endif; ?>

        <label style="margin-top:8px;">Upload / Replace image</label>
        <input type="file" name="featured_image">

        <div class="note">Allowed types: jpg, jpeg, png, webp. Max important: browser limits; server side size depends on PHP settings.</div>

        <div class="controls">
          <button type="submit" class="btn btn-primary">Save Post</button>

          <?php if ($editingId): ?>
            <button type="submit" name="action" value="delete" class="btn btn-danger" onclick="return confirm('Delete this post and its image? This cannot be undone.')">Delete Post</button>
          <?php endif; ?>

          <a class="btn btn-ghost" href="blog_post_form.php">Create New</a>
        </div>

        <div style="margin-top:12px;">
          <div class="small">Tip: After saving, use the nav items above to quickly jump between posts.</div>
        </div>
      </div>

    </form>
  </div>
</body>

</html>