<?php
// Start session before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../db.php');

// Fetch existing CTA (only one record expected)
$sql = "SELECT * FROM final_cta LIMIT 1";
$result = $conn->query($sql);
$cta = $result->fetch_assoc();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Manage Final CTA | Admin Panel</title>

  <!-- Core CSS (your compiled admin CSS) -->
  <link rel="stylesheet" href="../frontend/dist/index.css">

  <!-- Optional Ynex assets (kept for icons/consistency) -->
  <link rel="stylesheet" href="https://laravelui.spruko.com/ynex/assets/css/style.css" />
  <link rel="stylesheet" href="https://laravelui.spruko.com/ynex/assets/css/icons.css" />

  <meta name="viewport" content="width=device-width,initial-scale=1" />

  <style>
    :root{
      --navy: #071731;
      --muted: #6b7280;
      --gold: #D4AF37;
      --gold-dark: #B8941F;
      --panel-bg: rgba(255,255,255,0.96);
      --card-border: rgba(8,15,35,0.04);
      --page-bg: linear-gradient(135deg,#f8fafc 0%,#eef1f5 100%);
      --text: #0f172a;
      --input-bg: #fff;
      --input-border: #e6eef8;
      --shadow: 0 4px 25px rgba(0,0,0,0.05);
    }

    /* Base */
    html,body { height:100%; margin:0; font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial; background:var(--page-bg); color:var(--text); -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale; }
    .page { min-height:100vh; display:block; }

    /* container/card */
    .page-wrapper { max-width:980px; margin:28px auto; padding:12px; }
    .card { background:var(--panel-bg); border-radius:12px; border:1px solid var(--card-border); box-shadow:var(--shadow); overflow:visible; }
    .card-header { padding:16px 18px; border-bottom:1px solid rgba(8,15,35,0.03); background:transparent; border-top-left-radius:12px; border-top-right-radius:12px; }
    .card-header .card-title { margin:0; font-size:1.125rem; color:var(--navy); font-weight:700; }
    .card-body { padding:18px; }

    label { display:block; margin:8px 0 6px; font-weight:600; color:var(--navy); font-size:0.95rem; }
    .form-control {
      width:100%;
      padding:10px 12px;
      border-radius:10px;
      border:1px solid var(--input-border);
      background:var(--input-bg);
      color:var(--text);
      font-size:0.95rem;
      box-sizing:border-box;
    }
    textarea.form-control { min-height:90px; resize:vertical; }

    .form-control:focus { outline:none; box-shadow:0 0 0 4px rgba(212,175,55,0.08); border-color:var(--gold); }

    .row.g-3 { gap:12px; display:flex; flex-wrap:wrap; }
    .col-md-6 { width:50%; box-sizing:border-box; padding:6px; }
    @media (max-width:900px){ .col-md-6{ width:100%; } .page-wrapper{ padding:10px; } }

    .actions { text-align:center; margin-top:18px; }
    .btn {
      display:inline-flex; align-items:center; gap:8px; justify-content:center;
      padding:10px 18px; border-radius:10px; border:0; font-weight:700; cursor:pointer;
    }
    .btn-save { background:var(--gold); color:#fff; box-shadow:0 10px 30px rgba(181,138,44,0.12); }
    .btn-save:hover { background:var(--gold-dark); transform:translateY(-1px); }

    hr.separator { border:0; height:1px; background:linear-gradient(90deg, rgba(0,0,0,0.04), rgba(0,0,0,0.02)); margin:18px 0; }

    /* Compact small headings */
    .page-title { font-size:1.3rem; color:var(--navy); margin-bottom:6px; font-weight:700; text-align:center; }
    .page-sub { color:var(--muted); text-align:center; margin-bottom:14px; }

    /* ------------- Dark mode ------------- */
    body.dark-mode {
      --page-bg: #071426;
      --panel-bg: rgba(10,20,30,0.86);
      --card-border: rgba(255,255,255,0.04);
      --text: #E6EEF8;
      --input-bg: rgba(255,255,255,0.03);
      --input-border: rgba(255,255,255,0.06);
      --shadow: none;
    }
    body.dark-mode .card-header { border-bottom-color: rgba(255,255,255,0.03); }
    body.dark-mode label { color: #E6EEF8; }
    body.dark-mode .card-title { color:#E6EEF8; }
    body.dark-mode .page-sub { color: #cbd5e1; }
    body.dark-mode .form-control { color: #E6EEF8; }
    body.dark-mode .col-md-6 { padding:6px; }
  </style>
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <div class="page">
    <div class="page-wrapper" role="main">
      <div class="card" aria-labelledby="finalCtaHeading">
        <div class="card-header">
          <div>
            <div class="page-title" id="finalCtaHeading">Final CTA Section</div>
            <div class="page-sub">Update the call-to-action that appears at the page end</div>
          </div>
        </div>

        <div class="card-body">
          <form action="save_final_cta.php" method="POST" novalidate>
            <label for="cta_title">Title</label>
            <input id="cta_title" class="form-control" name="title" type="text" placeholder="Enter CTA title" value="<?= htmlspecialchars($cta['title'] ?? '') ?>" required>

            <label for="cta_subtitle">Subtitle</label>
            <input id="cta_subtitle" class="form-control" name="subtitle" type="text" placeholder="Enter subtitle" value="<?= htmlspecialchars($cta['subtitle'] ?? '') ?>">

            <label for="cta_description">Description</label>
            <textarea id="cta_description" class="form-control" name="description" placeholder="Short description" required><?= htmlspecialchars($cta['description'] ?? '') ?></textarea>

            <hr class="separator" />

            <div class="row g-3" role="group" aria-label="CTA buttons">
              <div class="col-md-6">
                <label for="button1_text">Button 1 Text</label>
                <input id="button1_text" class="form-control" name="button1_text" type="text" placeholder="e.g., Get Started" value="<?= htmlspecialchars($cta['button1_text'] ?? '') ?>">
              </div>

              <div class="col-md-6">
                <label for="button1_link">Button 1 Link</label>
                <input id="button1_link" class="form-control" name="button1_link" type="text" placeholder="/start" value="<?= htmlspecialchars($cta['button1_link'] ?? '') ?>">
              </div>

              <div class="col-md-6">
                <label for="button2_text">Button 2 Text</label>
                <input id="button2_text" class="form-control" name="button2_text" type="text" placeholder="e.g., Learn More" value="<?= htmlspecialchars($cta['button2_text'] ?? '') ?>">
              </div>

              <div class="col-md-6">
                <label for="button2_link">Button 2 Link</label>
                <input id="button2_link" class="form-control" name="button2_link" type="text" placeholder="/learn-more" value="<?= htmlspecialchars($cta['button2_link'] ?? '') ?>">
              </div>
            </div>

            <div class="actions">
              <button type="submit" class="btn btn-save" aria-label="Save CTA">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="vertical-align:middle"><path d="M12 2v2M5 7h14M5 11h14M7 15h10" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Save CTA
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

<script>
  // Apply dark-mode if the site's theme key indicates 'dark'
  (function(){
    try {
      const theme = localStorage.getItem('dg_theme_v1');
      if (theme === 'dark') document.body.classList.add('dark-mode');
      else document.body.classList.remove('dark-mode');
    } catch (e) {
      console.error('theme sync error', e);
    }
  })();

  // Optional: observe storage changes in case header or another frame toggles theme
  window.addEventListener('storage', (e) => {
    if (e.key === 'dg_theme_v1') {
      if (e.newValue === 'dark') document.body.classList.add('dark-mode');
      else document.body.classList.remove('dark-mode');
    }
  });
</script>
</body>
</html>
