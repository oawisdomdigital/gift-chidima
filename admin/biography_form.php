<?php
// Start session before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../db.php');

// --- Fetch existing content (if any) ---
$sql = "SELECT * FROM biography_section ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
$existing = $result->num_rows > 0 ? $result->fetch_assoc() : null;

// --- Handle form submission ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $title = $_POST['title'] ?? '';
  $paragraph1 = $_POST['paragraph1'] ?? '';
  $paragraph2 = $_POST['paragraph2'] ?? '';
  $paragraph3 = $_POST['paragraph3'] ?? '';
  $quote = $_POST['quote'] ?? '';
  $image_path = $existing['image_path'] ?? null;

  // --- Handle image upload if provided ---
  if (!empty($_FILES["image"]["name"])) {
    $target_dir = "../uploads/";
    if (!is_dir($target_dir)) {
      mkdir($target_dir, 0777, true);
    }

    $file_name = time() . "_" . basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
      $image_path = $file_name;
    }
  }

  // --- Insert or update ---
  if ($existing) {
    $stmt = $conn->prepare("UPDATE biography_section SET title=?, paragraph1=?, paragraph2=?, paragraph3=?, quote=?, image_path=? WHERE id=?");
    $stmt->bind_param("ssssssi", $title, $paragraph1, $paragraph2, $paragraph3, $quote, $image_path, $existing['id']);
  } else {
    $stmt = $conn->prepare("INSERT INTO biography_section (title, paragraph1, paragraph2, paragraph3, quote, image_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $title, $paragraph1, $paragraph2, $paragraph3, $quote, $image_path);
  }

  if ($stmt->execute()) {
    $success = true;
    $sql = "SELECT * FROM biography_section ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);
    $existing = $result->fetch_assoc();
  } else {
    $error = $stmt->error;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - Biography Section</title>
  <link rel="stylesheet" href="../frontend/dist/index.css">
  <style>
    :root {
      --gold: #d4af37;
      --bg-light: #f5f7fb;
      --bg-card: #ffffff;
      --text-dark: #071731;
      --border-color: #e5e7eb;
    }

    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background: var(--bg-light);
      margin: 0;
      padding: 0;
      transition: background 0.3s, color 0.3s;
    }

    .page-container {
      max-width: 950px;
      margin: 2rem auto;
      padding: 1.5rem;
    }

    .form-card {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 8px 20px rgba(0,0,0,0.04);
      transition: background 0.3s, border-color 0.3s;
    }

    .form-header {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--text-dark);
      border-bottom: 1px solid var(--border-color);
      padding-bottom: 0.8rem;
      margin-bottom: 1.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    label {
      display: block;
      font-weight: 500;
      color: var(--text-dark);
      margin-bottom: 0.4rem;
    }

    input[type="text"],
    textarea,
    input[type="file"] {
      width: 100%;
      border: 1px solid var(--border-color);
      background: #fff;
      border-radius: 10px;
      padding: 0.75rem;
      font-size: 0.95rem;
      color: var(--text-dark);
      margin-bottom: 1rem;
      transition: border 0.2s, box-shadow 0.2s;
    }

    input:focus, textarea:focus {
      border-color: var(--gold);
      outline: none;
      box-shadow: 0 0 0 2px rgba(212,175,55,0.2);
    }

    textarea {
      min-height: 120px;
      resize: vertical;
    }

    .success {
      background: #dcfce7;
      color: #166534;
      padding: 1rem;
      border-radius: 8px;
      font-weight: 500;
      margin-bottom: 1rem;
      border: 1px solid #86efac;
    }

    .error {
      background: #fee2e2;
      color: #991b1b;
      padding: 1rem;
      border-radius: 8px;
      font-weight: 500;
      margin-bottom: 1rem;
      border: 1px solid #fecaca;
    }

    .image-preview {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .image-preview img {
      border-radius: 10px;
      max-height: 120px;
      border: 1px solid var(--border-color);
    }

    button[type="submit"] {
      background: var(--gold);
      color: #fff;
      font-weight: 600;
      padding: 0.8rem 1.5rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s, transform 0.1s;
      display: block;
      margin: auto;
    }

    button:hover {
      background: #c19b2e;
      transform: translateY(-1px);
    }

    body.dark-mode {
      --bg-light: #0f172a;
      --bg-card: rgba(15,23,42,0.95);
      --text-dark: #f8fafc;
      --border-color: rgba(255,255,255,0.08);
    }

    body.dark-mode input[type="text"],
    body.dark-mode textarea,
    body.dark-mode input[type="file"] {
      background: rgba(255,255,255,0.08);
      color: #f1f5f9;
      border-color: rgba(255,255,255,0.1);
    }

    body.dark-mode button[type="submit"] {
      background: var(--gold);
      color: #071731;
    }

    body.dark-mode .form-card {
      box-shadow: none;
    }
  </style>
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <div class="page-container">
    <div class="form-card">
      <div class="form-header">
        <span>Biography Section</span>
        <small class="text-sm text-gray-400">Edit About / Biography content 📖</small>
      </div>

      <?php if (!empty($success)): ?>
        <div class="success">✅ Biography section saved successfully!</div>
      <?php elseif (!empty($error)): ?>
        <div class="error">❌ Error saving content: <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data">
        <label>Title</label>
        <input type="text" name="title" value="<?= htmlspecialchars($existing['title'] ?? '') ?>">

        <label>Paragraph 1</label>
        <textarea name="paragraph1" rows="4"><?= htmlspecialchars($existing['paragraph1'] ?? '') ?></textarea>

        <label>Paragraph 2</label>
        <textarea name="paragraph2" rows="4"><?= htmlspecialchars($existing['paragraph2'] ?? '') ?></textarea>

        <label>Paragraph 3</label>
        <textarea name="paragraph3" rows="4"><?= htmlspecialchars($existing['paragraph3'] ?? '') ?></textarea>

        <label>Quote</label>
        <textarea name="quote" rows="2"><?= htmlspecialchars($existing['quote'] ?? '') ?></textarea>

        <?php if (!empty($existing['image_path'])): ?>
          <div class="image-preview">
            <img src="../uploads/<?= htmlspecialchars($existing['image_path']) ?>" alt="Biography Image">
            <span>Current image</span>
          </div>
        <?php endif; ?>

        <label>Upload New Image (optional)</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit">💾 Save Biography</button>
      </form>
    </div>
  </div>

  <script>
    const isDark = localStorage.getItem('dg_theme_v1') === 'dark';
    if (isDark) document.body.classList.add('dark-mode');
  </script>
</body>
</html>
