<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include('../db.php');
require_once(__DIR__ . '/includes/auth.php');

// Get existing book data if editing
$book = null;
if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= $book ? 'Edit Book' : 'Upload Books' ?> | Admin Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Ynex Theme Core -->
  <link rel="stylesheet" href="https://laravelui.spruko.com/ynex/assets/css/style.css">
  <link rel="stylesheet" href="https://laravelui.spruko.com/ynex/assets/css/icons.css">
  <link rel="stylesheet" href="../frontend/dist/index.css">

  <style>
    /* General layout */
    body {
      font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto;
      background-color: var(--body-bg);
      color: var(--text-color);
      transition: background-color 0.3s, color 0.3s;
    }

    .main-container {
      padding: 2rem;
      max-width: 1100px;
      margin: 0 auto;
    }

    .card {
      background-color: var(--card-bg);
      border-radius: 12px;
      border: 1px solid var(--border-color);
      box-shadow: 0 4px 25px rgba(0,0,0,0.05);
    }

    .card-header {
      padding: 1.2rem 1.5rem;
      border-bottom: 1px solid var(--border-color);
    }

    .card-header h2 {
      margin: 0;
      font-size: 1.4rem;
      font-weight: 700;
      color: var(--text-color);
      text-align: center;
    }

    .card-header p {
      text-align: center;
      color: var(--muted-color);
      font-size: 0.95rem;
      margin-top: 0.4rem;
    }

    .card-body {
      padding: 1.5rem;
    }

    label {
      font-weight: 600;
      margin-top: 0.8rem;
      margin-bottom: 0.4rem;
      display: block;
      color: var(--label-color);
    }

    .form-control {
      width: 100%;
      padding: 0.6rem 0.9rem;
      border-radius: 8px;
      border: 1px solid var(--border-color);
      background-color: var(--input-bg);
      color: var(--text-color);
      transition: all 0.2s;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15);
    }

    textarea.form-control {
      min-height: 90px;
      resize: vertical;
    }

    .book-group {
      border: 1px solid var(--border-color);
      border-radius: 10px;
      padding: 1rem;
      background: var(--card-bg-secondary);
      margin-bottom: 1.2rem;
    }

    .book-group h3 {
      margin: 0 0 0.6rem;
      color: var(--text-color);
      font-size: 1.1rem;
    }

    .actions {
      text-align: center;
      margin-top: 1.5rem;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.6rem 1.2rem;
      border-radius: 8px;
      border: none;
      font-weight: 600;
      cursor: pointer;
      transition: 0.2s ease;
    }

    .btn-save {
      background-color: var(--accent);
      color: #fff;
    }

    .btn-save:hover {
      background-color: var(--accent-dark);
    }

    .btn-add {
      background-color: #28a745;
      color: #fff;
    }

    .btn-add:hover {
      background-color: #218838;
    }

    hr.separator {
      border: 0;
      height: 1px;
      background: var(--border-color);
      margin: 1.5rem 0;
    }

    /* Color Variables */
    :root {
      --body-bg: #f8fafc;
      --card-bg: #ffffff;
      --card-bg-secondary: #ffffffd9;
      --text-color: #0f172a;
      --muted-color: #6b7280;
      --label-color: #1e293b;
      --border-color: rgba(8,15,35,0.06);
      --accent: #D4AF37;
      --accent-dark: #b8941f;
    }

    /* Dark Mode */
    body.dark-mode {
      --body-bg: #071426;
      --card-bg: #0b1626;
      --card-bg-secondary: #0d1b30;
      --text-color: #E6EEF8;
      --muted-color: #9ca3af;
      --label-color: #E6EEF8;
      --border-color: rgba(255,255,255,0.08);
      --accent: #D4AF37;
      --accent-dark: #b8941f;
    }
  </style>
</head>

