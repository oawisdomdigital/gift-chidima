<?php
// Unlimited execution time/memory
ini_set('max_execution_time', '0');
ini_set('max_input_time', '0');
ini_set('memory_limit', '-1');
ini_set('post_max_size', '0');
ini_set('upload_max_filesize', '0');
set_time_limit(0);

if (session_status() === PHP_SESSION_NONE) session_start();
include('../db.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDir = '../uploads/gallery/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $successCount = 0;
    $errorCount = 0;

    if (!isset($_POST['title']) || !is_array($_POST['title'])) {
        $message = "No data submitted or form data is invalid.";
    } else {
        $count = count($_POST['title']);
        for ($i = 0; $i < $count; $i++) {
            $title = $_POST['title'][$i] ?? '';
            $description = $_POST['description'][$i] ?? '';
            $type = $_POST['type'][$i] ?? 'image';
            $isEmbedded = 0;
            $src = '';
            $thumbnail = '';

            if ($type === 'video' && !empty($_POST['youtube_url'][$i])) {
                $src = $_POST['youtube_url'][$i];
                $isEmbedded = 1;
                $thumbnail = $_POST['thumbnail_url'][$i] ?? '';
            } elseif (isset($_FILES['media_file']['name'][$i]) && $_FILES['media_file']['error'][$i] === 0) {
                $ext = pathinfo($_FILES['media_file']['name'][$i], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $ext;
                $destPath = $uploadDir . $filename;

                if (move_uploaded_file($_FILES['media_file']['tmp_name'][$i], $destPath)) {
                    $src = 'uploads/gallery/' . $filename;

                    if ($type === 'video' && isset($_FILES['thumbnail']['name'][$i]) && $_FILES['thumbnail']['error'][$i] === 0) {
                        $thumbExt = pathinfo($_FILES['thumbnail']['name'][$i], PATHINFO_EXTENSION);
                        $thumbName = uniqid() . '.' . $thumbExt;
                        $thumbPath = $uploadDir . $thumbName;
                        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'][$i], $thumbPath)) $thumbnail = 'uploads/gallery/' . $thumbName;
                    }
                }
            }

            if ($src) {
                $stmt = $conn->prepare("INSERT INTO gallery (type, src, thumbnail, title, description, is_embedded) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssi", $type, $src, $thumbnail, $title, $description, $isEmbedded);
                if ($stmt->execute()) $successCount++; else $errorCount++;
            }
        }
    }

    if ($successCount > 0) $message = "$successCount item(s) uploaded successfully!" . ($errorCount > 0 ? " ($errorCount failed)" : '');
    elseif ($errorCount > 0) $message = "Failed to upload any items. Please try again.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php 
$page_title = 'Upload Gallery Media';
include 'includes/head.php'; 
?>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f4f4;
    color: #333;
    transition: background 0.3s, color 0.3s;
    margin: 0;
}
body.dark-mode {
    background: #0f172a;
    color: #f1f5f9;
}
.container {
    max-width: 850px;
    margin: 20px auto;
    padding: 20px;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
body.dark-mode .container { background: #1e293b; }
h1 { text-align: center; font-size: 1.8rem; font-weight: bold; margin-bottom: 15px; }

/* Buttons */
.button {
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: inline-block;
    margin: 3px;
    transition: background 0.3s, color 0.3s;
    color: #fff;
}
.button.add { background: #10b981; }
.button.add:hover { background: #059669; }
.button.save { background: #2563eb; }
.button.save:hover { background: #1d4ed8; }

/* Gallery Item Card */
.gallery-item {
    background: #fafafa;
    border-radius: 8px;
    padding: 15px;
    margin-top: 15px;
    border: 1px solid #ddd;
    position: relative;
    transition: background 0.3s, color 0.3s, border-color 0.3s;
}
body.dark-mode .gallery-item { background: #334155; border-color: #475569; color: #f1f5f9; }

/* Remove button */
.remove-btn {
    position: absolute;
    right: 8px;
    top: 8px;
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 2px 6px;
    cursor: pointer;
    font-weight: bold;
}
body.dark-mode .remove-btn { background: #f87171; }

/* Input fields */
input[type="text"], input[type="url"], input[type="file"], textarea, select {
    width: 100%;
    padding: 8px 10px;
    border-radius: 6px;
    margin-bottom: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    transition: border 0.3s, background 0.3s, color 0.3s;
}
body.dark-mode input, body.dark-mode textarea, body.dark-mode select {
    background: #334155;
    border-color: #475569;
    color: #f1f5f9;
}

/* Messages */
.message {
    padding: 8px 12px;
    margin-bottom: 15px;
    border-radius: 6px;
    background: #e0ffe0;
    color: #007700;
    font-weight: bold;
    font-size: 14px;
}
body.dark-mode .message { background: #064e3b; color: #a7f3d0; }

/* Previews */
.preview {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 6px;
    margin-top: 5px;
    border: 1px solid #ccc;
}
body.dark-mode .preview { border-color: #475569; }
</style>
</head>
<body class="<?= (isset($_SESSION['dark_mode']) && $_SESSION['dark_mode']) ? 'dark-mode' : '' ?>">
<?php include __DIR__ . '/includes/header.php'; ?>

<div class="container">
    <h1>Upload Gallery Media</h1>

    <?php if ($message) echo "<div class='message'>$message</div>"; ?>

    <form method="post" enctype="multipart/form-data">
        <div id="gallery-container"></div>

        <div class="buttons-container text-center">
            <button type="button" class="button add" id="addGalleryItem">+ Add Media</button>
            <button type="submit" class="button save">💾 Save Changes</button>
        </div>
    </form>
</div>

<template id="gallery-item-template">
    <div class="gallery-item">
        <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✕</button>
        
        <label>Title</label>
        <input type="text" name="title[]">

        <label>Description</label>
        <textarea name="description[]"></textarea>

        <label>Type</label>
        <select name="type[]" class="media-type">
            <option value="image">Image</option>
            <option value="video">Video</option>
        </select>

        <div class="file-upload">
            <label>Upload File</label>
            <input type="file" name="media_file[]" class="media-file">
            <div class="preview-container"></div>
        </div>

        <div class="youtube-embed" style="display:none;">
            <label>YouTube URL</label>
            <input type="url" name="youtube_url[]">
            <label>Custom Thumbnail URL (optional)</label>
            <input type="url" name="thumbnail_url[]">
        </div>

        <div class="video-thumbnail" style="display:none;">
            <label>Video Thumbnail (Only for Video File)</label>
            <input type="file" name="thumbnail[]" class="thumbnail-file">
            <div class="thumbnail-preview-container"></div>
        </div>
    </div>
</template>

<script>
const container = document.getElementById('gallery-container');
const template = document.getElementById('gallery-item-template');

document.getElementById('addGalleryItem').addEventListener('click', addGalleryItem);
addGalleryItem();

function addGalleryItem() {
    const clone = template.content.cloneNode(true);
    const mediaType = clone.querySelector('.media-type');
    const youtubeEmbed = clone.querySelector('.youtube-embed');
    const videoThumbnail = clone.querySelector('.video-thumbnail');
    const mediaFile = clone.querySelector('.media-file');
    const previewContainer = clone.querySelector('.preview-container');
    const thumbnailFile = clone.querySelector('.thumbnail-file');
    const thumbnailPreviewContainer = clone.querySelector('.thumbnail-preview-container');

    mediaType.addEventListener('change', (e) => {
        if (e.target.value === 'video') {
            youtubeEmbed.style.display = 'block';
            videoThumbnail.style.display = 'block';
        } else {
            youtubeEmbed.style.display = 'none';
            videoThumbnail.style.display = 'none';
        }
    });

    mediaFile.addEventListener('change', (e) => {
        previewContainer.innerHTML = '';
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            const fileSize = (file.size / (1024 * 1024)).toFixed(2);
            const fileInfo = document.createElement('div');
            fileInfo.className = 'file-info';
            fileInfo.innerHTML = `Selected: ${file.name} (${fileSize} MB)`;
            previewContainer.appendChild(fileInfo);

            if (file.type.startsWith('image/')) {
                const preview = document.createElement('img');
                preview.className = 'preview';
                preview.src = URL.createObjectURL(file);
                previewContainer.appendChild(preview);
            } else if (file.type.startsWith('video/')) {
                const video = document.createElement('video');
                video.onloadedmetadata = function() {
                    const duration = Math.round(video.duration / 60);
                    fileInfo.innerHTML += ` | Duration: ${duration} min`;
                };
                video.src = URL.createObjectURL(file);
            }
        }
    });

    thumbnailFile.addEventListener('change', (e) => {
        thumbnailPreviewContainer.innerHTML = '';
        if (e.target.files && e.target.files[0]) {
            const preview = document.createElement('img');
            preview.className = 'preview';
            preview.src = URL.createObjectURL(e.target.files[0]);
            thumbnailPreviewContainer.appendChild(preview);
        }
    });

    container.appendChild(clone);
}
</script>
</body>
</html>
