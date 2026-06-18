<?php
/**
 * Rocco Play Admin — Add Category
 */
$pageTitle = 'Add Category';
$breadcrumb = [
  ['label' => 'Categories', 'url' => 'categories.php'],
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
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $display_order = intval($_POST['display_order'] ?? 0);
    $icon = trim($_POST['icon'] ?? 'fas fa-film');
    $section_label = trim($_POST['section_label'] ?? '');
    $is_originals = isset($_POST['is_originals']) ? 1 : 0;
    $glow_color = trim($_POST['glow_color'] ?? 'glow-red');

    if (empty($name)) {
        $errors[] = 'Category name is required.';
    }
    if (empty($slug)) {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, display_order, is_visible, is_hidden, icon, section_label, is_originals, glow_color) 
                               VALUES (:name, :slug, :description, :display_order, 1, 0, :icon, :section_label, :is_originals, :glow_color)");
        $stmt->execute([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'display_order' => $display_order,
            'icon' => $icon,
            'section_label' => $section_label ?: $name,
            'is_originals' => $is_originals,
            'glow_color' => $glow_color
        ]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Category "' . $name . '" created successfully!'];
        header('Location: categories.php');
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

      <div class="page-header">
        <h1 class="page-title">Add New Category</h1>
        <p class="page-subtitle">Create a new content category for your slider sections.</p>
      </div>

      <div class="card" style="max-width:700px;">
        <?php if (!empty($errors)): ?>
          <div class="flash-message flash-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars(implode(' ', $errors)); ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="category_add.php" id="categoryForm">
          <?php echo csrfField(); ?>

          <div class="form-group">
            <label class="form-label">Category Name <span style="color:var(--brand-red-bright);">*</span></label>
            <input type="text" name="name" class="form-control-admin" placeholder="e.g. Trending Movies" 
                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required id="catName">
            <div class="form-error" id="nameError">Category name is required.</div>
          </div>

          <div class="form-group">
            <label class="form-label">Slug</label>
            <input type="text" name="slug" class="form-control-admin" placeholder="auto-generated-from-name" 
                   value="<?php echo htmlspecialchars($_POST['slug'] ?? ''); ?>" id="catSlug">
            <div class="form-hint">URL-friendly identifier. Auto-generated if left empty.</div>
          </div>

          <div class="form-group">
            <label class="form-label">Section Label</label>
            <input type="text" name="section_label" class="form-control-admin" placeholder="e.g. Trending Now" 
                   value="<?php echo htmlspecialchars($_POST['section_label'] ?? ''); ?>">
            <div class="form-hint">Small label shown above the section title on the frontend.</div>
          </div>

          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control-admin" placeholder="Optional description..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
              <label class="form-label">Display Order</label>
              <input type="number" name="display_order" class="form-control-admin" value="<?php echo htmlspecialchars($_POST['display_order'] ?? '0'); ?>" min="0">
            </div>
            <div class="form-group">
              <label class="form-label">Icon Class</label>
              <input type="text" name="icon" class="form-control-admin" placeholder="fas fa-film" 
                     value="<?php echo htmlspecialchars($_POST['icon'] ?? 'fas fa-film'); ?>">
            </div>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
              <label class="form-label">Glow Color</label>
              <select name="glow_color" class="form-control-admin">
                <option value="glow-red" <?php echo ($_POST['glow_color'] ?? '') == 'glow-red' ? 'selected' : ''; ?>>Red</option>
                <option value="glow-gold" <?php echo ($_POST['glow_color'] ?? '') == 'glow-gold' ? 'selected' : ''; ?>>Gold</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">&nbsp;</label>
              <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding-top:6px;">
                <input type="checkbox" name="is_originals" value="1" <?php echo !empty($_POST['is_originals']) ? 'checked' : ''; ?> style="accent-color:var(--brand-red-bright);">
                <span style="font-size:0.88rem;">Mark as Originals</span>
              </label>
            </div>
          </div>

          <div style="display:flex;gap:12px;margin-top:12px;">
            <button type="submit" class="btn-admin btn-primary-admin">
              <i class="fas fa-plus"></i> Create Category
            </button>
            <a href="categories.php" class="btn-admin btn-outline-admin">
              <i class="fas fa-arrow-left"></i> Cancel
            </a>
          </div>
        </form>
      </div>

<?php
$extraScripts = <<<'JS'
<script>
$(document).ready(function() {
  // Auto-generate slug from name
  $('#catName').on('keyup', function() {
    var slug = generateSlug($(this).val());
    $('#catSlug').val(slug);
  });

  // Frontend validation
  $('#categoryForm').on('submit', function(e) {
    var valid = true;
    if ($('#catName').val().trim() === '') {
      $('#nameError').addClass('show');
      valid = false;
    } else {
      $('#nameError').removeClass('show');
    }
    if (!valid) e.preventDefault();
  });
});
</script>
JS;
require_once __DIR__ . '/includes/footer.php';
?>
