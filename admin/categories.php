<?php
/**
 * Rocco Play Admin — Category Management
 */
$pageTitle = 'Categories';
$breadcrumb = [['label' => 'Categories']];
require_once __DIR__ . '/includes/header.php';

// Fetch all categories with image count
$categories = $pdo->query("
  SELECT c.*, (SELECT COUNT(*) FROM media WHERE category_id = c.id) as image_count 
  FROM categories c 
  WHERE c.slug != 'popular-categories' AND c.name != 'Popular Categories'
  ORDER BY c.display_order ASC
")->fetchAll();

// Flash message
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

require_once __DIR__ . '/includes/sidebar.php';
?>

      <?php if ($flash): ?>
      <div class="flash-message flash-<?php echo $flash['type']; ?>">
        <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
        <?php echo htmlspecialchars($flash['message']); ?>
      </div>
      <?php endif; ?>

      <!-- Page Header -->
      <div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
        <div>
          <h1 class="page-title">Categories</h1>
          <p class="page-subtitle">Manage your content categories and slider sections.</p>
        </div>
        <a href="category_add.php" class="btn-admin btn-primary-admin">
          <i class="fas fa-plus"></i> Add New Category
        </a>
      </div>

      <!-- Category Table Card -->
      <div class="card">
        <div class="card-header-custom">
          <div class="card-title-custom">
            <i class="fas fa-layer-group"></i> All Categories (<?php echo count($categories); ?>)
          </div>
          <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="tableSearch" placeholder="Search categories...">
          </div>
        </div>

        <?php if (count($categories) > 0): ?>
        <div class="table-responsive-admin">
          <table class="table-admin" id="categoriesTable">
            <thead>
              <tr>
                <th style="width:40px;"></th>
                <th style="width:60px;">#</th>
                <th>Category Name</th>
                <th>Slug</th>
                <th style="width:100px;">Images</th>
                <th style="width:100px;">Status</th>
                <th style="width:200px;">Actions</th>
              </tr>
            </thead>
            <tbody id="categoryList">
              <?php foreach ($categories as $cat): ?>
              <tr data-id="<?php echo $cat['id']; ?>">
                <td><i class="fas fa-grip-vertical drag-handle"></i></td>
                <td><span class="badge-admin badge-tag"><?php echo $cat['display_order']; ?></span></td>
                <td>
                  <strong><?php echo htmlspecialchars($cat['name']); ?></strong>
                  <?php if ($cat['is_originals']): ?>
                    <span class="badge-admin badge-active" style="margin-left:6px;">Original</span>
                  <?php endif; ?>
                </td>
                <td><code style="color:var(--text-muted);font-size:0.8rem;"><?php echo htmlspecialchars($cat['slug'] ?? ''); ?></code></td>
                <td><span class="badge-admin badge-tag" style="white-space: nowrap;"><?php echo $cat['image_count']; ?> images</span></td>
                <td>
                  <label class="toggle-switch">
                    <input type="checkbox" class="visibility-toggle" data-id="<?php echo $cat['id']; ?>" <?php echo $cat['is_visible'] ? 'checked' : ''; ?>>
                    <span class="toggle-slider"></span>
                  </label>
                </td>
                <td>
                  <div style="display:flex;gap:6px;flex-wrap:wrap;">
                    <a href="category_edit.php?id=<?php echo $cat['id']; ?>" class="btn-admin btn-sm-admin btn-warning-admin" title="Edit">
                      <i class="fas fa-pen"></i>
                    </a>
                    <a href="slider_manage.php?category_id=<?php echo $cat['id']; ?>" class="btn-admin btn-sm-admin btn-info-admin" title="Manage Slider">
                      <i class="fas fa-images"></i>
                    </a>
                    <button class="btn-admin btn-sm-admin btn-danger-admin delete-category" data-id="<?php echo $cat['id']; ?>" data-name="<?php echo htmlspecialchars($cat['name']); ?>" title="Delete">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
          <div class="empty-state-icon"><i class="fas fa-layer-group"></i></div>
          <div class="empty-state-text">No categories yet</div>
          <div class="empty-state-hint">Click "Add New Category" to create your first slider category.</div>
        </div>
        <?php endif; ?>
      </div>

<?php
$extraScripts = <<<'JS'
<script>
$(document).ready(function() {

  // ===== Drag & Drop Reorder =====
  var categoryList = document.getElementById('categoryList');
  if (categoryList) {
    new Sortable(categoryList, {
      handle: '.drag-handle',
      animation: 200,
      ghostClass: 'sortable-ghost',
      dragClass: 'sortable-drag',
      onEnd: function() {
        var ids = [];
        $('#categoryList tr').each(function() {
          ids.push($(this).data('id'));
        });
        adminAjax('category_reorder.php', { ids: ids }, function() {
          // Update order numbers in UI
          $('#categoryList tr').each(function(i) {
            $(this).find('.badge-tag').first().text(i + 1);
          });
        });
      }
    });
  }

  // ===== Visibility Toggle =====
  $(document).on('change', '.visibility-toggle', function() {
    var id = $(this).data('id');
    var isVisible = $(this).is(':checked') ? 1 : 0;
    adminAjax('category_toggle.php', { id: id, is_visible: isVisible });
  });

  // ===== Delete Category =====
  $(document).on('click', '.delete-category', function() {
    var id = $(this).data('id');
    var name = $(this).data('name');
    var $row = $(this).closest('tr');
    confirmDelete('Delete category "' + name + '" and all its images?', function() {
      adminAjax('category_delete.php', { id: id }, function() {
        $row.fadeOut(300, function() { $(this).remove(); });
      });
    });
  });

});
</script>
JS;
require_once __DIR__ . '/includes/footer.php';
?>
