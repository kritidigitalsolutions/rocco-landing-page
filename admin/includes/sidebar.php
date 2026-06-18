    <!-- Sidebar Overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <img src="../img/logo.jpg" alt="Rocco Play" class="sidebar-logo">
        <span class="sidebar-brand">RoccoPlay</span>
      </div>

      <nav class="sidebar-nav">
        <div class="sidebar-label">Main</div>
        <a href="index.php" class="sidebar-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>">
          <i class="fas fa-th-large"></i>
          <span class="link-text">Dashboard</span>
        </a>

        <div class="sidebar-label">Content</div>
        <a href="categories.php" class="sidebar-link <?php echo $currentPage === 'categories' || $currentPage === 'category_add' || $currentPage === 'category_edit' ? 'active' : ''; ?>">
          <i class="fas fa-layer-group"></i>
          <span class="link-text">Categories</span>
        </a>
        <a href="popular_categories.php" class="sidebar-link <?php echo $currentPage === 'popular_categories' || $currentPage === 'popular_category_add' || $currentPage === 'popular_category_edit' ? 'active' : ''; ?>">
          <i class="fas fa-star"></i>
          <span class="link-text">Popular Categories</span>
        </a>
        <a href="media_library.php" class="sidebar-link <?php echo $currentPage === 'media_library' ? 'active' : ''; ?>">
          <i class="fas fa-photo-film"></i>
          <span class="link-text">Media Library</span>
        </a>
        <a href="messages.php" class="sidebar-link <?php echo $currentPage === 'messages' || $currentPage === 'message_view' ? 'active' : ''; ?>">
          <i class="fas fa-envelope"></i>
          <span class="link-text">Messages</span>
        </a>

        <div class="sidebar-label">Configuration</div>
        <a href="site_settings.php" class="sidebar-link <?php echo $currentPage === 'site_settings' ? 'active' : ''; ?>">
          <i class="fas fa-gear"></i>
          <span class="link-text">Site Settings</span>
        </a>

        <div class="sidebar-label">Website</div>
        <a href="../index.php" target="_blank" class="sidebar-link">
          <i class="fas fa-external-link-alt"></i>
          <span class="link-text">View Website</span>
        </a>
      </nav>

      <div class="sidebar-footer">
        <a href="logout.php" class="sidebar-logout">
          <i class="fas fa-right-from-bracket"></i>
          <span class="link-text">Logout</span>
        </a>
      </div>
    </aside>

    <!-- Topbar -->
    <header class="topbar">
      <div class="topbar-left">
        <button class="topbar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
          <i class="fas fa-bars"></i>
        </button>
        <div class="topbar-breadcrumb">
          <a href="index.php">Dashboard</a>
          <?php if (isset($breadcrumb) && is_array($breadcrumb)): ?>
            <?php foreach ($breadcrumb as $crumb): ?>
              <span class="separator"><i class="fas fa-chevron-right"></i></span>
              <?php if (isset($crumb['url'])): ?>
                <a href="<?php echo htmlspecialchars($crumb['url']); ?>"><?php echo htmlspecialchars($crumb['label']); ?></a>
              <?php else: ?>
                <span class="current"><?php echo htmlspecialchars($crumb['label']); ?></span>
              <?php endif; ?>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="topbar-right">
        <div class="topbar-date">
          <i class="fas fa-calendar-alt"></i>
          <span id="topbarDate"><?php echo date('D, d M Y'); ?></span>
        </div>
        <div class="topbar-admin">
          <div class="topbar-avatar"><?php echo strtoupper(substr(getAdminName(), 0, 1)); ?></div>
          <span class="topbar-admin-name"><?php echo htmlspecialchars(getAdminName()); ?></span>
        </div>
      </div>
    </header>

    <!-- Main Content Start -->
    <main class="main-content page-fade">
