<?php
/**
 * Rocco Play Admin — Dashboard Home
 */
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';

// Fetch stats
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalImages = $pdo->query("SELECT COUNT(*) FROM media")->fetchColumn();
$visibleCategories = $pdo->query("SELECT COUNT(*) FROM categories WHERE is_visible = 1")->fetchColumn();
$hiddenCategories = $pdo->query("SELECT COUNT(*) FROM categories WHERE is_visible = 0")->fetchColumn();

// Recent uploads
$recentUploads = $pdo->query("SELECT * FROM media ORDER BY uploaded_at DESC LIMIT 12")->fetchAll();

require_once __DIR__ . '/includes/sidebar.php';
?>

      <!-- Page Header -->
      <div class="page-header">
        <h1 class="page-title">Welcome back, <?php echo htmlspecialchars(getAdminName()); ?> 👋</h1>
        <p class="page-subtitle">Here's what's happening with your OTT platform today.</p>
      </div>

      <!-- Stats Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-card-icon red">
            <i class="fas fa-layer-group"></i>
          </div>
          <div class="stat-card-value" data-count="<?php echo $totalCategories; ?>">0</div>
          <div class="stat-card-label">Total Categories</div>
          <div class="stat-card-bg-icon"><i class="fas fa-layer-group"></i></div>
        </div>

        <div class="stat-card">
          <div class="stat-card-icon gold">
            <i class="fas fa-images"></i>
          </div>
          <div class="stat-card-value" data-count="<?php echo $totalImages; ?>">0</div>
          <div class="stat-card-label">Total Images</div>
          <div class="stat-card-bg-icon"><i class="fas fa-images"></i></div>
        </div>

        <div class="stat-card">
          <div class="stat-card-icon green">
            <i class="fas fa-eye"></i>
          </div>
          <div class="stat-card-value" data-count="<?php echo $visibleCategories; ?>">0</div>
          <div class="stat-card-label">Visible Categories</div>
          <div class="stat-card-bg-icon"><i class="fas fa-eye"></i></div>
        </div>

        <div class="stat-card">
          <div class="stat-card-icon purple">
            <i class="fas fa-eye-slash"></i>
          </div>
          <div class="stat-card-value" data-count="<?php echo $hiddenCategories; ?>">0</div>
          <div class="stat-card-label">Hidden Categories</div>
          <div class="stat-card-bg-icon"><i class="fas fa-eye-slash"></i></div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="quick-actions">
        <a href="category_add.php" class="btn-admin btn-primary-admin">
          <i class="fas fa-plus"></i> Add Category
        </a>
        <a href="media_library.php" class="btn-admin btn-gold">
          <i class="fas fa-photo-film"></i> Media Library
        </a>
        <a href="hero_slides.php" class="btn-admin btn-outline-admin">
          <i class="fas fa-panorama"></i> Hero Slider
        </a>
        <a href="site_settings.php" class="btn-admin btn-outline-admin">
          <i class="fas fa-gear"></i> Site Settings
        </a>
        <a href="../index.php" target="_blank" class="btn-admin btn-outline-admin">
          <i class="fas fa-external-link-alt"></i> View Website
        </a>
      </div>

      <!-- Recent Uploads -->
      <div class="card">
        <div class="card-header-custom">
          <div class="card-title-custom">
            <i class="fas fa-clock-rotate-left"></i> Recent Uploads
          </div>
          <a href="media_library.php" class="btn-admin btn-sm-admin btn-outline-admin">View All</a>
        </div>
        <?php if (count($recentUploads) > 0): ?>
        <div class="recent-grid">
          <?php foreach ($recentUploads as $img): ?>
            <div class="recent-item">
              <?php 
                 $imgSrc = $img['file_path'] ?: $img['img_path']; 
                 if (str_starts_with($imgSrc, 'admin/')) {
                     $imgSrc = substr($imgSrc, 6);
                 } elseif (!str_starts_with($imgSrc, 'http') && !str_starts_with($imgSrc, '/')) {
                     $imgSrc = '../' . $imgSrc;
                 }
              ?>
              <img src="<?php echo htmlspecialchars($imgSrc); ?>" 
                   alt="<?php echo htmlspecialchars($img['original_name'] ?? $img['title'] ?? 'Image'); ?>"
                   loading="lazy">
            </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
          <div class="empty-state-icon"><i class="fas fa-images"></i></div>
          <div class="empty-state-text">No images uploaded yet</div>
          <div class="empty-state-hint">Start by adding a category and uploading slider images.</div>
        </div>
        <?php endif; ?>
      </div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
