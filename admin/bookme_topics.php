<?php
session_start();
require_once('../db.php');
require_once(__DIR__ . '/includes/auth.php');

// Handle topic deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $mysqli->query("DELETE FROM speaking_topics WHERE id = $id");
    header('Location: bookme_topics.php?msg=deleted');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $stmt = $mysqli->prepare("UPDATE speaking_topics SET icon = ?, title = ?, description = ?, sort_order = ? WHERE id = ?");
        $stmt->bind_param("sssii", $_POST['icon'], $_POST['title'], $_POST['description'], $_POST['sort_order'], $_POST['id']);
    } else {
        $stmt = $mysqli->prepare("INSERT INTO speaking_topics (icon, title, description, sort_order) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $_POST['icon'], $_POST['title'], $_POST['description'], $_POST['sort_order']);
    }
    $stmt->execute();
    header('Location: bookme_topics.php?msg=saved');
    exit;
}

// Fetch topics
$topics = [];
$result = $mysqli->query("SELECT * FROM speaking_topics ORDER BY sort_order");
while ($row = $result->fetch_assoc()) {
    $topics[] = $row;
}

// Available icons
$available_icons = ['Lightbulb', 'Users', 'Target', 'Heart', 'Sparkles', 'BookOpen', 'Brain', 'Gem', 'Star', 'Trophy'];
?>
<?php
$page_title = 'Manage Speaking Topics';
include 'includes/head.php';
?>
<style>
  /* Smooth transitions for theme toggle */
  body, input, select, textarea, button {
    transition: background-color 0.3s, color 0.3s, border-color 0.3s;
  }

  /* Dark mode overrides */
  body.dark-mode { background-color: #0f172a; color: #f1f5f9; }
  body.dark-mode .bg-gray-100 { background-color: #0f172a !important; }
  body.dark-mode .bg-gray-200 { background-color: #1e293b !important; }
  body.dark-mode .bg-gray-800 { background-color: #1e293b !important; }
  body.dark-mode .bg-gray-700 { background-color: #334155 !important; }
  body.dark-mode .bg-green-800 { background-color: #166534 !important; }
  body.dark-mode .bg-blue-600 { background-color: #2563eb !important; }
  body.dark-mode .hover\:bg-gray-700:hover { background-color: #475569 !important; }
  body.dark-mode .hover\:bg-gray-600:hover { background-color: #475569 !important; }
  body.dark-mode .hover\:bg-blue-500:hover { background-color: #1e40af !important; }
  body.dark-mode .hover\:bg-green-500:hover { background-color: #15803d !important; }
  body.dark-mode .text-gray-900 { color: #f1f5f9 !important; }
  body.dark-mode .text-gray-700 { color: #94a3b8 !important; }
  body.dark-mode .text-gray-100 { color: #f1f5f9 !important; }
  body.dark-mode .border-gray-600 { border-color: #475569 !important; }
</style>
<body class="bg-gray-100 text-gray-900 min-h-screen transition-colors duration-300">

<?php include __DIR__ . '/includes/header.php'; ?>

<div class="max-w-6xl mx-auto p-6">

  <!-- Navigation Links -->
  <div class="flex flex-wrap gap-4 mb-6">
    <a href="dashboard.php" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded shadow">← Back to Dashboard</a>
    <a href="bookme_sections.php" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded shadow">Manage Sections</a>
    <a href="bookme_requests.php" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded shadow">View Booking Requests</a>
  </div>

  <!-- Success Message -->
  <?php if (isset($_GET['msg'])): ?>
    <div class="bg-green-200 text-green-800 px-4 py-3 rounded mb-6">
      <?php 
        if ($_GET['msg'] === 'saved') echo "Topic saved successfully!";
        if ($_GET['msg'] === 'deleted') echo "Topic deleted successfully!";
      ?>
    </div>
  <?php endif; ?>

  <h1 class="text-3xl font-bold mb-6">Manage Speaking Topics</h1>

  <!-- Add New Topic Form -->
  <div class="bg-gray-200 dark:bg-gray-800 p-6 rounded mb-6" id="addTopicForm" style="display: none;">
    <h2 class="text-2xl font-semibold mb-4">Add New Topic</h2>
    <form method="POST" class="space-y-4">
      <div>
        <label class="block mb-1 font-semibold">Icon</label>
        <select name="icon" required class="w-full p-2 rounded bg-gray-100 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 text-gray-900 dark:text-gray-100">
          <?php foreach ($available_icons as $icon): ?>
            <option value="<?= $icon ?>"><?= $icon ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block mb-1 font-semibold">Title</label>
        <input type="text" name="title" required class="w-full p-2 rounded bg-gray-100 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 text-gray-900 dark:text-gray-100">
      </div>
      <div>
        <label class="block mb-1 font-semibold">Description</label>
        <textarea name="description" required class="w-full p-2 rounded bg-gray-100 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 text-gray-900 dark:text-gray-100 min-h-[80px]"></textarea>
      </div>
      <div>
        <label class="block mb-1 font-semibold">Sort Order</label>
        <input type="number" name="sort_order" value="<?= count($topics) ?>" required class="w-full p-2 rounded bg-gray-100 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 text-gray-900 dark:text-gray-100">
      </div>
      <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 rounded text-white font-semibold">Add Topic</button>
    </form>
  </div>

  <button onclick="document.getElementById('addTopicForm').style.display='block'; this.style.display='none'" class="mb-6 px-4 py-2 bg-green-600 hover:bg-green-500 dark:bg-green-700 dark:hover:bg-green-600 rounded text-white font-semibold">+ Add New Topic</button>

  <!-- Existing Topics -->
  <div class="space-y-6">
    <?php foreach ($topics as $topic): ?>
      <div class="bg-gray-200 dark:bg-gray-800 p-6 rounded shadow">
        <form method="POST" class="space-y-4">
          <input type="hidden" name="id" value="<?= $topic['id'] ?>">

          <div>
            <label class="block mb-1 font-semibold">Icon</label>
            <select name="icon" required class="w-full p-2 rounded bg-gray-100 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 text-gray-900 dark:text-gray-100">
              <?php foreach ($available_icons as $icon): ?>
                <option value="<?= $icon ?>" <?= $icon === $topic['icon'] ? 'selected' : '' ?>><?= $icon ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="block mb-1 font-semibold">Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($topic['title']) ?>" required class="w-full p-2 rounded bg-gray-100 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 text-gray-900 dark:text-gray-100">
          </div>

          <div>
            <label class="block mb-1 font-semibold">Description</label>
            <textarea name="description" required class="w-full p-2 rounded bg-gray-100 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 text-gray-900 dark:text-gray-100 min-h-[80px]"><?= htmlspecialchars($topic['description']) ?></textarea>
          </div>

          <div>
            <label class="block mb-1 font-semibold">Sort Order</label>
            <input type="number" name="sort_order" value="<?= $topic['sort_order'] ?>" required class="w-full p-2 rounded bg-gray-100 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 text-gray-900 dark:text-gray-100">
          </div>

          <div class="flex gap-4">
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 rounded text-white font-semibold">Update Topic</button>
            <button type="button" onclick="if(confirm('Are you sure you want to delete this topic?')) window.location.href='?delete=<?= $topic['id'] ?>'" class="px-4 py-2 bg-red-600 hover:bg-red-500 dark:bg-red-700 dark:hover:bg-red-600 rounded text-white font-semibold">Delete Topic</button>
          </div>
        </form>
      </div>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>
