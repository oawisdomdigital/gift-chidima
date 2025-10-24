<?php
// Start session before any output
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include('../db.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $heading = $_POST['heading'] ?? '';
  $subheading = $_POST['subheading'] ?? '';

  // Update section heading/subheading (id = 1)
  $stmt = $conn->prepare("UPDATE key_highlights SET section_heading=?, section_subheading=? WHERE id=1");
  $stmt->bind_param("ss", $heading, $subheading);
  $stmt->execute();
  $stmt->close();

  // Remove old highlight records (id > 1)
  $conn->query("DELETE FROM key_highlights WHERE id > 1");

  // Reinsert new highlights
  $titles = $_POST['title'] ?? [];
  $descriptions = $_POST['description'] ?? [];
  $existing_icons = $_POST['existing_icon'] ?? [];

  $count = count($titles);
  for ($i = 0; $i < $count; $i++) {
    $title = $titles[$i] ?? '';
    $description = $descriptions[$i] ?? '';

    // Handle icon upload
    $iconPath = null;
    if (isset($_FILES['icon']['name'][$i]) && $_FILES['icon']['name'][$i] != '') {
      $uploadDir = '../uploads/highlights/';
      if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
      $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($_FILES['icon']['name'][$i]));
      $targetPath = $uploadDir . $fileName;
      if (move_uploaded_file($_FILES['icon']['tmp_name'][$i], $targetPath)) {
        $iconPath = 'uploads/highlights/' . $fileName;
      }
    } else {
      $iconPath = $existing_icons[$i] ?? null;
    }

    $stmt = $conn->prepare("INSERT INTO key_highlights (icon, title, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $iconPath, $title, $description);
    $stmt->execute();
    $stmt->close();
  }

  echo "<script>alert('Key highlights updated successfully!'); window.location.href='key_highlights_form.php';</script>";
  exit;
}

