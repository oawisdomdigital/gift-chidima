<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $heading = $_POST['section_heading'] ?? '';
  $subheading = $_POST['section_subheading'] ?? '';

  // Ensure row with id=1 exists
  $check = $conn->query("SELECT id FROM awards WHERE id = 1");
  if ($check->num_rows === 0) {
    $conn->query("INSERT INTO awards (id, section_heading, section_subheading) VALUES (1, '', '')");
  }

  // Update section heading/subheading
  $stmt = $conn->prepare("UPDATE awards SET section_heading=?, section_subheading=? WHERE id=1");
  $stmt->bind_param("ss", $heading, $subheading);
  $stmt->execute();
  $stmt->close();

  // Remove old awards/media (id > 1)
  $conn->query("DELETE FROM awards WHERE id > 1");

  // Insert award entries
  $awardCount = count($_POST['award_title'] ?? []);
  for ($i = 0; $i < $awardCount; $i++) {
    $awardTitle = $_POST['award_title'][$i] ?? '';
    $awardIcon = null;

    // Upload award icon
    if (!empty($_FILES['award_icon']['name'][$i])) {
      $uploadDir = '../uploads/awards/';
      if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
      $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($_FILES['award_icon']['name'][$i]));
      $targetPath = $uploadDir . $fileName;
      move_uploaded_file($_FILES['award_icon']['tmp_name'][$i], $targetPath);
      $awardIcon = 'uploads/awards/' . $fileName;
    } else {
      $awardIcon = $_POST['existing_award_icon'][$i] ?? null;
    }

    $stmt = $conn->prepare("INSERT INTO awards (award_title, award_icon) VALUES (?, ?)");
    $stmt->bind_param("ss", $awardTitle, $awardIcon);
    $stmt->execute();
  }

  // Insert media logos
  $mediaCount = count($_FILES['media_logo']['name'] ?? []);
  for ($i = 0; $i < $mediaCount; $i++) {
    if (!empty($_FILES['media_logo']['name'][$i])) {
      $uploadDir = '../uploads/media/';
      if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
      $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($_FILES['media_logo']['name'][$i]));
      $targetPath = $uploadDir . $fileName;
      move_uploaded_file($_FILES['media_logo']['tmp_name'][$i], $targetPath);
      $mediaLogo = 'uploads/media/' . $fileName;

      $stmt = $conn->prepare("INSERT INTO awards (media_logo) VALUES (?)");
      $stmt->bind_param("s", $mediaLogo);
      $stmt->execute();
    }
  }

  echo "<script>alert('Awards section updated successfully!'); window.location.href='awards_form.php';</script>";
  exit;
}

// Fetch for display
$section = $conn->query("SELECT section_heading, section_subheading FROM awards WHERE id=1")->fetch_assoc();
$awards = $conn->query("SELECT award_title, award_icon FROM awards WHERE award_title IS NOT NULL")->fetch_all(MYSQLI_ASSOC);
$media = $conn->query("SELECT media_logo FROM awards WHERE media_logo IS NOT NULL")->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<?php 
$page_title = 'Edit Awards Section';
include 'includes/head.php'; 
?>
<style>
:root {
  --navy: #071731;
  --gold: #D4AF37;
  --gold-dark: #B8941F;
  --muted: #6b7280;
  --panel-bg: rgba(255,255,255,0.96);
  --card-border: rgba(8,15,35,0.04);
}

/* base page */
body {
  font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto;
  margin: 0;
  background: linear-gradient(135deg, #f8fafc 0%, #eef1f5 100%);
  color: #0f172a;
}

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
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}
.form-header h2 {
  margin: 0;
  font-size: 1.25rem;
  color: var(--navy);
  font-weight: 700;
}
.form-header small { color: var(--muted); }

label {
  display: block;
  margin: 10px 0 6px;
  font-weight: 600;
  color: var(--navy);
  font-size: 0.95rem;
}

input[type="text"], textarea, input[type="file"] {
  width: 100%;
  padding: 10px 12px;
  border-radius: 10px;
  border: 1px solid #e6eef8;
  background: white;
  font-size: 0.95rem;
  color: var(--navy);
  margin-bottom: 6px;
}

textarea { min-height: 90px; resize: vertical; }

input:focus, textarea:focus {
  outline: none;
  box-shadow: 0 0 0 4px rgba(212,175,55,0.08);
  border-color: var(--gold);
}

/* section blocks */
.section {
  background: rgba(15,23,42,0.02);
  padding: 12px;
  border-radius: 10px;
  border: 1px solid rgba(8,15,35,0.03);
  margin-top: 12px;
  position: relative;
}

