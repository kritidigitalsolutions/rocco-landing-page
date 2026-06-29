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

-- ============ HERO SLIDES TABLE ============
CREATE TABLE IF NOT EXISTS hero_slides (
  id INT AUTO_INCREMENT PRIMARY KEY,
  image_path VARCHAR(500) NOT NULL,
  badge_text VARCHAR(255) DEFAULT NULL,
  badge_icon VARCHAR(100) DEFAULT 'fas fa-satellite-dish',
  heading VARCHAR(255) NOT NULL,
  paragraph TEXT DEFAULT NULL,
  display_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO hero_slides (image_path, badge_text, badge_icon, heading, paragraph, display_order, is_active)
SELECT * FROM (
  SELECT 'Hero banner/Hollywood hero  banner/wp9875976-hollywood-poster-wallpapers.jpg', 'Streaming Now - New Releases Every Week', 'fas fa-satellite-dish', 'Unlimited Movies, Shows & Originals', 'Dive into thousands of blockbusters, exclusive originals, and binge-worthy series. Stream in stunning 4K on any device, anytime, anywhere.', 1, 1 UNION ALL
  SELECT 'Hero banner/Hollywood hero  banner/wp10388053-hollywood-movie-poster-wallpapers.jpg', 'Streaming Now - New Releases Every Week', 'fas fa-satellite-dish', 'Unlimited Movies, Shows & Originals', 'Dive into thousands of blockbusters, exclusive originals, and binge-worthy series. Stream in stunning 4K on any device, anytime, anywhere.', 2, 1 UNION ALL
  SELECT 'Hero banner/Hollywood hero  banner/wp14444805-cinema-poster-wallpapers.jpg', 'Streaming Now - New Releases Every Week', 'fas fa-satellite-dish', 'Unlimited Movies, Shows & Originals', 'Dive into thousands of blockbusters, exclusive originals, and binge-worthy series. Stream in stunning 4K on any device, anytime, anywhere.', 3, 1 UNION ALL
  SELECT 'Hero banner/Hollywood hero  banner/wp10388016-hollywood-movie-poster-wallpapers.jpg', 'Streaming Now - New Releases Every Week', 'fas fa-satellite-dish', 'Unlimited Movies, Shows & Originals', 'Dive into thousands of blockbusters, exclusive originals, and binge-worthy series. Stream in stunning 4K on any device, anytime, anywhere.', 4, 1 UNION ALL
  SELECT 'Hero banner/Hollywood hero  banner/wp15666911-hollywood-posters-wallpapers.jpg', 'Streaming Now - New Releases Every Week', 'fas fa-satellite-dish', 'Unlimited Movies, Shows & Originals', 'Dive into thousands of blockbusters, exclusive originals, and binge-worthy series. Stream in stunning 4K on any device, anytime, anywhere.', 5, 1 UNION ALL
  SELECT 'Hero banner/Hollywood hero  banner/wp8525542-film-poster-wallpapers.jpg', 'Streaming Now - New Releases Every Week', 'fas fa-satellite-dish', 'Unlimited Movies, Shows & Originals', 'Dive into thousands of blockbusters, exclusive originals, and binge-worthy series. Stream in stunning 4K on any device, anytime, anywhere.', 6, 1 UNION ALL
  SELECT 'Hero banner/Bollywood hero banner/wp8807405-bollywood-movie-poster-wallpapers.jpg', 'Streaming Now - New Releases Every Week', 'fas fa-satellite-dish', 'Unlimited Movies, Shows & Originals', 'Dive into thousands of blockbusters, exclusive originals, and binge-worthy series. Stream in stunning 4K on any device, anytime, anywhere.', 7, 1 UNION ALL
  SELECT 'Hero banner/Bollywood hero banner/wp8807421-bollywood-movie-poster-wallpapers.jpg', 'Streaming Now - New Releases Every Week', 'fas fa-satellite-dish', 'Unlimited Movies, Shows & Originals', 'Dive into thousands of blockbusters, exclusive originals, and binge-worthy series. Stream in stunning 4K on any device, anytime, anywhere.', 8, 1 UNION ALL
  SELECT 'Hero banner/Bollywood hero banner/wp4253014-bollywood-movies-wallpapers.jpg', 'Streaming Now - New Releases Every Week', 'fas fa-satellite-dish', 'Unlimited Movies, Shows & Originals', 'Dive into thousands of blockbusters, exclusive originals, and binge-worthy series. Stream in stunning 4K on any device, anytime, anywhere.', 9, 1 UNION ALL
  SELECT 'Hero banner/Bollywood hero banner/wp8807385-bollywood-movie-poster-wallpapers.jpg', 'Streaming Now - New Releases Every Week', 'fas fa-satellite-dish', 'Unlimited Movies, Shows & Originals', 'Dive into thousands of blockbusters, exclusive originals, and binge-worthy series. Stream in stunning 4K on any device, anytime, anywhere.', 10, 1 UNION ALL
  SELECT 'Hero banner/Bollywood hero banner/wp8807390-bollywood-movie-poster-wallpapers.jpg', 'Streaming Now - New Releases Every Week', 'fas fa-satellite-dish', 'Unlimited Movies, Shows & Originals', 'Dive into thousands of blockbusters, exclusive originals, and binge-worthy series. Stream in stunning 4K on any device, anytime, anywhere.', 11, 1 UNION ALL
  SELECT 'Hero banner/Bollywood hero banner/wp8807422-bollywood-movie-poster-wallpapers.jpg', 'Streaming Now - New Releases Every Week', 'fas fa-satellite-dish', 'Unlimited Movies, Shows & Originals', 'Dive into thousands of blockbusters, exclusive originals, and binge-worthy series. Stream in stunning 4K on any device, anytime, anywhere.', 12, 1 UNION ALL
  SELECT 'Hero banner/Bollywood hero banner/wp8807444-bollywood-movie-poster-wallpapers.jpg', 'Streaming Now - New Releases Every Week', 'fas fa-satellite-dish', 'Unlimited Movies, Shows & Originals', 'Dive into thousands of blockbusters, exclusive originals, and binge-worthy series. Stream in stunning 4K on any device, anytime, anywhere.', 13, 1 UNION ALL
  SELECT 'Hero banner/Bollywood hero banner/wp8807445-bollywood-movie-poster-wallpapers.jpg', 'Streaming Now - New Releases Every Week', 'fas fa-satellite-dish', 'Unlimited Movies, Shows & Originals', 'Dive into thousands of blockbusters, exclusive originals, and binge-worthy series. Stream in stunning 4K on any device, anytime, anywhere.', 14, 1
) AS default_hero_slides
WHERE NOT EXISTS (SELECT 1 FROM hero_slides);

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
