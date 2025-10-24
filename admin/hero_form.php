<?php
// Start session before any output
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include('../db.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $welcome = $_POST['welcome_text'];
  $title1 = $_POST['title_line1'];
  $title2 = $_POST['title_line2'];
  $subtitle = $_POST['subtitle'];
  $desc = $_POST['description'];
  $button_text = $_POST['button_text'];
  $button_link = $_POST['button_link'];

  // Handle image upload if provided
  $file_name = null;
  if (!empty($_FILES["image"]["name"])) {
    $target_dir = "../uploads/";
    $file_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $file_name;
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
  }

  // Fetch existing record (if any)
  $check = $conn->query("SELECT * FROM hero_section LIMIT 1");

  if ($check->num_rows > 0) {
    // Update existing row
    if ($file_name) {
      $stmt = $conn->prepare("UPDATE hero_section SET welcome_text=?, title_line1=?, title_line2=?, subtitle=?, description=?, button_text=?, button_link=?, image_path=?");
      $stmt->bind_param("ssssssss", $welcome, $title1, $title2, $subtitle, $desc, $button_text, $button_link, $file_name);
    } else {
      $stmt = $conn->prepare("UPDATE hero_section SET welcome_text=?, title_line1=?, title_line2=?, subtitle=?, description=?, button_text=?, button_link=?");
      $stmt->bind_param("sssssss", $welcome, $title1, $title2, $subtitle, $desc, $button_text, $button_link);
    }
  } else {
    // Insert new record
    $stmt = $conn->prepare("INSERT INTO hero_section (welcome_text, title_line1, title_line2, subtitle, description, button_text, button_link, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $welcome, $title1, $title2, $subtitle, $desc, $button_text, $button_link, $file_name);
  }

  $stmt->execute();
  $success = true;
}

// Fetch latest hero section for form prefill
$result = $conn->query("SELECT * FROM hero_section LIMIT 1");
$hero = $result->fetch_assoc();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Hero Section</title>
  <link rel="stylesheet" href="../frontend/dist/index.css">
  <style>
    :root {
      --gold: #d4af37;
      --text-dark: #071731;
      --bg-light: #f5f7fb;
      --bg-card: #ffffff;
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
      align-items: center;
      justify-content: space-between;
    }

    label {
      display: block;
      font-weight: 500;
      color: var(--text-dark);
      margin-bottom: 0.3rem;
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

    button[type="submit"] {
      background: var(--gold);
      color: #fff;
      font-weight: 600;
      padding: 0.8rem 1.5rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s, transform 0.1s;
    }

    button:hover {
      background: #c19b2e;
      transform: translateY(-1px);
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

    .image-preview {
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .image-preview img {
      border-radius: 10px;
      max-height: 120px;
      border: 1px solid var(--border-color);
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
    <div class="form-card" data-fade-in>
      <div class="form-header">
        <span>Update Hero Section</span>
        <small class="text-sm text-gray-400">Edit homepage hero content ✨</small>
      </div>

      <?php if (!empty($success)): ?>
        <div class="success">✅ Hero section updated successfully!</div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data">
        <label>Welcome Text</label>
        <input type="text" name="welcome_text" value="<?= htmlspecialchars($hero['welcome_text'] ?? '') ?>" required>

        <label>Title Line 1</label>
        <input type="text" name="title_line1" value="<?= htmlspecialchars($hero['title_line1'] ?? '') ?>" required>

        <label>Title Line 2</label>
        <input type="text" name="title_line2" value="<?= htmlspecialchars($hero['title_line2'] ?? '') ?>" required>

        <label>Subtitle</label>
        <input type="text" name="subtitle" value="<?= htmlspecialchars($hero['subtitle'] ?? '') ?>" required>

        <label>Description</label>
        <textarea name="description" required><?= htmlspecialchars($hero['description'] ?? '') ?></textarea>

        <label>Button Text</label>
        <input type="text" name="button_text" value="<?= htmlspecialchars($hero['button_text'] ?? '') ?>" required>

        <label>Button Link</label>
        <input type="text" name="button_link" value="<?= htmlspecialchars($hero['button_link'] ?? '') ?>" required>

        <?php if (!empty($hero['image_path'])): ?>
          <div class="image-preview">
            <img src="../uploads/<?= htmlspecialchars($hero['image_path']) ?>" alt="Hero Image">
            <span>Current image</span>
          </div>
        <?php endif; ?>

        <label>Upload New Image (optional)</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit">💾 Save Changes</button>
      </form>
    </div>
  </div>

  <script>
    const isDark = localStorage.getItem('dg_theme_v1') === 'dark';
    if (isDark) document.body.classList.add('dark-mode');
  </script>
</body>
</html>
