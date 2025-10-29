<?php
session_start();
require_once('../db.php');
require_once(__DIR__ . '/includes/auth.php');

// Handle status update
if (isset($_GET['update_status'])) {
    $id = intval($_GET['id']);
    $status = $_GET['update_status'];
    if (in_array($status, ['pending', 'approved', 'declined'])) {
        $stmt = $mysqli->prepare("UPDATE booking_requests SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        header('Location: bookme_requests.php?msg=updated');
        exit;
    }
}

// Fetch requests
$requests = [];
$result = $mysqli->query("SELECT * FROM booking_requests ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) $requests[] = $row;

// Helpers
function formatDateTime($datetime) { return date("M j, Y g:i A", strtotime($datetime)); }
function getStatusColor($status) {
    switch ($status) {
        case 'pending': return 'bg-yellow-400 text-gray-900 dark:text-gray-900';
        case 'approved': return 'bg-green-600 text-white';
        case 'declined': return 'bg-red-600 text-white';
        default: return 'bg-gray-400 text-white';
    }
}
?>
<?php
$page_title = 'Booking Requests';
include 'includes/head.php';
?>
<style>
/* Dark mode variables */
body.dark-mode {
    --bg-page: #0f172a;
    --bg-card: #1e293b;
    --bg-button: #2563eb;
    --bg-button-hover: #1d4ed8;
    --text-default: #f1f5f9;
    --text-muted: #cbd5e1;
}

body {
    transition: background-color 0.3s, color 0.3s;
    background-color: var(--bg-page, #f9f9f9);
    color: var(--text-default, #111827);
}

body.dark-mode .request-card { background-color: var(--bg-card); }
body.dark-mode .nav-buttons a { background-color: #334155; color: var(--text-default); }
body.dark-mode .nav-buttons a:hover { background-color: #475569; }

.request-card {
    background-color: #f9f9f9;
    padding: 1.5rem;
    border-radius: 0.75rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    transition: background-color 0.3s;
}

.nav-buttons a {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 500;
    transition: background-color 0.3s, color 0.3s;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-weight: 600;
    font-size: 0.875rem;
}

.action-btn {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 500;
    transition: background-color 0.3s;
}

.action-btn.approve { background-color: #16a34a; color: white; }
.action-btn.approve:hover { background-color: #15803d; }
body.dark-mode .action-btn.approve { background-color: #22c55e; }
body.dark-mode .action-btn.approve:hover { background-color: #16a34a; }

.action-btn.decline { background-color: #dc2626; color: white; }
.action-btn.decline:hover { background-color: #b91c1c; }
body.dark-mode .action-btn.decline { background-color: #f87171; }
body.dark-mode .action-btn.decline:hover { background-color: #dc2626; }
</style>
</head>
<body class="<?= (isset($_SESSION['dark_mode']) && $_SESSION['dark_mode']) ? 'dark-mode' : '' ?>">
<?php include __DIR__ . '/includes/header.php'; ?>

<div class="max-w-5xl mx-auto p-4">

    <!-- Navigation Buttons -->
    <div class="nav-buttons flex flex-wrap gap-2 mb-6">
        <a href="dashboard.php">← Back to Dashboard</a>
        <a href="bookme_sections.php">Manage Sections</a>
        <a href="bookme_topics.php">Manage Topics</a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
        <div class="mb-6 p-4 bg-green-600 text-white rounded">Request status updated successfully!</div>
    <?php endif; ?>

    <h1 class="text-3xl font-bold mb-6">Booking Requests</h1>

    <?php if (empty($requests)): ?>
        <p>No booking requests yet.</p>
    <?php else: ?>
        <div class="space-y-6">
        <?php foreach ($requests as $request): ?>
            <div class="request-card">
                <div class="flex justify-between items-center mb-4">
                    <div class="text-gray-600 dark:text-gray-300 text-sm">Received: <?= formatDateTime($request['created_at']) ?></div>
                    <span class="status-badge <?= getStatusColor($request['status']) ?>">
                        <?= ucfirst($request['status']) ?>
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">
                    <div><span class="font-semibold">Name:</span> <?= htmlspecialchars($request['name']) ?></div>
                    <div><span class="font-semibold">Email:</span> <?= htmlspecialchars($request['email']) ?></div>
                    <div><span class="font-semibold">Organization:</span> <?= htmlspecialchars($request['organization']) ?></div>
                    <div><span class="font-semibold">Event Type:</span> <?= htmlspecialchars($request['event_type']) ?></div>
                    <div><span class="font-semibold">Date:</span> <?= htmlspecialchars($request['event_date']) ?></div>
                    <div><span class="font-semibold">Location:</span> <?= htmlspecialchars($request['location']) ?></div>
                    <div><span class="font-semibold">Audience Size:</span> <?= htmlspecialchars($request['audience_size']) ?></div>
                    <div><span class="font-semibold">Topics:</span> <?= htmlspecialchars($request['topics']) ?></div>
                    <div><span class="font-semibold">Budget:</span> <?= htmlspecialchars($request['budget']) ?></div>
                </div>

                <div class="mb-4"><span class="font-semibold">Message:</span> <p class="whitespace-pre-line"><?= htmlspecialchars($request['message']) ?></p></div>

                <?php if ($request['status'] === 'pending'): ?>
                    <div class="flex gap-3">
                        <a href="?update_status=approved&id=<?= $request['id'] ?>" onclick="return confirm('Approve this booking request?');" class="action-btn approve">Approve Request</a>
                        <a href="?update_status=declined&id=<?= $request['id'] ?>" onclick="return confirm('Decline this booking request?');" class="action-btn decline">Decline Request</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
