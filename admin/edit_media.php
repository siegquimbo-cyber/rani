<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$baseDir = realpath(__DIR__ . '/../assets/images');
if (!$baseDir) {
    die('Images directory not found.');
}

// Helper: Recursively get images
function getImages($dir) {
    $files = [];
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            $files = array_merge($files, getImages($path));
        } else {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'])) {
                $files[] = $path;
            }
        }
    }
    return $files;
}

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['target_path'])) {
        $inputPath = $_POST['target_path'];
    // If provided path is relative, prepend baseDir
    if (strpos($inputPath, $baseDir) !== 0) {
        $inputPath = $baseDir . DIRECTORY_SEPARATOR . ltrim($inputPath, '\\/');
    }
    $targetPath = realpath($inputPath);
    if (!$targetPath) {
        // If file does not yet exist or realpath fails due to slashes, build manually
        $targetPath = $inputPath;
    }
    if (strpos($targetPath, $baseDir) !== 0) {
        $errorMsg = 'Invalid target path.';
    } elseif (!is_uploaded_file($_FILES['new_image']['tmp_name'])) {
        $errorMsg = 'Please select a file to upload.';
    } else {
        $ext = strtolower(pathinfo($_FILES['new_image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'])) {
            $errorMsg = 'Invalid file type.';
        } else {
            if (move_uploaded_file($_FILES['new_image']['tmp_name'], $targetPath)) {
                // Bust cache by adding timestamp param to index.html for this src
                $successMsg = 'Image replaced successfully!';
                $relativeSrc = str_replace(realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR, '', $targetPath);
                $relativeSrc = str_replace('\\', '/', $relativeSrc); // normalize slashes
                $indexPath = realpath(__DIR__ . '/../index.html');
                if ($indexPath && is_writable($indexPath)) {
                    $html = file_get_contents($indexPath);
                    $timestamp = time();
                    // remove previous ?t param if present
                    $pattern = '/src="' . preg_quote($relativeSrc, '/') . '(\?t=\d+)?"/i';
                    $replacement = 'src="' . $relativeSrc . '?t=' . $timestamp . '"';
                    $htmlUpdated = preg_replace($pattern, $replacement, $html);
                    if ($htmlUpdated !== null) {
                        file_put_contents($indexPath, $htmlUpdated);
                    }
                }
            } else {
                $errorMsg = 'Failed to move uploaded file.';
            }
        }
    }
}

$images = getImages($baseDir);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Media — Rani Beauty Clinic CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <style>
        .images-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 1rem; }
        .img-card { background:#fff; border-radius:12px; padding:0.5rem; box-shadow:0 3px 10px rgba(0,0,0,0.05); text-align:center; }
        .img-card img { max-width:100%; height:100px; object-fit:cover; border-radius:8px; }
        .img-card form { margin-top:0.5rem; }
        .img-card input[type="file"] { font-size:0.8rem; }
        #preview-frame { width: 100%; height: 90vh; border: none; margin-bottom: 1.5rem; }
        .mode-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; }
        .btn-small { padding: 0.4rem 0.9rem; font-size: 0.85rem; }
        .btn-small.active { background-color: #28a745; color: #ffffff; }
        /* modal */
        .choice-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.6); display:flex; justify-content:center; align-items:center; z-index:9999; }
        .choice-box { background:#fff; padding:1.5rem 2rem; border-radius:12px; text-align:center; display:flex; flex-direction:column; gap:0.8rem; }
        .choice-box h3 { margin-bottom:0.5rem; }
    </style>
</head>
<body>
    <header class="dash-header edit-media-header">
        <div class="header-left">
            <h2 class="header-title">Edit Media</h2>
            <a href="dashboard.php" class="btn-secondary btn-small logout-btn">Back to Dashboard</a>
        </div>
        <div class="mode-actions">
            <button type="button" id="preview-btn" class="btn-secondary btn-small">Preview Mode</button>
            <button type="button" id="edit-btn" class="btn-secondary btn-small">Edit Mode</button>
            <button type="button" id="save-refresh-btn" class="btn-primary btn-small">Save Changes</button>
        </div>
    </header>
    <main class="dash-main">
        <!-- Live preview of site -->
        <iframe id="preview-frame" src="../index.html"></iframe>
        <?php if ($successMsg): ?>
            <div class="success-msg"><?= htmlspecialchars($successMsg) ?></div>
        <?php elseif ($errorMsg): ?>
            <div class="error-msg"><?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>
    </main>

<!-- Choice modal -->
    <div id="img-choice-modal" class="choice-modal" style="display:none">
        <div class="choice-box">
            <h3>Select Image Type</h3>
            <button type="button" class="btn-primary btn-small" data-choice="main">Main Image</button>
            <button type="button" class="btn-primary btn-small" data-choice="hover">Hover Image</button>
            <button type="button" class="btn-secondary btn-small" id="choice-cancel">Cancel</button>
        </div>
    </div>

    <input type="file" id="hidden-file" accept="image/*" style="display:none" />
<script src="editor_media.js"></script>
</body>
</html>
