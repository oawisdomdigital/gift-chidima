<?php
require_once(__DIR__ . '/includes/auth.php');
require_once(__DIR__ . '/../db.php');

// Get current footer content
$content = $mysqli->query("SELECT * FROM footer_content LIMIT 1")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $mysqli->prepare("UPDATE footer_content SET 
        heading = ?,
        description = ?,
        email = ?,
        linkedin_url = ?,
        twitter_url = ?,
        instagram_url = ?,
        facebook_url = ?,
        privacy_url = ?,
        terms_url = ?
    WHERE id = ?");

    $stmt->bind_param("sssssssssi",
        $_POST['heading'],
        $_POST['description'],
        $_POST['email'],
        $_POST['linkedin_url'],
        $_POST['twitter_url'],
        $_POST['instagram_url'],
        $_POST['facebook_url'],
        $_POST['privacy_url'],
        $_POST['terms_url'],
        $content['id']
    );

    if ($stmt->execute()) {
        $success = "Footer content updated successfully!";
        // Refresh content after update
        $content = $mysqli->query("SELECT * FROM footer_content LIMIT 1")->fetch_assoc();
    } else {
        $error = "Error updating footer content: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Footer Content - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Dark mode styles */
        body.dark-mode {
            background-color: #1a1a1a;
            color: #e6e6e6;
        }
        body.dark-mode .bg-white {
            background-color: #2d2d2d !important;
        }
        body.dark-mode .text-gray-700 {
            color: #d1d1d1 !important;
        }
        body.dark-mode .border-gray-300 {
            border-color: #404040 !important;
        }
        body.dark-mode input, 
        body.dark-mode textarea {
            background-color: #333 !important;
            color: #fff !important;
            border-color: #404040 !important;
        }
        body.dark-mode .shadow-md {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5);
        }

        .save-button {
    display: inline-block;
    margin-right: 0.5rem;
    padding: 0.5rem 1rem;
    background-color: #e2e8f0;
    color: #0B1C3B;
    font-weight: 500;
    border-radius: 8px;
    transition: background-color 0.3s, color 0.3s;
}
.save-button:hover {
    background-color: #cbd5e1;
}

    </style>
</head>
<body class="bg-gray-100 min-h-screen">
<?php include(__DIR__ . '/includes/header.php'); ?>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6 transition-colors">
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Footer Content</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Customize your website's footer content. All fields are optional.</p>
            </div>

            <?php if (isset($success)): ?>
                <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-8">
                <!-- Main Content Section -->
                <div class="space-y-6">
                    <div class="border-l-4 border-indigo-500 pl-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Main Content</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Set your footer's primary information.</p>
                    </div>
                    
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Heading</label>
                            <input type="text" name="heading" value="<?php echo htmlspecialchars($content['heading']); ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600"
                                placeholder="Dr. Gift Chidima Nnamoko Orairu">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contact Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($content['email']); ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600"
                                placeholder="contact@example.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <textarea name="description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600"
                            placeholder="Enter your footer description..."
                        ><?php echo htmlspecialchars($content['description']); ?></textarea>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Brief description that appears in the footer.</p>
                    </div>
                </div>

                <!-- Social Links Section -->
                <div class="space-y-6">
                    <div class="border-l-4 border-indigo-500 pl-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Social Media Links</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Connect your social media profiles.</p>
                    </div>
                    
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">LinkedIn URL</label>
                            <input type="text" name="linkedin_url" value="<?php echo htmlspecialchars($content['linkedin_url']); ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600"
                                placeholder="https://linkedin.com/in/... (optional)">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Twitter URL</label>
                            <input type="text" name="twitter_url" value="<?php echo htmlspecialchars($content['twitter_url']); ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600"
                                placeholder="https://twitter.com/... (optional)">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Instagram URL</label>
                            <input type="text" name="instagram_url" value="<?php echo htmlspecialchars($content['instagram_url']); ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600"
                                placeholder="https://instagram.com/... (optional)">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Facebook URL</label>
                            <input type="text" name="facebook_url" value="<?php echo htmlspecialchars($content['facebook_url']); ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600"
                                placeholder="https://facebook.com/... (optional)"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600"
                                placeholder="https://facebook.com/...">
                        </div>
                    </div>
                </div>

                <!-- Legal Links Section -->
                <div class="space-y-6">
                    <div class="border-l-4 border-indigo-500 pl-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Legal Pages</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Set your legal page URLs.</p>
                    </div>
                    
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Privacy Policy URL</label>
                            <input type="text" name="privacy_url" value="<?php echo htmlspecialchars($content['privacy_url']); ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600"
                                placeholder="/privacy">
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Use relative path (e.g., /privacy) or full URL.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Terms of Service URL</label>
                            <input type="text" name="terms_url" value="<?php echo htmlspecialchars($content['terms_url']); ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600"
                                placeholder="/terms">
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Use relative path (e.g., /terms) or full URL.</p>
                        </div>
                    </div>
                </div>

                <div class="flex save-button justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                        class="px-6 py-3 bg-[#D4AF37] hover:bg-[#c39f31] font-semibold rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#D4AF37] dark:focus:ring-offset-gray-900">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
    // Initialize dropdowns
    document.addEventListener('DOMContentLoaded', function() {
        const dropdowns = document.querySelectorAll('.group');
        dropdowns.forEach(dropdown => {
            const menu = dropdown.querySelector('.absolute');
            if (menu) {
                dropdown.addEventListener('mouseenter', () => {
                    menu.classList.remove('hidden');
                });
                dropdown.addEventListener('mouseleave', () => {
                    menu.classList.add('hidden');
                });
            }
        });
    });
    </script>
</body>
</html>