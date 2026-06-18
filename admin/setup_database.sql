-- ============================================
-- ROCCO PLAY — Admin Dashboard Database Setup
-- Run this SQL in phpMyAdmin or MySQL CLI
-- ============================================

CREATE DATABASE IF NOT EXISTS u858864769_roccoplay CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE u858864769_roccoplay;

-- ============ ADMIN USERS TABLE ============
CREATE TABLE IF NOT EXISTS admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  reset_otp VARCHAR(10) DEFAULT NULL,
  reset_otp_expires TIMESTAMP NULL DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed default admin (password: Admin@123)
INSERT INTO admin_users (name, email, password) VALUES 
('Admin', 'admin@roccoplay.com', '$2y$12$6HbuXKT6jbv6mn1US2OpDep7RVHevBGAxV2qfaNsAi9Rem7ZSmz0u')
ON DUPLICATE KEY UPDATE name=name;

-- ============ CATEGORIES TABLE ============
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) DEFAULT NULL,
  description TEXT DEFAULT NULL,
  display_order INT DEFAULT 0,
  is_visible TINYINT(1) DEFAULT 1,
  is_hidden TINYINT(1) DEFAULT 0,
  is_originals TINYINT(1) DEFAULT 0,
  icon VARCHAR(100) DEFAULT 'fas fa-film',
  section_label VARCHAR(255) DEFAULT NULL,
  glow_color VARCHAR(100) DEFAULT 'glow-red',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============ MEDIA TABLE ============
CREATE TABLE IF NOT EXISTS media (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  filename VARCHAR(255) DEFAULT NULL,
  original_name VARCHAR(255) DEFAULT NULL,
  file_path VARCHAR(500) DEFAULT NULL,
  file_size INT DEFAULT 0,
  display_order INT DEFAULT 0,
  tag VARCHAR(100) DEFAULT NULL,
  is_active TINYINT(1) DEFAULT 1,
  img_path VARCHAR(500) DEFAULT NULL,
  title VARCHAR(255) DEFAULT NULL,
  rating VARCHAR(10) DEFAULT '0',
  year_or_seasons VARCHAR(50) DEFAULT NULL,
  genre VARCHAR(100) DEFAULT NULL,
  badge_text VARCHAR(50) DEFAULT NULL,
  badge_class VARCHAR(50) DEFAULT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============ SITE SETTINGS TABLE ============
CREATE TABLE IF NOT EXISTS site_settings (
  id INT PRIMARY KEY DEFAULT 1,
  copyright_text TEXT DEFAULT NULL,
  playstore_link VARCHAR(500) DEFAULT NULL,
  appstore_link VARCHAR(500) DEFAULT NULL,
  custom_app_link VARCHAR(500) DEFAULT NULL,
  gtm_code TEXT DEFAULT NULL,
  ga_code TEXT DEFAULT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed default settings row
INSERT INTO site_settings (id, copyright_text) VALUES 
(1, '&copy; 2026 RoccoPlay. All rights reserved.')
ON DUPLICATE KEY UPDATE id=id;

-- ============ POPULAR CATEGORIES TABLE ============
CREATE TABLE IF NOT EXISTS popular_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  titles_count VARCHAR(255) DEFAULT '0 titles',
  card_color VARCHAR(100) DEFAULT '#B11226',
  bg_image VARCHAR(500) DEFAULT NULL,
  icon_class VARCHAR(100) DEFAULT '',
  icon_image VARCHAR(500) DEFAULT NULL,
  is_icon_hidden TINYINT(1) DEFAULT 0,
  is_hidden TINYINT(1) DEFAULT 0,
  display_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
