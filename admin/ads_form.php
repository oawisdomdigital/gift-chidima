<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('auth.php');
require_once('../db.php');

// CSRF protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

$ad = null;
$isEdit = false;
$alert = null;

// Load ad for editing
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $mysqli->prepare("SELECT * FROM advertisements WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ad = $result->fetch_assoc();
    $isEdit = true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        http_response_code(400);
        $alert = ['type' => 'error', 'message' => 'Invalid CSRF token. Please refresh and try again.'];
    } else {
        $name = trim($_POST['name']);
        $type = $_POST['type'];
        $content = $_POST['content'];
        $link_url = $_POST['link_url'];
        $status = $_POST['status'];
        $start_date = $_POST['start_date'] ?: null;
        $end_date = $_POST['end_date'] ?: null;

        $image_url = $ad['image_url'] ?? '';

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $maxSize = 2 * 1024 * 1024;
            $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));

            if (!in_array($file_extension, $allowed)) {
                $alert = ['type' => 'error', 'message' => 'Invalid image file type. Allowed: JPG, PNG, GIF, WEBP.'];
            } elseif ($_FILES['image']['size'] > $maxSize) {
                $alert = ['type' => 'error', 'message' => 'Uploaded file too large. Maximum size is 2MB.'];
            } else {
                $target_dir = "../uploads/ads/";
                if (!file_exists($target_dir)) mkdir($target_dir, 0755, true);

                $new_filename = uniqid('ad_') . '.' . $file_extension;
                $target_file = $target_dir . $new_filename;

                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image_url = 'uploads/ads/' . $new_filename;
                } else {
                    $alert = ['type' => 'error', 'message' => 'Error uploading image file.'];
                }
            }
        }

        // If no upload error, save record
        if (!$alert) {
            if ($isEdit) {
                $stmt = $mysqli->prepare("UPDATE advertisements SET name=?, type=?, content=?, image_url=?, link_url=?, status=?, start_date=?, end_date=? WHERE id=?");
                $stmt->bind_param('ssssssssi', $name, $type, $content, $image_url, $link_url, $status, $start_date, $end_date, $id);
                $successMsg = "Advertisement updated successfully!";
            } else {
                $stmt = $mysqli->prepare("INSERT INTO advertisements (name, type, content, image_url, link_url, status, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('ssssssss', $name, $type, $content, $image_url, $link_url, $status, $start_date, $end_date);
                $successMsg = "Advertisement created successfully!";
            }

            if ($stmt->execute()) {
                $_SESSION['alert'] = ['type' => 'success', 'message' => $successMsg];
                header('Location: ads_form.php' . ($isEdit ? '?id=' . $id : ''));
                exit();
            } else {
                $alert = ['type' => 'error', 'message' => 'Database error. Please try again later.'];
            }
        }
    }
}