<body class="<?= isset($_SESSION['theme']) && $_SESSION['theme'] === 'dark' ? 'dark-mode' : '' ?>">
  <?php include __DIR__ . '/includes/header.php'; ?>

  <div class="main-container">
    <div class="card">
      <div class="card-header">
        <h2><?= $book ? 'Edit Book' : 'Upload Multiple Books' ?></h2>
        <p>Add or update your eBooks and physical books below</p>
      </div>

      <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
          <div id="books-container">
            <div class="book-group">
              <h3><?= $book ? 'Edit Book' : 'Book 1' ?></h3>

              <label>Title</label>
              <input type="text" name="title[]" class="form-control" required value="<?= htmlspecialchars($book['title'] ?? '') ?>">

              <label>Subtitle</label>
              <input type="text" name="subtitle[]" class="form-control" value="<?= htmlspecialchars($book['subtitle'] ?? '') ?>">

              <label>Description</label>
              <textarea name="description[]" class="form-control" required><?= htmlspecialchars($book['description'] ?? '') ?></textarea>

              <label>Detailed Description</label>
              <textarea name="detailed_description[]" class="form-control"><?= htmlspecialchars($book['detailed_description'] ?? '') ?></textarea>

              <label>Key Lessons (one per line)</label>
              <textarea name="key_lessons[]" class="form-control"><?= htmlspecialchars($book['key_lessons'] ?? '') ?></textarea>

              <label>Cover Image</label>
              <input type="file" name="cover_image[]" class="form-control">

              <label>Book File (for digital books)</label>
              <input type="file" name="book_file[]" class="form-control">

              <div class="row g-3">
                <div class="col-md-6">
                  <label>Price</label>
                  <input type="number" step="0.01" name="price[]" class="form-control" value="<?= htmlspecialchars($book['price'] ?? '') ?>" required>
                </div>
                <div class="col-md-3">
                  <label>Currency</label>
                  <select name="currency[]" class="form-control">
                    <option value="NGN">NGN</option>
                    <option value="USD">USD</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label>Type</label>
                  <select name="type[]" class="form-control">
                    <option value="physical">Physical</option>
                    <option value="digital">Digital</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="actions">
            <button type="button" class="btn btn-add" onclick="addBook()">+ Add Another Book</button>
          </div>

          <hr class="separator">

          <div class="actions">
            <button type="submit" name="submit" class="btn btn-save">
              <i class="bi bi-upload me-1"></i>
              <?= $book ? 'Update Book' : 'Upload Books' ?>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

<script>
function addBook() {
  const container = document.getElementById('books-container');
  const count = container.children.length + 1;
  const div = document.createElement('div');
  div.className = 'book-group';
  div.innerHTML = `
    <h3>Book ${count}</h3>
    <label>Title</label><input type="text" name="title[]" class="form-control" required>
    <label>Subtitle</label><input type="text" name="subtitle[]" class="form-control">
    <label>Description</label><textarea name="description[]" class="form-control" required></textarea>
    <label>Detailed Description</label><textarea name="detailed_description[]" class="form-control"></textarea>
    <label>Key Lessons (one per line)</label><textarea name="key_lessons[]" class="form-control"></textarea>
    <label>Cover Image</label><input type="file" name="cover_image[]" class="form-control">
    <label>Book File (for digital books)</label><input type="file" name="book_file[]" class="form-control">
    <div class="row g-3">
      <div class="col-md-6"><label>Price</label><input type="number" step="0.01" name="price[]" class="form-control" required></div>
      <div class="col-md-3"><label>Currency</label><select name="currency[]" class="form-control"><option value="NGN">NGN</option><option value="USD">USD</option></select></div>
      <div class="col-md-3"><label>Type</label><select name="type[]" class="form-control"><option value="physical">Physical</option><option value="digital">Digital</option></select></div>
    </div>`;
  container.appendChild(div);
}

// Handle dark mode via localStorage (shared with theme toggle)
(function() {
  try {
    const theme = localStorage.getItem('dg_theme_v1');
    if (theme === 'dark') document.body.classList.add('dark-mode');
  } catch(e){}
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
