<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$siteFile = realpath(__DIR__ . '/../index.html');
if (!$siteFile || !is_writable($siteFile)) {
    die('index.html is not writable. Please adjust file permissions.');
}

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newContent = $_POST['html_content'] ?? '';
    if ($newContent === '') {
        $errorMsg = 'Content cannot be empty.';
    } else {
        if (file_put_contents($siteFile, $newContent) !== false) {
            $successMsg = 'index.html updated successfully!';
        } else {
            $errorMsg = 'Failed to write to index.html.';
        }
    }
}

$currentContent = file_get_contents($siteFile);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Content — Rani Beauty Clinic CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <style>
        #preview-frame { width: 100%; height: 90vh; border: none; }
        #save-btn { margin-top: 0; }
        .btn-small { padding: 0.4rem 0.9rem; font-size: 0.85rem; }
        .btn-small.active { background-color: #28a745; color: #ffffff; }
        .mode-actions { display:flex; gap:0.5rem; flex-wrap:wrap; align-items:center; }
        /* Override CMS layout styles for clean preview-only look */
        body { background: #ffffff; }
        .dash-main { padding: 0; }
    </style>
</head>
<body>
    <header class="dash-header edit-content-header">
        <div class="header-left">
            <h2 class="header-title">Edit Content</h2>
            <a href="dashboard.php" class="btn-secondary btn-small logout-btn">Back to Dashboard</a>
        </div>
        <div class="mode-actions">
            <button type="button" id="preview-btn" class="btn-secondary btn-small active">Preview Mode</button>
            <button type="button" id="edit-btn" class="btn-secondary btn-small">Edit Mode</button>
            <button type="button" id="save-btn" class="btn-primary btn-small">Save Changes</button>
        </div>
    </header>
    <main class="dash-main">
        <?php if ($successMsg): ?>
            <div class="success-msg"><?= htmlspecialchars($successMsg) ?></div>
        <?php elseif ($errorMsg): ?>
            <div class="error-msg"><?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <iframe id="preview-frame" src="../index.html"></iframe>
            <textarea id="html_content" name="html_content" hidden></textarea>
        </form>
        <script src="editor.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded',()=>{
                const msg=document.querySelector('.success-msg, .error-msg');
                if(msg){
                    setTimeout(()=>msg.remove(),3000);
                }
            });
        </script>
    </main>
</body>
</html>
