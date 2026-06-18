<?php
/**
 * Rocco Play Admin — Add Popular Category
 */
$pageTitle = 'Add Popular Category';
$breadcrumb = [
  ['label' => 'Popular Categories', 'url' => 'popular_categories.php'],
  ['label' => 'Add New']
];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/includes/db.php';
    require_once __DIR__ . '/includes/auth.php';
    requireAuth();

    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    }

    $name = trim($_POST['name'] ?? '');
    $titles_count = trim($_POST['titles_count'] ?? '');
    $card_color = trim($_POST['card_color'] ?? '#B11226');
    $icon_class = trim($_POST['icon_class'] ?? '');
    $display_order = intval($_POST['display_order'] ?? 0);
    $is_icon_hidden = isset($_POST['is_icon_hidden']) ? 1 : 0;
    
    if (empty($name)) {
        $errors[] = 'Category name is required.';
    }

    // Handle Uploads
    $uploadDir = __DIR__ . '/uploads/images/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $bg_image = null;
    if (isset($_FILES['bg_image']) && $_FILES['bg_image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['bg_image']['name'], PATHINFO_EXTENSION));
        $bg_image_name = uniqid('bg_') . '.' . $ext;
        move_uploaded_file($_FILES['bg_image']['tmp_name'], $uploadDir . $bg_image_name);
        $bg_image = 'admin/uploads/images/' . $bg_image_name;
    }

    $icon_image = null;
    if (isset($_FILES['icon_image']) && $_FILES['icon_image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['icon_image']['name'], PATHINFO_EXTENSION));
        $icon_image_name = uniqid('icon_') . '.' . $ext;
        move_uploaded_file($_FILES['icon_image']['tmp_name'], $uploadDir . $icon_image_name);
        $icon_image = 'admin/uploads/images/' . $icon_image_name;
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO popular_categories (name, titles_count, card_color, icon_class, bg_image, icon_image, is_icon_hidden, display_order) 
                               VALUES (:name, :titles_count, :card_color, :icon_class, :bg_image, :icon_image, :is_icon_hidden, :display_order)");
        $stmt->execute([
            'name' => $name,
            'titles_count' => $titles_count,
            'card_color' => $card_color,
            'icon_class' => $icon_class,
            'bg_image' => $bg_image,
            'icon_image' => $icon_image,
            'is_icon_hidden' => $is_icon_hidden,
            'display_order' => $display_order
        ]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Popular Category "' . $name . '" created successfully!'];
        header('Location: popular_categories.php');
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

      <div class="page-header">
        <h1 class="page-title">Add Popular Category</h1>
        <p class="page-subtitle">Create a new popular category card for the homepage.</p>
      </div>

      <div class="card" style="max-width:700px;">
        <?php if (!empty($errors)): ?>
          <div class="flash-message flash-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars(implode(' ', $errors)); ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="popular_category_add.php" enctype="multipart/form-data" id="popularCategoryForm">
          <?php echo csrfField(); ?>

          <div class="form-group">
            <label class="form-label">Category Name <span style="color:var(--brand-red-bright);">*</span></label>
            <input type="text" name="name" class="form-control-admin" placeholder="e.g. Action" 
                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required id="catName">
          </div>

          <div class="form-group">
            <label class="form-label">Titles Count</label>
            <input type="text" name="titles_count" class="form-control-admin" placeholder="e.g. 1,200+ titles" 
                   value="<?php echo htmlspecialchars($_POST['titles_count'] ?? ''); ?>">
            <div class="form-hint">Text to display under the category name.</div>
          </div>

          <!-- Icon Class Field with Live Preview -->
          <div class="form-group">
            <label class="form-label"><i class="fas fa-icons" style="color:var(--brand-gold);margin-right:6px;"></i> Icon Class (FontAwesome / Iconscout)</label>
            <div style="display:flex;gap:12px;align-items:flex-start;">
              <div style="flex:1;">
                <input type="text" name="icon_class" id="iconClassInput" class="form-control-admin" 
                       placeholder="e.g. fas fa-explosion or uil uil-film" 
                       value="<?php echo htmlspecialchars($_POST['icon_class'] ?? ''); ?>">
                <div class="form-hint">Enter FontAwesome class like <code style="color:var(--brand-gold);">fas fa-ghost</code> or Iconscout class like <code style="color:var(--brand-gold);">uil uil-film</code>. Icon will preview live.</div>
              </div>
              <div id="iconPreviewBox" style="width:56px;height:56px;min-width:56px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.05);border:2px solid rgba(255,255,255,0.08);border-radius:12px;font-size:1.5rem;color:var(--brand-gold);transition:all 0.3s ease;">
                <i id="iconPreview" class="<?php echo htmlspecialchars($_POST['icon_class'] ?? 'fas fa-icons'); ?>" style="opacity:0.5;"></i>
              </div>
            </div>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
              <label class="form-label">Display Order</label>
              <input type="number" name="display_order" class="form-control-admin" value="<?php echo htmlspecialchars($_POST['display_order'] ?? '0'); ?>" min="0">
            </div>
            <div class="form-group">
              <label class="form-label">Card Color</label>
              <input type="color" name="card_color" class="form-control-admin" style="padding: 2px" 
                     value="<?php echo htmlspecialchars($_POST['card_color'] ?? '#B11226'); ?>">
              <div class="form-hint">Background color or glow for the card.</div>
            </div>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
              <label class="form-label">Background Image (Optional)</label>
              <input type="file" name="bg_image" class="form-control-admin" accept="image/*">
            </div>
            <div class="form-group">
              <label class="form-label">Icon Image (Optional fallback)</label>
              <input type="file" name="icon_image" class="form-control-admin" accept="image/*">
              <div class="form-hint">Used only if Icon Class is empty.</div>
            </div>
          </div>

          <div class="form-group">
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding-top:6px;">
              <input type="checkbox" name="is_icon_hidden" value="1" <?php echo !empty($_POST['is_icon_hidden']) ? 'checked' : ''; ?> style="accent-color:var(--brand-red-bright);">
              <span style="font-size:0.88rem;">Hide Icon Component</span>
            </label>
          </div>

          <div style="display:flex;gap:12px;margin-top:12px;">
            <button type="submit" class="btn-admin btn-primary-admin">
              <i class="fas fa-plus"></i> Create Popular Category
            </button>
            <a href="popular_categories.php" class="btn-admin btn-outline-admin">
              <i class="fas fa-arrow-left"></i> Cancel
            </a>
          </div>
        </form>
      </div>

<?php
$extraScripts = <<<'JS'
<script>
$(document).ready(function() {
  // Live icon preview
  $('#iconClassInput').on('input', function() {
    var val = $(this).val().trim();
    var $preview = $('#iconPreview');
    var $box = $('#iconPreviewBox');
    if (val.length > 3) {
      $preview.attr('class', val).css('opacity', '1');
      $box.css({ 'border-color': 'rgba(212,175,55,0.4)', 'box-shadow': '0 0 15px rgba(212,175,55,0.15)' });
    } else {
      $preview.attr('class', 'fas fa-icons').css('opacity', '0.5');
      $box.css({ 'border-color': 'rgba(255,255,255,0.08)', 'box-shadow': 'none' });
    }
  });
});
</script>
JS;
require_once __DIR__ . '/includes/footer.php';
?>
