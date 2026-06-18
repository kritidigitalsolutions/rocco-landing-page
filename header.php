<?php
// Initialize database connection and fetch global settings 
require_once __DIR__ . '/admin/includes/db.php';

// Fetch from site_settings table
$siteSettings = $pdo->query("SELECT * FROM site_settings WHERE id = 1")->fetch();

// Also keep backward compatibility with old settings table if it exists
try {
    $stmt = $pdo->query("SELECT * FROM settings");
    $global_settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (Exception $e) {
    $global_settings = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rocco Play — Unlimited Movies, Shows & Originals | Stream Now</title>
  <meta name="description" content="Rocco Play — Your premium OTT destination for unlimited Bollywood & Hollywood movies, series, and originals. Stream in HD, anytime, anywhere." />
  <meta name="theme-color" content="#0B0B0F" />

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />

  <!-- Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

  <!-- Iconscout Unicons -->
  <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css" />

  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

  <!-- Animate.css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

  <!-- Main CSS -->
  <link rel="stylesheet" href="styles.css" />

  <!-- Favicon -->
  <link rel="icon" type="image/jpeg" href="img/logo.jpg" />
  <!-- Analytics/Tracking Scripts from Site Settings -->
  <?php echo $siteSettings['gtm_code'] ?? ($global_settings['gtm_code'] ?? ''); ?>
  <?php echo $siteSettings['ga_code'] ?? ($global_settings['ga_code'] ?? ''); ?>
</head>
<body>

  <!-- ============ CUSTOM CURSOR ============ -->
  <div class="custom-cursor" id="customCursor"></div>

  <!-- ============ AMBIENT PARTICLES ============ -->
  <div class="particles-bg" id="particlesBg"></div>

  <!-- ============ NAVIGATION ============ -->
  <nav class="navbar" id="navbar">
    <div class="container nav-inner">
      <a href="index.php" class="nav-logo">
        <img src="img/logo.jpg" alt="Rocco Play Logo" />
        <span class="nav-logo-text">RoccoPlay</span>
      </a>

      <div class="nav-links" id="navLinks">
        <a href="index.php#hero"><i class="fas fa-home"></i> Home</a>
        <a href="index.php#trending"><i class="fas fa-fire"></i> Trending</a>
        <a href="index.php#categories"><i class="fas fa-th-large"></i> Categories</a>
        <a href="index.php#originals"><i class="fas fa-star"></i> Originals</a>
        <a href="index.php#download"><i class="fas fa-mobile-alt"></i> App</a>
      </div>

      <div class="nav-actions">
        <div class="nav-download-btns">
          <a href="<?php echo htmlspecialchars($siteSettings['appstore_link'] ?? ($global_settings['app_store_link'] ?? '#download')); ?>" class="btn btn-outline btn-small"><i class="fab fa-apple"></i> App Store</a>
          <a href="<?php echo htmlspecialchars($siteSettings['playstore_link'] ?? ($global_settings['play_store_link'] ?? '#download')); ?>" class="btn btn-outline btn-small"><i class="fab fa-google-play"></i> Google Play</a>
        </div>
        <div class="hamburger" id="hamburger" aria-label="Menu toggle">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>
    </div>
  </nav>

  <!-- Mobile Navigation -->
  <div class="mobile-nav" id="mobileNav">
    <a href="index.php#hero" class="mobile-nav-link"><i class="fas fa-home"></i> Home</a>
    <a href="index.php#trending" class="mobile-nav-link"><i class="fas fa-fire"></i> Trending</a>
    <a href="index.php#categories" class="mobile-nav-link"><i class="fas fa-th-large"></i> Categories</a>
    <a href="index.php#originals" class="mobile-nav-link"><i class="fas fa-star"></i> Originals</a>
    <a href="index.php#download" class="mobile-nav-link"><i class="fas fa-mobile-alt"></i> Download App</a>
  </div>
