<?php
session_start();
require_once __DIR__ . '/../api/config.php';
require_once(__DIR__ . '/includes/auth.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $subtitle = $_POST['subtitle'] ?? '';
    $author = $_POST['author'] ?? '';
    $price = $_POST['price'] ?? 0;
    $description = $_POST['description'] ?? '';

    // handle optional file upload (quick, use move_uploaded_file)
    $cover_path = null;
    if (!empty($_FILES['cover']['name'])) {
        $ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
        $destName = uniqid() . '.' . $ext;
        $dest = __DIR__ . '/../uploads/' . $destName;
        if (!is_dir(dirname($dest))) mkdir(dirname($dest),0755,true);
        if (move_uploaded_file($_FILES['cover']['tmp_name'], $dest)) {
            // URL path accessible by browser (adjust as needed)
            $cover_path = '/myapp/uploads/' . $destName;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO books (title, subtitle, author, cover_path, price, description) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$title, $subtitle, $author, $cover_path, $price, $description]);
    header('Location: books_list.php');
    exit;
}
?>
<!doctype html>
<html>
<head>
<?php 
$page_title = 'Add Book';
include 'includes/head.php'; 
?>
  <link rel="stylesheet" href="../frontend/dist/index.css">
  </head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
  <h1>Add Book</h1>
  <form method="post" enctype="multipart/form-data">
    <label>Title <input name="title"></label><br/>
    <label>Subtitle <input name="subtitle"></label><br/>
    <label>Author <input name="author"></label><br/>
    <label>Price <input name="price" type="number" step="0.01"></label><br/>
    <label>Cover <input name="cover" type="file"></label><br/>
    <label>Description <textarea name="description"></textarea></label><br/>
    <button type="submit">Add</button>
  </form>
</body>
</html>
