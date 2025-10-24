<?php
// Start session before any output
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include('../db.php');

// Ensure section row exists
$check = $conn->query("SELECT id FROM ventures WHERE id = 1");
if ($check->num_rows === 0) {
  $conn->query("INSERT INTO ventures (id, section_heading, section_subheading) VALUES (1, '', '')");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $heading = $_POST['section_heading'] ?? '';
  $subheading = $_POST['section_subheading'] ?? '';

  // Update section heading/subheading
  $stmt = $conn->prepare("UPDATE ventures SET section_heading=?, section_subheading=? WHERE id=1");
  $stmt->bind_param("ss", $heading, $subheading);
  $stmt->execute();
  $stmt->close();

  // Clear existing venture cards
  $conn->query("DELETE FROM ventures WHERE id > 1");

  // Reinsert venture cards
  $count = isset($_POST['name']) ? count($_POST['name']) : 0;
  for ($i = 0; $i < $count; $i++) {
    $name = $_POST['name'][$i] ?? '';
    $description = $_POST['description'][$i] ?? '';
    $logoPath = null;

    // Handle logo upload
    if (!empty($_FILES['logo']['name'][$i])) {
      $uploadDir = '../uploads/ventures/';
      if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
      $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($_FILES['logo']['name'][$i]));
      $targetPath = $uploadDir . $fileName;
      if (move_uploaded_file($_FILES['logo']['tmp_name'][$i], $targetPath)) {
        $logoPath = 'uploads/ventures/' . $fileName;
      }
    } else {
      $logoPath = $_POST['existing_logo'][$i] ?? null;
    }

    $stmt = $conn->prepare("INSERT INTO ventures (logo, name, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $logoPath, $name, $description);
    $stmt->execute();
    $stmt->close();
  }

  echo "<script>alert('Ventures section updated successfully!'); window.location.href='ventures_form.php';</script>";
  exit;
}

