-- Rani Beauty Clinic CMS – Initial Database Setup
-- Run this script in phpMyAdmin or MySQL CLI to create the database and default admin user.

-- 1. Create database
CREATE DATABASE IF NOT EXISTS ranicms;
USE ranicms;

-- 2. Admins table
CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

-- 3. Default admin account (username: admin, password: admin123)
INSERT INTO admins (username, password)
VALUES ('admin', 'admin123')
ON DUPLICATE KEY UPDATE password = 'admin123';
