Rani Beauty Clinic CMS — Quick Setup Guide
=========================================

1) Import Database
------------------
・Open phpMyAdmin (http://localhost/phpmyadmin) while XAMPP is running.
・Create the database by importing `admin/initial_setup.sql` OR copy/paste the SQL into the SQL tab.

2) Configure MySQL Credentials (if needed)
-----------------------------------------
・Edit `admin/config.php` and update `$user`, `$pass`, and `$host` to match your local environment.

3) Access the Admin Panel
-------------------------
・Navigate to http://localhost/path-to-project/admin/login.php
・Login with:
    Username: admin
    Password: admin123
・You will be redirected to a blank dashboard page, ready for future widgets.

4) Security Notes
-----------------
・For production, always hash passwords (e.g., password_hash) and use HTTPS.
・Restrict /admin path via .htaccess or server config if necessary.
