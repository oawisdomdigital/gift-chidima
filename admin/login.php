<?php
require_once('../db.php');
if (session_status() === PHP_SESSION_NONE) session_start();

// CSRF token for login form
if (empty($_SESSION['login_csrf'])) $_SESSION['login_csrf'] = bin2hex(random_bytes(16));

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['login_csrf'], $_POST['csrf_token'])) {
    $err = 'Invalid request';
  } else {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $mysqli->prepare('SELECT id, username, password_hash, name FROM admin_users WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
      if (password_verify($password, $row['password_hash'])) {
        // success
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $row['id'];
        $_SESSION['admin_name'] = $row['name'] ?: $row['username'];
        header('Location: dashboard.php');
        exit();
      }
    }
    $err = 'Invalid credentials';
  }
}
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Login</title>
  <link href="../frontend/dist/output.css" rel="stylesheet">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
  <form method="POST" class="bg-white p-8 rounded shadow w-full max-w-md">
    <h1 class="text-xl font-bold mb-4">Admin Login</h1>
    <?php if ($err): ?>
      <div class="text-red-600 mb-4"><?php echo htmlspecialchars($err); ?></div>
    <?php endif; ?>
    <div class="mb-4">
      <label class="block text-sm">Username</label>
      <input name="username" class="mt-1 w-full border rounded px-3 py-2" />
    </div>
    <div class="mb-4">
      <label class="block text-sm">Password</label>
      <input name="password" type="password" class="mt-1 w-full border rounded px-3 py-2" />
    </div>
    <div class="flex justify-end">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['login_csrf']); ?>">
      <button class="bg-blue-600 text-white px-4 py-2 rounded">Login</button>
    </div>
  </form>
</body>
</html>