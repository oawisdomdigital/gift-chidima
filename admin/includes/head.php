<?php
// Admin head include: loads local build for dev, production build from root, or CDN fallback
$isDev = $_SERVER['SERVER_NAME'] === 'localhost' || strpos($_SERVER['SERVER_NAME'], '.local') !== false;
$localCss = __DIR__ . '/../../frontend/dist/index.css';
$localHref = '../frontend/dist/index.css';
$productionHref = '/assets/index-DFf-Pp12.css'; // Production path in root directory
$cdnHref = 'https://cdn.tailwindcss.com';
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php echo htmlspecialchars($page_title ?? 'Admin'); ?></title>

<!-- Development mode: Use CDN for live preview -->
<?php if ($isDev): ?>
  <script src="<?php echo $cdnHref; ?>"></script>
<?php endif; ?>

<!-- Production mode: Use compiled CSS -->
<?php if (!$isDev): ?>
  <?php if (file_exists($localCss)): ?>
    <link rel="stylesheet" href="<?php echo $localHref; ?>">
  <?php else: ?>
    <link rel="stylesheet" href="<?php echo $productionHref; ?>">
  <?php endif; ?>
<?php endif; ?>

<!-- Optional small admin overrides -->
<link rel="stylesheet" href="../frontend/dist/admin-overrides.css" onerror="this.remove()">