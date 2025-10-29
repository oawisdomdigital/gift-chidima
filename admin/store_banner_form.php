<?php 
// Start session before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// admin/store_banner_form.php
include('../db.php');

// Helper to escape output
function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

// Upload directory
$uploadDir = "../uploads/store_books/";
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// === POST HANDLING ===
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'save';
    $bannerId = !empty($_POST['banner_id']) ? (int)$_POST['banner_id'] : null;

    try {
        // === Delete Banner ===
        if ($action === 'delete' && $bannerId) {
            // Delete associated books files
            $res = $conn->prepare("SELECT cover_image FROM store_books WHERE banner_id=?");
            $res->bind_param("i", $bannerId);
            $res->execute();
            $rows = $res->get_result()->fetch_all(MYSQLI_ASSOC);
            $res->close();
            foreach ($rows as $r) {
                if (!empty($r['cover_image'])) {
                    $filePath = __DIR__ . '/../' . ltrim($r['cover_image'], '/');
                    if (file_exists($filePath)) @unlink($filePath);
                }
            }

            // Delete books and banner
            $stmt = $conn->prepare("DELETE FROM store_books WHERE banner_id=?");
            $stmt->bind_param("i", $bannerId);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("DELETE FROM store_banner WHERE id=?");
            $stmt->bind_param("i", $bannerId);
            $stmt->execute();
            $stmt->close();

            header("Location: store_banner_form.php?deleted=1");
            exit;
        }

        // === Save Banner ===
        $section_title = trim($_POST['section_title'] ?? '');
        $section_subtitle = trim($_POST['section_subtitle'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $button_text = trim($_POST['button_text'] ?? '');
        $button_link = trim($_POST['button_link'] ?? '');
        $bg_color_from = trim($_POST['bg_color_from'] ?? 'from-amber-50');
        $bg_color_to = trim($_POST['bg_color_to'] ?? 'to-orange-50');

        if ($bannerId) {
            $stmt = $conn->prepare("UPDATE store_banner SET section_title=?, section_subtitle=?, description=?, button_text=?, button_link=?, bg_color_from=?, bg_color_to=? WHERE id=?");
            $stmt->bind_param("sssssssi", $section_title, $section_subtitle, $description, $button_text, $button_link, $bg_color_from, $bg_color_to, $bannerId);
            $stmt->execute();
            $stmt->close();
            $message = "Banner updated successfully.";
        } else {
            $stmt = $conn->prepare("INSERT INTO store_banner (section_title, section_subtitle, description, button_text, button_link, bg_color_from, bg_color_to) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $section_title, $section_subtitle, $description, $button_text, $button_link, $bg_color_from, $bg_color_to);
            $stmt->execute();
            $bannerId = $stmt->insert_id;
            $stmt->close();
            $message = "Banner created successfully.";
        }

        // === Existing Books ===
        $existingIds = $_POST['book_id_existing'] ?? [];
        $existingTitles = $_POST['book_title_existing'] ?? [];
        $existingLabels = $_POST['book_label_existing'] ?? [];
        $existingPaths = $_POST['existing_cover_path'] ?? [];
        $removeList = $_POST['remove_book'] ?? [];
        $existingFiles = $_FILES['book_cover_existing'] ?? null;

        $updateStmt = $conn->prepare("UPDATE store_books SET title=?, cover_label=?, cover_image=? WHERE id=?");
        $deleteStmt = $conn->prepare("DELETE FROM store_books WHERE id=?");

        for ($i = 0; $i < count($existingIds); $i++) {
            $bid = (int)$existingIds[$i];
            $title = trim($existingTitles[$i] ?? '');
            $label = trim($existingLabels[$i] ?? '');
            $oldPath = trim($existingPaths[$i] ?? '');

            if (in_array((string)$bid, $removeList, true)) {
                if (!empty($oldPath)) {
                    $path = __DIR__ . '/../' . ltrim($oldPath, '/');
                    if (file_exists($path)) @unlink($path);
                }
                $deleteStmt->bind_param("i", $bid);
                $deleteStmt->execute();
                continue;
            }

            $newPath = $oldPath;

            if ($existingFiles && !empty($existingFiles['name'][$i])) {
                $filename = $existingFiles['name'][$i];
                $tmpname = $existingFiles['tmp_name'][$i];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','webp'];
                if (!in_array($ext,$allowed)) throw new Exception("Invalid image type: $filename");
                if (@getimagesize($tmpname) === false) throw new Exception("File is not an image: $filename");

                $safeName = uniqid() . '_' . preg_replace('/[^A-Za-z0-9_-]/','_',pathinfo($filename, PATHINFO_FILENAME)) . '.' . $ext;
                $target = $uploadDir . $safeName;
                if (!move_uploaded_file($tmpname, $target)) throw new Exception("Failed to move file: $filename");

                if (!empty($oldPath)) {
                    $oldFull = __DIR__ . '/../' . ltrim($oldPath, '/');
                    if (file_exists($oldFull)) @unlink($oldFull);
                }

                $newPath = "uploads/store_books/" . $safeName;
            }

            $updateStmt->bind_param("sssi", $title, $label, $newPath, $bid);
            $updateStmt->execute();
        }
        $updateStmt->close();
        $deleteStmt->close();

        // === New Books ===
        $newTitles = $_POST['book_title_new'] ?? [];
        $newLabels = $_POST['book_label_new'] ?? [];
        $newFiles = $_FILES['book_cover_new'] ?? null;

        $insertStmt = $conn->prepare("INSERT INTO store_books (banner_id, title, cover_label, cover_image) VALUES (?, ?, ?, ?)");
        for ($i=0; $i<count($newTitles); $i++) {
            $ntitle = trim($newTitles[$i] ?? '');
            $nlabel = trim($newLabels[$i] ?? '');
            if (!$ntitle) continue;

            $coverPath = null;
            if ($newFiles && !empty($newFiles['name'][$i])) {
                $filename = $newFiles['name'][$i];
                $tmpname = $newFiles['tmp_name'][$i];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','webp'];
                if (!in_array($ext,$allowed)) throw new Exception("Invalid image type: $filename");
                if (@getimagesize($tmpname) === false) throw new Exception("File is not an image: $filename");

                $safeName = uniqid() . '_' . preg_replace('/[^A-Za-z0-9_-]/','_',pathinfo($filename, PATHINFO_FILENAME)) . '.' . $ext;
                $target = $uploadDir . $safeName;
                if (!move_uploaded_file($tmpname, $target)) throw new Exception("Failed to move file: $filename");

                $coverPath = "uploads/store_books/" . $safeName;
            }

            $insertStmt->bind_param("isss", $bannerId, $ntitle, $nlabel, $coverPath);
            $insertStmt->execute();
        }
        $insertStmt->close();

    } catch (Exception $ex) {
        $message = "Error: " . $ex->getMessage();
    }
}

// Fetch all banners for selection
$bannerList = $conn->query("SELECT id, section_title FROM store_banner ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
$editingId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['banner_id']) ? (int)$_POST['banner_id'] : null);

$banner = [];
$books = [];
if ($editingId) {
    $stmt = $conn->prepare("SELECT * FROM store_banner WHERE id=?");
    $stmt->bind_param("i", $editingId);
    $stmt->execute();
    $banner = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($banner) {
        $stmt = $conn->prepare("SELECT * FROM store_books WHERE banner_id=? ORDER BY id ASC");
        $stmt->bind_param("i", $editingId);
        $stmt->execute();
        $books = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        $editingId = null;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php 
$page_title = 'Manage Store Banner';
include 'includes/head.php'; 
?>
<style>
:root {
  --navy: #071731;
  --gold: #D4AF37;
  --gold-dark: #B8941F;
  --panel-bg: rgba(255,255,255,0.96);
  --card-border: rgba(8,15,35,0.04);
  --text: #0f172a;
  --input-bg: #fff;
  --input-border: #e6eef8;
  --shadow: 0 4px 25px rgba(0,0,0,0.05);
  --btn-bg: #2563eb;
  --btn-text: #fff;
  --remove-bg: #ef4444;
}
body { background: var(--panel-bg); color: var(--text); }
body.dark-mode { 
  --panel-bg: rgba(10,20,30,0.86); 
  --text:#E6EEF8; 
  --input-bg: rgba(10,20,30,0.86); 
  --input-border: rgba(255,255,255,0.1); 
  --btn-bg:#3b82f6;
  --shadow:none;
}
.container {
  background: var(--panel-bg); border:1px solid var(--card-border); border-radius:12px; padding:2rem; max-width:1000px; margin:2rem auto;
}
.form-input, .form-textarea { background: var(--input-bg); border:1px solid var(--input-border); color: var(--text); border-radius:8px; padding:0.5rem 0.75rem; }
.existing-book, .new-book { background: var(--panel-bg); border:1px solid var(--card-border); border-radius:12px; padding:1rem; position:relative; }
.remove-btn { position:absolute; top:0.5rem; right:0.5rem; background:#ef4444;color:#fff;padding:0.25rem 0.5rem;border-radius:6px;font-weight:600; }
.save-btn, .delete-btn, .add-btn { background: var(--btn-bg); color: var(--btn-text); padding:0.5rem 1rem; border-radius:8px; font-weight:600; cursor:pointer; }
.save-btn:hover, .delete-btn:hover, .add-btn:hover, .remove-btn:hover { opacity:0.9; }
</style>
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
<div class="container">
  <div class="flex justify-between items-center mb-6 flex-wrap gap-2">
    <h2 class="text-2xl font-semibold"><?= $editingId ? 'Edit Store Banner' : 'Create Store Banner' ?></h2>
    <div class="flex flex-wrap gap-2">
      <div class="bg-gray-200 text-gray-700 px-3 py-1 rounded"><?= count($bannerList) ?> Banners</div>
      <a href="store_banner_form.php" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">➕ New</a>
      <?php foreach($bannerList as $b): ?>
        <a href="?id=<?= (int)$b['id'] ?>" class="px-3 py-1 bg-gray-300 hover:bg-gray-400 rounded"><?= e($b['section_title'] ?: 'Untitled') ?></a>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if($message): ?>
    <div class="p-3 mb-4 rounded <?= strpos($message,'Error')===0?'bg-red-100 text-red-700':'bg-green-100 text-green-700' ?>"><?= e($message) ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="space-y-6">
    <input type="hidden" name="banner_id" value="<?= e($editingId) ?>">

    <!-- Banner Info -->
    <div><label class="block mb-1 font-medium">Section Title</label><input type="text" name="section_title" value="<?= e($banner['section_title'] ?? '') ?>" class="form-input w-full"></div>
    <div><label class="block mb-1 font-medium">Section Subtitle</label><input type="text" name="section_subtitle" value="<?= e($banner['section_subtitle'] ?? '') ?>" class="form-input w-full"></div>
    <div><label class="block mb-1 font-medium">Description</label><textarea name="description" rows="3" class="form-textarea w-full"><?= e($banner['description'] ?? '') ?></textarea></div>
    <div class="flex gap-4 flex-wrap">
      <div><label>Button Text</label><input type="text" name="button_text" value="<?= e($banner['button_text'] ?? '') ?>" class="form-input"></div>
      <div><label>Button Link</label><input type="text" name="button_link" value="<?= e($banner['button_link'] ?? '') ?>" class="form-input"></div>
    </div>
    <div class="flex gap-4 flex-wrap">
      <div><label>Background Color From</label><input type="text" name="bg_color_from" value="<?= e($banner['bg_color_from'] ?? 'from-amber-50') ?>" class="form-input"></div>
      <div><label>Background Color To</label><input type="text" name="bg_color_to" value="<?= e($banner['bg_color_to'] ?? 'to-orange-50') ?>" class="form-input"></div>
    </div>

    <!-- Existing Books -->
    <h3 class="text-xl font-semibold mt-6">Existing Books</h3>
    <div id="existing-books" class="space-y-4">
      <?php foreach($books as $book): ?>
        <div class="existing-book">
          <button type="button" class="remove-btn" onclick="this.closest('.existing-book').querySelector('input[name=remove_book][]').checked=true; this.closest('.existing-book').style.display='none'">✕</button>
          <input type="hidden" name="book_id_existing[]" value="<?= (int)$book['id'] ?>">
          <input type="hidden" name="existing_cover_path[]" value="<?= e($book['cover_image']) ?>">
          <input type="hidden" name="remove_book[]" value="">
          <div class="flex flex-col md:flex-row gap-4">
            <div><label>Title</label><input type="text" name="book_title_existing[]" value="<?= e($book['title']) ?>" class="form-input w-full"></div>
            <div><label>Label</label><input type="text" name="book_label_existing[]" value="<?= e($book['cover_label']) ?>" class="form-input w-full"></div>
            <div><label>Cover</label><input type="file" name="book_cover_existing[]" class="form-input"></div>
            <?php if(!empty($book['cover_image'])): ?>
              <img src="../<?= e($book['cover_image']) ?>" alt="cover" class="h-24 object-cover rounded">
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- New Books -->
    <h3 class="text-xl font-semibold mt-6">Add New Books</h3>
    <div id="new-books" class="space-y-4"></div>
    <button type="button" id="add-new-book" class="add-btn">+ Add Another Book</button>

    <!-- Actions -->
    <div class="flex gap-4 flex-wrap mt-6">
      <button type="submit" name="action" value="save" class="save-btn"><?= $editingId ? 'Update' : 'Save' ?></button>
      <?php if($editingId): ?>
        <button type="submit" name="action" value="delete" class="delete-btn" onclick="return confirm('Are you sure you want to delete this banner?')">Delete Banner</button>
      <?php endif; ?>
    </div>
  </form>
</div>

<script>
// Add new book dynamically
let newBooksContainer = document.getElementById('new-books');
document.getElementById('add-new-book').addEventListener('click', () => {
  let div = document.createElement('div');
  div.className = 'new-book flex flex-col md:flex-row gap-4 relative';
  div.innerHTML = `
    <button type="button" class="remove-btn" onclick="this.closest('.new-book').remove()">✕</button>
    <div><label>Title</label><input type="text" name="book_title_new[]" class="form-input w-full"></div>
    <div><label>Label</label><input type="text" name="book_label_new[]" class="form-input w-full"></div>
    <div><label>Cover</label><input type="file" name="book_cover_new[]" class="form-input"></div>
  `;
  newBooksContainer.appendChild(div);
});
</script>
</body>
</html>
