<?php
/**
 * Rocco Play Admin — Popular Categories Management
 */
$pageTitle = 'Popular Categories';
$breadcrumb = [['label' => 'Popular Categories']];
require_once __DIR__ . '/includes/header.php';

// Fetch all popular categories
$popular_categories = $pdo->query("SELECT * FROM popular_categories ORDER BY display_order ASC")->fetchAll();

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
          <h1 class="page-title">Popular Categories</h1>
          <p class="page-subtitle">Manage the popular category cards shown on the homepage.</p>
        </div>
        <a href="popular_category_add.php" class="btn-admin btn-primary-admin">
          <i class="fas fa-plus"></i> Add Popular Category
        </a>
      </div>

      <!-- Popular Categories Table Card -->
      <div class="card">
        <div class="card-header-custom">
          <div class="card-title-custom">
            <i class="fas fa-star"></i> All Popular Categories (<?php echo count($popular_categories); ?>)
          </div>
        </div>

        <?php if (count($popular_categories) > 0): ?>
        <div class="table-responsive-admin">
          <table class="table-admin" id="popularCategoriesTable">
            <thead>
              <tr>
                <th style="width:40px;"></th>
                <th style="width:60px;">#</th>
                <th style="width:80px;">Icon</th>
                <th>Category Name</th>
                <th>Icon Class</th>
                <th>Titles Count</th>
                <th>Color</th>
                <th style="width:100px;">Status</th>
                <th style="width:150px;">Actions</th>
              </tr>
            </thead>
            <tbody id="popularList">
              <?php foreach ($popular_categories as $cat): ?>
              <tr data-id="<?php echo $cat['id']; ?>">
                <td><i class="fas fa-grip-vertical drag-handle"></i></td>
                <td><span class="badge-admin badge-tag"><?php echo $cat['display_order']; ?></span></td>
                <td>
                  <?php if (!empty($cat['icon_class'])): ?>
                    <div style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.05);border-radius:8px;border:1px solid rgba(255,255,255,0.08);">
                      <i class="<?php echo htmlspecialchars($cat['icon_class']); ?>" style="font-size:1.2rem;color:<?php echo htmlspecialchars($cat['card_color']); ?>;"></i>
                    </div>
                  <?php elseif ($cat['icon_image']): ?>
                    <img src="../<?php echo htmlspecialchars($cat['icon_image']); ?>" alt="Icon" style="width:32px;height:32px;object-fit:cover;border-radius:4px;background:#333;">
                  <?php else: ?>
                    <div style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;background:#1a1a22;border-radius:8px;"><i class="fas fa-image" style="color:#666;"></i></div>
                  <?php endif; ?>
                </td>
                <td><strong><?php echo htmlspecialchars($cat['name']); ?></strong></td>
                <td>
                  <?php if (!empty($cat['icon_class'])): ?>
                    <code style="color:var(--brand-gold);font-size:0.78rem;background:rgba(212,175,55,0.1);padding:3px 8px;border-radius:4px;"><?php echo htmlspecialchars($cat['icon_class']); ?></code>
                  <?php else: ?>
                    <span style="color:var(--text-muted);font-size:0.82rem;">—</span>
                  <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($cat['titles_count']); ?></td>
                <td>
                  <span style="display:inline-block;width:16px;height:16px;border-radius:50%;background:<?php echo htmlspecialchars($cat['card_color']); ?>;vertical-align:middle;margin-right:6px;"></span>
                  <?php echo htmlspecialchars($cat['card_color']); ?>
                </td>
                <td>
                  <label class="toggle-switch">
                    <input type="checkbox" class="visibility-toggle" data-id="<?php echo $cat['id']; ?>" <?php echo !$cat['is_hidden'] ? 'checked' : ''; ?>>
                    <span class="toggle-slider"></span>
                  </label>
                </td>
                <td>
                  <div style="display:flex;gap:6px;flex-wrap:wrap;">
                    <a href="popular_category_edit.php?id=<?php echo $cat['id']; ?>" class="btn-admin btn-sm-admin btn-warning-admin" title="Edit">
                      <i class="fas fa-pen"></i>
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
          <div class="empty-state-icon"><i class="fas fa-star"></i></div>
          <div class="empty-state-text">No popular categories yet</div>
          <div class="empty-state-hint">Click "Add Popular Category" to create your first card.</div>
        </div>
        <?php endif; ?>
      </div>

<?php
$extraScripts = <<<'JS'
<script>
$(document).ready(function() {

  // ===== Drag & Drop Reorder =====
  var popularList = document.getElementById('popularList');
  if (popularList) {
    new Sortable(popularList, {
      handle: '.drag-handle',
      animation: 200,
      ghostClass: 'sortable-ghost',
      dragClass: 'sortable-drag',
      onEnd: function() {
        var ids = [];
        $('#popularList tr').each(function() {
          ids.push($(this).data('id'));
        });
        adminAjax('popular_category_reorder.php', { ids: ids }, function() {
          // Update order numbers in UI
          $('#popularList tr').each(function(i) {
            $(this).find('.badge-tag').first().text(i + 1);
          });
        });
      }
    });
  }

  // ===== Visibility Toggle =====
  $(document).on('change', '.visibility-toggle', function() {
    var id = $(this).data('id');
    var isHidden = $(this).is(':checked') ? 0 : 1;
    adminAjax('popular_category_toggle.php', { id: id, is_hidden: isHidden });
  });

  // ===== Delete Category =====
  $(document).on('click', '.delete-category', function() {
    var id = $(this).data('id');
    var name = $(this).data('name');
    var $row = $(this).closest('tr');
    confirmDelete('Delete popular category "' + name + '"?', function() {
      adminAjax('popular_category_delete.php', { id: id }, function() {
        $row.fadeOut(300, function() { $(this).remove(); });
      });
    });
  });

});
</script>
JS;
require_once __DIR__ . '/includes/footer.php';
?>
