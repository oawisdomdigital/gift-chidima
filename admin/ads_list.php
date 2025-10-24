<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('auth.php');
require_once('../db.php');

// Get all advertisements
$query = "SELECT * FROM advertisements ORDER BY created_at DESC";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Advertisements - Admin Dashboard</title>
<link href="../frontend/dist/index.css" rel="stylesheet">
<style>
/* Dark mode */
body.dark-mode {
    background-color: #0f172a;
    color: #f1f5f9;
}

body.dark-mode .container {
    background-color: #1e293b;
}

body.dark-mode table {
    color: #f1f5f9;
}

body.dark-mode thead {
    background-color: #1e293b;
    color: #94a3b8;
}

body.dark-mode tbody tr:nth-child(even) {
    background-color: #273449;
}

body.dark-mode tbody tr:nth-child(odd) {
    background-color: #1e293b;
}

body.dark-mode tbody tr:hover {
    background-color: #334155;
}

body.dark-mode .text-gray-900 { color: #f1f5f9 !important; }
body.dark-mode .text-gray-500 { color: #94a3b8 !important; }
body.dark-mode .bg-white { background-color: #1e293b !important; }

/* Badge adjustments for dark mode */
body.dark-mode .bg-purple-100 { background-color: #7c3aed !important; color: #f1f5f9 !important; }
body.dark-mode .bg-blue-100 { background-color: #3b82f6 !important; color: #f1f5f9 !important; }
body.dark-mode .bg-green-100 { background-color: #10b981 !important; color: #f1f5f9 !important; }
body.dark-mode .bg-yellow-100 { background-color: #facc15 !important; color: #111827 !important; }
body.dark-mode .bg-gray-100 { background-color: #374151 !important; color: #f1f5f9 !important; }

/* Action links */
body.dark-mode a.text-blue-600 { color: #60a5fa !important; }
body.dark-mode a.text-blue-600:hover { color: #3b82f6 !important; }
body.dark-mode a.text-red-600 { color: #f87171 !important; }
body.dark-mode a.text-red-600:hover { color: #ef4444 !important; }
</style>
</head>
<body class="<?= (isset($_SESSION['dark_mode']) && $_SESSION['dark_mode']) ? 'dark-mode' : '' ?>">
<?php include __DIR__ . '/includes/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manage Advertisements</h1>
        <a href="ads_form.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Add New Ad</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['name']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo $row['type'] === 'banner' ? 'bg-purple-100 text-purple-800' : 
                                    ($row['type'] === 'inline' ? 'bg-blue-100 text-blue-800' : 
                                    'bg-green-100 text-green-800'); ?>">
                                    <?php echo ucfirst(htmlspecialchars($row['type'])); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo $row['status'] === 'active' ? 'bg-green-100 text-green-800' : 
                                    ($row['status'] === 'scheduled' ? 'bg-yellow-100 text-yellow-800' : 
                                    'bg-gray-100 text-gray-800'); ?>">
                                    <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php if ($row['start_date'] && $row['end_date']): ?>
                                    <?php echo date('M j, Y', strtotime($row['start_date'])); ?> - 
                                    <?php echo date('M j, Y', strtotime($row['end_date'])); ?>
                                <?php else: ?>
                                    No dates set
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="ads_form.php?id=<?php echo $row['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                <a href="ads_delete.php?id=<?php echo $row['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this advertisement?')" 
                                   class="text-red-600 hover:text-red-900">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            No advertisements found. <a href="ads_form.php" class="text-blue-600 hover:underline">Create one</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
