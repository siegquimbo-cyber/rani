<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard — Rani Beauty Clinic CMS</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="style.css" />
</head>
    <body>
        <div class="dash-wrapper">
            <aside class="sidebar">
                <div class="sidebar-brand">
                    <img src="../assets/images/logo.png" alt="Rani Beauty Clinic Logo" class="sidebar-logo" />
                </div>

                <nav class="sidebar-nav">
                    <a href="dashboard.php" class="sidebar-link active"><i class="fa fa-home"></i> Dashboard</a>
                    <a href="edit_media.php" class="sidebar-link"><i class="fa fa-image"></i> Media</a>
                    <a href="edit_content.php" class="sidebar-link"><i class="fa fa-file-alt"></i> Content</a>
                    <a href="logout.php" class="sidebar-link"><i class="fa fa-sign-out-alt"></i> Logout</a>
                </nav>
            </aside>

            <div class="dash-content">
                <header class="dash-header">
                    <h2>Welcome <?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']) : 'Admin'; ?></h2>
                </header>

                <main class="dash-main">
                    <!-- Quick links -->

                    <section class="preview-wrapper">
                        <div class="preview-header">
                            <h3>Homepage Preview</h3>
                            <a href="../index.html" target="_blank" class="btn-secondary small">Open in new tab</a>
                        </div>
                        <iframe src="../index.html" title="Site Preview" class="site-preview" loading="lazy"></iframe>
                    </section>
                </main>
            </div>
        </div>

        <!-- Font Awesome for icons -->
        <script src="https://kit.fontawesome.com/25e8e2a0e0.js" crossorigin="anonymous"></script>
    </body>
</html>