// Fetch data for form display
$section = $conn->query("SELECT section_heading, section_subheading FROM key_highlights WHERE id = 1")->fetch_assoc();
$result = $conn->query("SELECT icon, title, description FROM key_highlights WHERE id > 1");
$highlights = $result->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit Key Highlights</title>
  <link rel="stylesheet" href="../frontend/dist/index.css">
  <style>
    :root{
      --navy: #071731;
      --gold: #D4AF37;
      --gold-dark: #B8941F;
      --muted: #6b7280;
      --panel-bg: rgba(255,255,255,0.96);
      --card-border: rgba(8,15,35,0.04);
    }

    /* base page */
    body {
      font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      margin: 0;
      background: linear-gradient(135deg,#f8fafc 0%,#eef1f5 100%);
      color: #0f172a;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }

    /* container: compact and centered like hero_form */
    .page-container {
      max-width: 980px;
      margin: 18px auto;
      padding: 12px;
    }

    /* card */
    .form-card {
      background: var(--panel-bg);
      border: 1px solid var(--card-border);
      border-radius: 12px;
      padding: 18px;
      box-shadow: 0 8px 24px rgba(8,15,35,0.06);
    }

    .form-header {
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:12px;
      margin-bottom:12px;
    }
    .form-header h2 {
      margin:0;
      font-size:1.25rem;
      color:var(--navy);
      font-weight:700;
    }
    .form-header small { color:var(--muted); }

    /* inputs compact */
    label {
      display:block;
      margin:10px 0 6px 0;
      font-weight:600;
      color:var(--navy);
      font-size:0.95rem;
    }

    input[type="text"], textarea, input[type="file"]{
      width:100%;
      padding:10px 12px;
      border-radius:10px;
      border:1px solid #e6eef8;
      background:white;
      font-size:0.95rem;
      color:var(--navy);
      box-sizing:border-box;
      margin-bottom:6px;
    }

    textarea { min-height:90px; resize:vertical; }

    input:focus, textarea:focus {
      outline:none;
      box-shadow:0 0 0 4px rgba(212,175,55,0.08);
      border-color:var(--gold);
    }

    /* highlight block */
    .highlight {
      display:block;
      background: rgba(15,23,42,0.02);
      padding:12px;
      border-radius:10px;
      border:1px solid rgba(8,15,35,0.03);
      margin-top:12px;
      position:relative;
    }

    .remove-btn {
      position:absolute;
      right:10px;
      top:10px;
      border:none;
      background:#ef4444;
      color:white;
      padding:6px 8px;
      border-radius:8px;
      cursor:pointer;
      font-weight:600;
    }

    .preview {
      width:64px;
      height:64px;
      object-fit:cover;
      border-radius:8px;
      border:1px solid #e6eef8;
      display:block;
      margin-top:6px;
    }

    /* action buttons */
    .actions { display:flex; gap:10px; align-items:center; justify-content:center; margin-top:14px; }
    .btn {
      padding:10px 16px;
      border-radius:10px;
      border:none;
      font-weight:700;
      cursor:pointer;
      font-size:0.95rem;
    }
    .btn-primary { background:var(--gold); color:white; box-shadow:0 10px 30px rgba(181,138,44,0.12); }
    .btn-primary:hover { background:var(--gold-dark); transform:translateY(-1px); }
    .btn-add { background:#10b981; color:white; }
    .btn-add:hover { background:#059669; }

    /* tidy small screens */
    @media (max-width:900px){
      .page-container { padding:10px; margin:10px; }
      .form-card { padding:12px; }
    }

    /* dark mode (syncs with header's localStorage key 'dg_theme_v1') */
    body.dark-mode {
      background: #071426;
      color: #E6EEF8;
    }
    body.dark-mode .form-card {
      background: rgba(10,20,30,0.86);
      border-color: rgba(255,255,255,0.04);
      box-shadow: none;
    }
    body.dark-mode input[type="text"], body.dark-mode textarea {
      background: rgba(255,255,255,0.04);
      border-color: rgba(255,255,255,0.06);
      color: #E6EEF8;
    }
    body.dark-mode .highlight {
      background: rgba(255,255,255,0.02);
      border-color: rgba(255,255,255,0.03);
    }
    body.dark-mode .preview { border-color: rgba(255,255,255,0.06); }
  </style>
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <div class="page-container">
    <div class="form-card">
      <div class="form-header">
        <h2>Key Highlights Editor</h2>
        <small>Edit the highlights shown on the homepage</small>
      </div>

      <form method="POST" enctype="multipart/form-data" id="highlightsForm">
        <label for="heading">Section Heading</label>
        <input id="heading" type="text" name="heading" value="<?= htmlspecialchars($section['section_heading'] ?? '') ?>">

        <label for="subheading">Section Subheading</label>
        <textarea id="subheading" name="subheading"><?= htmlspecialchars($section['section_subheading'] ?? '') ?></textarea>

        <div id="highlights-container">
          <?php foreach ($highlights as $i => $h): ?>
            <div class="highlight">
              <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>

              <label>Icon (upload)</label>
              <?php if (!empty($h['icon'])): ?>
                <img src="../<?= htmlspecialchars($h['icon']) ?>" class="preview" alt="icon preview">
              <?php endif; ?>
              <input type="file" name="icon[]">
              <input type="hidden" name="existing_icon[]" value="<?= htmlspecialchars($h['icon']) ?>">

              <label>Title</label>
              <input type="text" name="title[]" value="<?= htmlspecialchars($h['title']) ?>">

              <label>Description</label>
              <textarea name="description[]"><?= htmlspecialchars($h['description']) ?></textarea>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="actions">
          <button type="button" id="addHighlight" class="btn btn-add">+ Add Highlight</button>
          <button type="submit" class="btn btn-primary">💾 Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Dark mode sync with header toggle (localStorage key used in header: dg_theme_v1)
    (function(){
      const isDark = localStorage.getItem('dg_theme_v1') === 'dark';
      if (isDark) document.body.classList.add('dark-mode');
    })();

    // dynamic add highlight (compact HTML to match existing blocks)
    (function(){
      const container = document.getElementById('highlights-container');
      document.getElementById('addHighlight').addEventListener('click', () => {
        const div = document.createElement('div');
        div.className = 'highlight';
        div.innerHTML = `
          <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>

          <label>Icon (upload)</label>
          <input type="file" name="icon[]">
          <input type="hidden" name="existing_icon[]" value="">

          <label>Title</label>
          <input type="text" name="title[]" value="">

          <label>Description</label>
          <textarea name="description[]"></textarea>
        `;
        container.appendChild(div);
        // Scroll newly added element into view
        div.scrollIntoView({ behavior: 'smooth', block: 'center' });
      });
    })();
  </script>
</body>
</html>
