<?php
session_start();
require_once __DIR__ . '/../db.php';
if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$result = $mysqli->query("SELECT * FROM books ORDER BY created_at DESC");
$books = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}

// Handle messages
$message = '';
if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = '<div class="alert success">Book deleted successfully!</div>';
} elseif (isset($_GET['error']) && $_GET['error'] === 'delete_failed') {
    $message = '<div class="alert error">Failed to delete book. Please try again.</div>';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Books | Admin Panel</title>

  <link rel="stylesheet" href="../frontend/dist/index.css">
  <link rel="stylesheet" href="https://laravelui.spruko.com/ynex/assets/css/style.css" />
  <link rel="stylesheet" href="https://laravelui.spruko.com/ynex/assets/css/icons.css" />

  <meta name="viewport" content="width=device-width,initial-scale=1" />

  <style>
    :root {
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
      --row-hover: rgba(212,175,55,0.05);
    }

    body {
      font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background: var(--page-bg);
      color: var(--text);
      margin: 0;
      padding: 0;
    }

    .page-wrapper {
      max-width: 1000px;
      margin: 30px auto;
      padding: 15px;
    }

    .card {
      background: var(--panel-bg);
      border: 1px solid var(--card-border);
      border-radius: 12px;
      box-shadow: var(--shadow);
      overflow: hidden;
    }

    .card-header {
      background: transparent;
      padding: 20px;
      border-bottom: 1px solid rgba(8,15,35,0.03);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .card-title {
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--navy);
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: var(--gold);
      color: #fff;
      padding: 8px 16px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      font-weight: 600;
      transition: 0.3s;
      text-decoration: none;
    }
    .btn:hover { background: var(--gold-dark); }

    .alert {
      margin: 15px 0;
      padding: 12px 15px;
      border-radius: 6px;
      font-weight: 600;
    }
    .success { background: #d4edda; color: #155724; }
    .error { background: #f8d7da; color: #721c24; }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid var(--card-border);
    }

    th {
      background: var(--gold);
      color: #fff;
      font-weight: 700;
    }

    tr:hover {
      background: var(--row-hover);
    }

    td img {
      border-radius: 8px;
    }

    .actions a {
      text-decoration: none;
      color: var(--gold-dark);
      font-weight: 600;
      margin-right: 10px;
    }

    .actions a:hover {
      text-decoration: underline;
    }

    /* ------------- Dark mode ------------- */
    body.dark-mode {
      --page-bg: #071426;
      --panel-bg: rgba(10,20,30,0.86);
      --card-border: rgba(255,255,255,0.04);
      --text: #E6EEF8;
      --shadow: none;
      --row-hover: rgba(212,175,55,0.1);
    }

    body.dark-mode .card-title { color: #E6EEF8; }
    body.dark-mode th { background: var(--gold-dark); }
    body.dark-mode .alert.success { background: rgba(28,56,40,0.6); color: #a2f0b9; }
    body.dark-mode .alert.error { background: rgba(56,28,28,0.6); color: #ffb3b3; }
    body.dark-mode .actions a { color: var(--gold); }
  </style>
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<div class="page-wrapper">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Books Management</div>
      <a href="book_form.php" class="btn">+ Add New Book</a>
    </div>

    <div class="card-body" style="padding: 20px;">
      <?= $message ?>
      <div style="overflow-x:auto;">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Title</th>
              <th>Price</th>
              <th>Cover</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($books)): ?>
              <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:20px;">No books found.</td></tr>
            <?php else: ?>
              <?php foreach($books as $b): ?>
                <tr>
                  <td><?= $b['id'] ?></td>
                  <td><?= htmlspecialchars($b['title']) ?></td>
                  <td><?= htmlspecialchars($b['price']) ?></td>
                  <td>
                    <?php if ($b['cover_image']): ?>
                      <img src="<?= htmlspecialchars('../' . $b['cover_image']) ?>" width="60" alt="Cover">
                    <?php endif; ?>
                  </td>
                  <td class="actions">
                    <a href="book_form.php?id=<?= $b['id'] ?>">Edit</a>
                    <a href="books_delete.php?id=<?= $b['id'] ?>"
                       onclick="return confirm('Are you sure you want to delete this book? This action cannot be undone.')">
                      Delete
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
  // Sync with dark theme preference
  (function(){
    const theme = localStorage.getItem('dg_theme_v1');
    if (theme === 'dark') document.body.classList.add('dark-mode');
    else document.body.classList.remove('dark-mode');
  })();

  window.addEventListener('storage', (e) => {
    if (e.key === 'dg_theme_v1') {
      if (e.newValue === 'dark') document.body.classList.add('dark-mode');
      else document.body.classList.remove('dark-mode');
    }
  });
</script>
</body>
</html>