// Fetch data
$section = $conn->query("SELECT section_heading, section_subheading FROM ventures WHERE id = 1")->fetch_assoc();
$result = $conn->query("SELECT logo, name, description FROM ventures WHERE id > 1");
$ventures = $result->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit Ventures Section</title>

  <!-- your global compiled css -->
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

    /* Page baseline */
    body{
      font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      margin:0;
      color:#0f172a;
      background: linear-gradient(135deg,#f8fafc 0%,#eef1f5 100%);
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }

    /* Container: compact, centered like hero_form.php */
    .page-container{
      max-width: 980px;
      margin: 14px auto;
      padding: 10px;
    }

    /* Card */
    .card {
      background: var(--panel-bg);
      border: 1px solid var(--card-border);
      border-radius: 12px;
      padding: 16px;
      box-shadow: 0 8px 24px rgba(8,15,35,0.06);
    }

    .card-header{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:12px;
      margin-bottom:12px;
    }
    .card-header h2{
      margin:0;
      font-size:1.15rem;
      color:var(--navy);
      font-weight:700;
    }
    .card-header small{ color: var(--muted); }

    /* compact form controls */
    label{
      display:block;
      margin:8px 0 6px;
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
      margin-bottom:6px;
      box-sizing:border-box;
    }

    textarea{ min-height:90px; resize:vertical; }

    input:focus, textarea:focus{
      outline:none;
      box-shadow:0 0 0 4px rgba(212,175,55,0.08);
      border-color:var(--gold);
    }

    /* venture block */
    .venture {
      background: rgba(15,23,42,0.02);
      padding:12px;
      border-radius:10px;
      border:1px solid rgba(8,15,35,0.03);
      margin-top:12px;
      position:relative;
    }

    .remove-btn{
      position:absolute;
      right:10px;
      top:10px;
      border:none;
      background:#ef4444;
      color:white;
      padding:6px 8px;
      border-radius:8px;
      cursor:pointer;
      font-weight:700;
    }

    .preview{
      width:72px;
      height:72px;
      object-fit:cover;
      border-radius:8px;
      border:1px solid #e6eef8;
      margin-top:6px;
      display:block;
    }

    /* actions */
    .actions{
      display:flex;
      gap:10px;
      justify-content:center;
      align-items:center;
      margin-top:14px;
    }

    .btn{
      padding:10px 16px;
      border-radius:10px;
      border:none;
      font-weight:700;
      cursor:pointer;
      font-size:0.95rem;
    }
    .btn-primary{
      background:var(--gold);
      color:white;
      box-shadow:0 10px 30px rgba(181,138,44,0.12);
    }
    .btn-primary:hover{ background:var(--gold-dark); transform:translateY(-1px); }
    .btn-add{ background:#10b981; color:white; }
    .btn-add:hover{ background:#059669; }

    /* responsive small screens */
    @media (max-width:900px){
      .page-container{ padding:8px; margin:8px; }
      .card{ padding:12px; }
    }

    /* dark-mode sync with header toggle (dg_theme_v1) */
    body.dark-mode{
      background: #071426;
      color: #E6EEF8;
    }
    body.dark-mode .card{
      background: rgba(10,20,30,0.86);
      border-color: rgba(255,255,255,0.04);
      box-shadow: none;
    }
    body.dark-mode input[type="text"], body.dark-mode textarea{
      background: rgba(255,255,255,0.04);
      border-color: rgba(255,255,255,0.08);
      color: #E6EEF8;
    }
    body.dark-mode .venture{
      background: rgba(255,255,255,0.02);
      border-color: rgba(255,255,255,0.03);
    }
    body.dark-mode .preview{ border-color: rgba(255,255,255,0.06); }
  </style>
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <div class="page-container">
    <div class="card" role="region" aria-labelledby="venturesHeading">
      <div class="card-header">
        <h2 id="venturesHeading">Ventures Section</h2>
        <small>Manage venture cards (logos, names, descriptions)</small>
      </div>

      <form method="POST" enctype="multipart/form-data" id="venturesForm">
        <label for="section_heading">Section Heading</label>
        <input id="section_heading" type="text" name="section_heading" value="<?= htmlspecialchars($section['section_heading'] ?? '') ?>">

        <label for="section_subheading">Section Subheading</label>
        <textarea id="section_subheading" name="section_subheading"><?= htmlspecialchars($section['section_subheading'] ?? '') ?></textarea>

        <div id="ventures-container">
          <?php foreach ($ventures as $i => $v): ?>
            <div class="venture">
              <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>

              <label>Logo (Upload)</label>
              <?php if (!empty($v['logo'])): ?>
                <img src="../<?= htmlspecialchars($v['logo']) ?>" alt="logo preview" class="preview">
              <?php endif; ?>
              <input type="file" name="logo[]">
              <input type="hidden" name="existing_logo[]" value="<?= htmlspecialchars($v['logo']) ?>">

              <label>Venture Name</label>
              <input type="text" name="name[]" value="<?= htmlspecialchars($v['name']) ?>">

              <label>Description</label>
              <textarea name="description[]"><?= htmlspecialchars($v['description']) ?></textarea>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="actions">
          <button type="button" id="addVenture" class="btn btn-add">+ Add Venture</button>
          <button type="submit" class="btn btn-primary">💾 Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Sync dark mode with header's localStorage key 'dg_theme_v1'
    (function(){
      const isDark = localStorage.getItem('dg_theme_v1') === 'dark';
      if (isDark) document.body.classList.add('dark-mode');
    })();

    // Dynamic add venture block
    (function(){
      const container = document.getElementById('ventures-container');
      document.getElementById('addVenture').addEventListener('click', () => {
        const div = document.createElement('div');
        div.className = 'venture';
        div.innerHTML = `
          <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>

          <label>Logo (Upload)</label>
          <input type="file" name="logo[]">
          <input type="hidden" name="existing_logo[]" value="">

          <label>Venture Name</label>
          <input type="text" name="name[]" placeholder="Enter venture name">

          <label>Description</label>
          <textarea name="description[]" placeholder="Enter venture description"></textarea>
        `;
        container.appendChild(div);
        div.scrollIntoView({behavior:'smooth', block:'center'});
      });
    })();
  </script>
</body>
</html>
