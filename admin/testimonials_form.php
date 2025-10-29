<?php
// Start session before any output
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include('../db.php');

// Ensure row with id=1 exists
$check = $conn->query("SELECT id FROM testimonials WHERE id = 1");
if ($check->num_rows === 0) {
  $conn->query("INSERT INTO testimonials (id, section_heading, section_subheading) VALUES (1, '', '')");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $heading = $_POST['section_heading'] ?? '';
  $subheading = $_POST['section_subheading'] ?? '';

  // Update section heading/subheading
  $stmt = $conn->prepare("UPDATE testimonials SET section_heading=?, section_subheading=? WHERE id=1");
  $stmt->bind_param("ss", $heading, $subheading);
  $stmt->execute();
  $stmt->close();

  // Clear old testimonials (id > 1)
  $conn->query("DELETE FROM testimonials WHERE id > 1");

  // Insert new testimonials
  $count = isset($_POST['quote']) ? count($_POST['quote']) : 0;
  for ($i = 0; $i < $count; $i++) {
    $quote = trim($_POST['quote'][$i] ?? '');
    $author = trim($_POST['author'][$i] ?? '');
    $role = trim($_POST['role'][$i] ?? '');

    if ($quote && $author) {
      $stmt = $conn->prepare("INSERT INTO testimonials (quote, author, role) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $quote, $author, $role);
      $stmt->execute();
      $stmt->close();
    }
  }

  echo "<script>alert('Testimonials updated successfully!'); window.location.href='testimonials_form.php';</script>";
  exit;
}

// Fetch current data
$section = $conn->query("SELECT section_heading, section_subheading FROM testimonials WHERE id=1")->fetch_assoc();
$result = $conn->query("SELECT quote, author, role FROM testimonials WHERE id > 1");
$testimonials = $result->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<?php 
$page_title = 'Testimonials Section';
include 'includes/head.php'; 
?>

  <style>
    :root{
      --navy:#071731;
      --gold:#D4AF37;
      --gold-dark:#B8941F;
      --muted:#6b7280;
      --panel-bg:rgba(255,255,255,0.96);
      --card-border:rgba(8,15,35,0.04);
    }
    body{
      font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial;
      margin:0;
      color:#0f172a;
      background:linear-gradient(135deg,#f8fafc 0%,#eef1f5 100%);
      -webkit-font-smoothing:antialiased;
    }
    .page-container{
      max-width:980px;
      margin:14px auto;
      padding:10px;
    }
    .card{
      background:var(--panel-bg);
      border:1px solid var(--card-border);
      border-radius:12px;
      padding:16px;
      box-shadow:0 8px 24px rgba(8,15,35,0.06);
    }
    .card-header{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:10px;
      margin-bottom:10px;
    }
    .card-header h2{
      margin:0;
      font-size:1.15rem;
      color:var(--navy);
      font-weight:700;
    }
    .card-header small{color:var(--muted);}

    label{
      display:block;
      margin:8px 0 6px;
      font-weight:600;
      color:var(--navy);
      font-size:0.95rem;
    }
    input[type="text"],textarea{
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
    textarea{min-height:90px;resize:vertical;}
    input:focus,textarea:focus{
      outline:none;
      box-shadow:0 0 0 4px rgba(212,175,55,0.08);
      border-color:var(--gold);
    }

    .testimonial{
      background:rgba(15,23,42,0.02);
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
    .btn-primary:hover{background:var(--gold-dark);transform:translateY(-1px);}
    .btn-add{background:#10b981;color:white;}
    .btn-add:hover{background:#059669;}

    @media(max-width:900px){
      .page-container{padding:8px;margin:8px;}
      .card{padding:12px;}
    }

    /* dark-mode */
    body.dark-mode{
      background:#071426;
      color:#E6EEF8;
    }
    body.dark-mode .card{
      background:rgba(10,20,30,0.86);
      border-color:rgba(255,255,255,0.04);
      box-shadow:none;
    }
    body.dark-mode input,body.dark-mode textarea{
      background:rgba(255,255,255,0.04);
      border-color:rgba(255,255,255,0.08);
      color:#E6EEF8;
    }
    body.dark-mode .testimonial{
      background:rgba(255,255,255,0.02);
      border-color:rgba(255,255,255,0.03);
    }
  </style>
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <div class="page-container">
    <div class="card">
      <div class="card-header">
        <h2>Testimonials Section</h2>
        <small>Manage client quotes and feedback</small>
      </div>

      <form method="POST" id="testimonialsForm">
        <label>Section Heading</label>
        <input type="text" name="section_heading" value="<?= htmlspecialchars($section['section_heading'] ?? '') ?>">

        <label>Section Subheading</label>
        <textarea name="section_subheading"><?= htmlspecialchars($section['section_subheading'] ?? '') ?></textarea>

        <h3 style="margin-top:15px;color:var(--navy);">Testimonials</h3>

        <div id="testimonials-container">
          <?php foreach ($testimonials as $t): ?>
            <div class="testimonial">
              <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>
              <label>Quote</label>
              <textarea name="quote[]" placeholder="Write testimonial here..."><?= htmlspecialchars($t['quote']) ?></textarea>

              <label>Author</label>
              <input type="text" name="author[]" value="<?= htmlspecialchars($t['author']) ?>" placeholder="Full name">

              <label>Role / Title</label>
              <input type="text" name="role[]" value="<?= htmlspecialchars($t['role']) ?>" placeholder="e.g., CEO, Tech Africa">
            </div>
          <?php endforeach; ?>
        </div>

        <div class="actions">
          <button type="button" id="addTestimonial" class="btn btn-add">+ Add Testimonial</button>
          <button type="submit" class="btn btn-primary">💾 Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Dark mode sync
    (function(){
      const isDark = localStorage.getItem('dg_theme_v1') === 'dark';
      if(isDark) document.body.classList.add('dark-mode');
    })();

    // Add testimonial dynamically
    document.getElementById('addTestimonial').addEventListener('click', ()=>{
      const div = document.createElement('div');
      div.className = 'testimonial';
      div.innerHTML = `
        <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>
        <label>Quote</label>
        <textarea name="quote[]" placeholder="Write testimonial here..."></textarea>
        <label>Author</label>
        <input type="text" name="author[]" placeholder="Full name">
        <label>Role / Title</label>
        <input type="text" name="role[]" placeholder="e.g., CEO, Tech Africa">
      `;
      document.getElementById('testimonials-container').appendChild(div);
      div.scrollIntoView({behavior:'smooth', block:'center'});
    });
  </script>
</body>
</html>
