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
    // Trim username to avoid accidental whitespace issues
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Prepare statement and check for errors
    $stmt = $mysqli->prepare('SELECT id, username, password_hash, name FROM admin_users WHERE username = ? LIMIT 1');
    if (! $stmt) {
      error_log('Login prepare failed: ' . $mysqli->error);
      $err = 'Server error. Please try again later.';
    } else {
      $stmt->bind_param('s', $username);
      if (! $stmt->execute()) {
        error_log('Login execute failed: ' . $stmt->error);
        $err = 'Server error. Please try again later.';
      } else {
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;

        if ($row) {
          // Check password
          if (password_verify($password, $row['password_hash'])) {
            // success
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['name'] ?: $row['username'];
            header('Location: dashboard.php');
            exit();
          } else {
            // Wrong password
            error_log('Login failed: password_verify failed for user ' . $username);
            $err = 'Invalid credentials';
          }
        } else {
          // No such username
          error_log('Login failed: no user found with username ' . $username);
          $err = 'Invalid credentials';
        }
      }
    }
  }
}
?>

<!doctype html>
<html>
<head>
<?php 
$page_title = 'Admin Login';
include 'includes/head.php'; 
?>
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