.remove-btn {
  position: absolute;
  right: 10px;
  top: 10px;
  border: none;
  background: #ef4444;
  color: white;
  padding: 6px 8px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
}

.preview {
  width: 64px;
  height: 64px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #e6eef8;
  margin-top: 6px;
}

/* buttons */
.actions {
  display: flex;
  gap: 10px;
  align-items: center;
  justify-content: center;
  margin-top: 14px;
}
.btn {
  padding: 10px 16px;
  border-radius: 10px;
  border: none;
  font-weight: 700;
  cursor: pointer;
  font-size: 0.95rem;
}
.btn-primary { background: var(--gold); color: white; box-shadow: 0 10px 30px rgba(181,138,44,0.12); }
.btn-primary:hover { background: var(--gold-dark); transform: translateY(-1px); }
.btn-add { background: #10b981; color: white; }
.btn-add:hover { background: #059669; }

/* dark mode */
body.dark-mode {
  background: #071426;
  color: #E6EEF8;
}
body.dark-mode .form-card {
  background: rgba(10,20,30,0.86);
  border-color: rgba(255,255,255,0.04);
}
body.dark-mode input[type="text"],
body.dark-mode textarea {
  background: rgba(255,255,255,0.04);
  border-color: rgba(255,255,255,0.06);
  color: #E6EEF8;
}
body.dark-mode .section {
  background: rgba(255,255,255,0.02);
  border-color: rgba(255,255,255,0.03);
}
body.dark-mode .preview {
  border-color: rgba(255,255,255,0.06);
}
</style>
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<div class="page-container">
  <div class="form-card">
    <div class="form-header">
      <h2>Awards Section Editor</h2>
      <small>Manage awards and media logos</small>
    </div>

    <form method="POST" enctype="multipart/form-data">
      <label>Section Heading</label>
      <input type="text" name="section_heading" value="<?= htmlspecialchars($section['section_heading'] ?? '') ?>">

      <label>Section Subheading</label>
      <textarea name="section_subheading"><?= htmlspecialchars($section['section_subheading'] ?? '') ?></textarea>

      <h3 style="margin-top:15px;color:var(--navy);">Awards</h3>
      <div id="awards-container">
        <?php foreach ($awards as $a): ?>
          <div class="section">
            <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>
            <label>Award Title</label>
            <input type="text" name="award_title[]" value="<?= htmlspecialchars($a['award_title']) ?>">
            <label>Icon (Upload)</label>
            <?php if ($a['award_icon']): ?>
              <img src="../<?= htmlspecialchars($a['award_icon']) ?>" class="preview" alt="">
            <?php endif; ?>
            <input type="file" name="award_icon[]">
            <input type="hidden" name="existing_award_icon[]" value="<?= htmlspecialchars($a['award_icon']) ?>">
          </div>
        <?php endforeach; ?>
      </div>
      <button type="button" class="btn btn-add" id="addAward">+ Add Award</button>

      <h3 style="margin-top:20px;color:var(--navy);">Media Logos</h3>
      <div id="media-container">
        <?php foreach ($media as $m): ?>
          <div class="section">
            <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>
            <?php if ($m['media_logo']): ?>
              <img src="../<?= htmlspecialchars($m['media_logo']) ?>" class="preview" alt="">
            <?php endif; ?>
            <input type="file" name="media_logo[]">
          </div>
        <?php endforeach; ?>
      </div>
      <button type="button" class="btn btn-add" id="addMedia">+ Add Media Logo</button>

      <div class="actions">
        <button type="submit" class="btn btn-primary">💾 Save Changes</button>
      </div>
    </form>
  </div>
</div>

<script>
// Sync dark mode with header theme
(function(){
  const isDark = localStorage.getItem('dg_theme_v1') === 'dark';
  if (isDark) document.body.classList.add('dark-mode');
})();

// Add Award
document.getElementById('addAward').onclick = () => {
  const div = document.createElement('div');
  div.className = 'section';
  div.innerHTML = `
    <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>
    <label>Award Title</label>
    <input type="text" name="award_title[]" placeholder="Award title">
    <label>Icon (Upload)</label>
    <input type="file" name="award_icon[]">
    <input type="hidden" name="existing_award_icon[]" value="">
  `;
  document.getElementById('awards-container').appendChild(div);
};

// Add Media
document.getElementById('addMedia').onclick = () => {
  const div = document.createElement('div');
  div.className = 'section';
  div.innerHTML = `
    <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>
    <label>Media Logo</label>
    <input type="file" name="media_logo[]">
  `;
  document.getElementById('media-container').appendChild(div);
};
</script>
</body>
</html>