// Load alert message from session (after redirect)
if (isset($_SESSION['alert'])) {
    $alert = $_SESSION['alert'];
    unset($_SESSION['alert']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit Advertisement' : 'Add New Advertisement'; ?> - Admin Dashboard</title>
    <link href="../frontend/dist/index.css" rel="stylesheet">
    <style>
        /* Dark Mode */
        body.dark-mode {
            background-color: #0f172a;
            color: #f1f5f9;
        }

        body.dark-mode .bg-white {
            background-color: #1e293b !important;
        }
        body.dark-mode .text-gray-900 {
            color: #f1f5f9 !important;
        }
        body.dark-mode .text-gray-700 {
            color: #cbd5e1 !important;
        }
        body.dark-mode .text-gray-500 {
            color: #94a3b8 !important;
        }
        body.dark-mode .bg-gray-100 {
            background-color: #334155 !important;
            color: #f1f5f9 !important;
        }
        body.dark-mode input,
        body.dark-mode select,
        body.dark-mode textarea {
            background-color: #1e293b;
            color: #f1f5f9;
            border-color: #475569;
        }
        body.dark-mode input:focus,
        body.dark-mode select:focus,
        body.dark-mode textarea:focus {
            border-color: #3b82f6;
            ring-color: #3b82f6;
        }
        body.dark-mode .bg-green-50 {
            background-color: #064e3b !important;
            color: #a7f3d0 !important;
            border-color: #065f46 !important;
        }
        body.dark-mode .bg-red-50 {
            background-color: #7f1d1d !important;
            color: #fecaca !important;
            border-color: #991b1b !important;
        }
        body.dark-mode .file\:bg-blue-50 {
            background-color: #1e3a8a !important;
            color: #bfdbfe !important;
        }
        body.dark-mode .file\:hover\:bg-blue-100:hover {
            background-color: #2563eb !important;
        }
        body.dark-mode .bg-blue-600 { background-color: #2563eb !important; }
        body.dark-mode .bg-blue-600:hover { background-color: #1d4ed8 !important; }
        body.dark-mode .bg-gray-100:hover { background-color: #475569 !important; }
    </style>
</head>
<body class="bg-gray-50 <?= (isset($_SESSION['dark_mode']) && $_SESSION['dark_mode']) ? 'dark-mode' : '' ?>">
    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <!-- Page Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">
                    <?php echo $isEdit ? 'Edit Advertisement' : 'Add New Advertisement'; ?>
                </h1>
                <a href="ads_list.php" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-200">Back to List</a>
            </div>

            <!-- Alert Message -->
            <?php if ($alert): ?>
                <div class="mb-6 rounded-lg p-4 <?php echo $alert['type'] === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'; ?>">
                    <div class="flex items-center">
                        <?php if ($alert['type'] === 'success'): ?>
                            <svg class="h-5 w-5 text-green-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        <?php else: ?>
                            <svg class="h-5 w-5 text-red-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        <?php endif; ?>
                        <span class="font-medium"><?php echo htmlspecialchars($alert['message']); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" id="name" name="name" required
                            value="<?php echo htmlspecialchars($ad['name'] ?? ''); ?>"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select id="type" name="type" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="banner" <?php echo (($ad['type'] ?? '') === 'banner') ? 'selected' : ''; ?>>Banner</option>
                            <option value="inline" <?php echo (($ad['type'] ?? '') === 'inline') ? 'selected' : ''; ?>>Inline</option>
                            <option value="sidebar" <?php echo (($ad['type'] ?? '') === 'sidebar') ? 'selected' : ''; ?>>Sidebar</option>
                        </select>
                    </div>

                    <!-- HTML Content -->
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700">HTML Content</label>
                        <textarea id="content" name="content" rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($ad['content'] ?? ''); ?></textarea>
                        <p class="mt-1 text-sm text-gray-500">Enter valid HTML code for the advertisement.</p>
                    </div>

                    <!-- Image -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                        <?php if (!empty($ad['image_url'])): ?>
                            <div class="mt-2">
                                <img src="<?php echo htmlspecialchars($ad['image_url']); ?>" alt="Ad Image" class="h-32 object-contain rounded border">
                            </div>
                        <?php endif; ?>
                        <input type="file" id="image" name="image" accept="image/*"
                            class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <!-- Link -->
                    <div>
                        <label for="link_url" class="block text-sm font-medium text-gray-700">Link URL</label>
                        <input type="url" id="link_url" name="link_url"
                            value="<?php echo htmlspecialchars($ad['link_url'] ?? ''); ?>"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="status" name="status" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="active" <?php echo (($ad['status'] ?? '') === 'active') ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo (($ad['status'] ?? '') === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            <option value="scheduled" <?php echo (($ad['status'] ?? '') === 'scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                        </select>
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" id="start_date" name="start_date"
                                value="<?php echo htmlspecialchars($ad['start_date'] ?? ''); ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" id="end_date" name="end_date"
                                value="<?php echo htmlspecialchars($ad['end_date'] ?? ''); ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <?php echo $isEdit ? 'Update Advertisement' : 'Create Advertisement'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
