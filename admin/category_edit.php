<?php
/**
 * Rocco Play Admin — Edit Category (Fixed)
 */
$pageTitle = 'Edit Category';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireAuth();

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: categories.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id");
$stmt->execute(['id' => $id]);
$category = $stmt->fetch();

if (!$category) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Category not found.'];
    header('Location: categories.php');
    exit;
}

$errors = [];
$name = $category['name'];
$slug = $category['slug'];
$description = $category['description'];
$display_order = $category['display_order'];
$icon = $category['icon'];
$section_label = $category['section_label'];
$is_originals = $category['is_originals'];
$glow_color = $category['glow_color'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        $stmt = $pdo->prepare("UPDATE categories SET name=:name, slug=:slug, description=:description, 
                               display_order=:display_order, icon=:icon, section_label=:section_label, 
                               is_originals=:is_originals, glow_color=:glow_color WHERE id=:id");
        $stmt->execute([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'display_order' => $display_order,
            'icon' => $icon,
            'section_label' => $section_label ?: $name,
            'is_originals' => $is_originals,
            'glow_color' => $glow_color,
            'id' => $id
        ]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Category "' . $name . '" updated successfully!'];
        header('Location: categories.php');
        exit;
    }
}

$breadcrumb = [
    ['label' => 'Categories', 'url' => 'categories.php'],
    ['label' => 'Edit: ' . $category['name']]
];

$currentPage = 'category_edit';
$csrf_token = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Category — Rocco Play Admin</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="assets/css/admin.css">
  <link rel="icon" type="image/jpeg" href="../img/logo.jpg">
</head>
<body>
  <div class="admin-wrapper" id="adminWrapper">

<?php require_once __DIR__ . '/includes/sidebar.php'; ?>

      <div class="page-header">
        <h1 class="page-title">Edit Category</h1>
        <p class="page-subtitle">Update "<?php echo htmlspecialchars($category['name']); ?>"</p>
        <div class="accent-bar"></div>
      </div>

      <div class="card" style="max-width:700px;">
        <?php if (!empty($errors)): ?>
          <div class="flash-message flash-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars(implode(' ', $errors)); ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="category_edit.php?id=<?php echo $id; ?>">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

          <div class="form-group">
            <label class="form-label">Category Name <span style="color:var(--brand-red-bright);">*</span></label>
            <input type="text" name="name" class="form-control-admin" value="<?php echo htmlspecialchars($name); ?>" required id="catName">
          </div>

          <div class="form-group">
            <label class="form-label">Slug</label>
            <input type="text" name="slug" class="form-control-admin" value="<?php echo htmlspecialchars($slug); ?>" id="catSlug">
            <div class="form-hint">URL-friendly identifier.</div>
          </div>

          <div class="form-group">
            <label class="form-label">Section Label</label>
            <input type="text" name="section_label" class="form-control-admin" value="<?php echo htmlspecialchars($section_label); ?>">
            <div class="form-hint">Small label shown above the section title on the frontend.</div>
          </div>

          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control-admin"><?php echo htmlspecialchars($description); ?></textarea>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
              <label class="form-label">Display Order</label>
              <input type="number" name="display_order" class="form-control-admin" value="<?php echo intval($display_order); ?>" min="0">
            </div>
            <div class="form-group">
              <label class="form-label">Icon Class</label>
              <input type="text" name="icon" class="form-control-admin" value="<?php echo htmlspecialchars($icon); ?>">
            </div>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
              <label class="form-label">Glow Color</label>
              <select name="glow_color" class="form-control-admin">
                <option value="glow-red" <?php echo $glow_color == 'glow-red' ? 'selected' : ''; ?>>Red</option>
                <option value="glow-gold" <?php echo $glow_color == 'glow-gold' ? 'selected' : ''; ?>>Gold</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">&nbsp;</label>
              <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding-top:6px;">
                <input type="checkbox" name="is_originals" value="1" <?php echo $is_originals ? 'checked' : ''; ?> style="accent-color:var(--brand-red-bright);">
                <span style="font-size:0.88rem;">Mark as Originals</span>
              </label>
            </div>
          </div>

          <div style="display:flex;gap:12px;margin-top:12px;">
            <button type="submit" class="btn-admin btn-primary-admin">
              <i class="fas fa-save"></i> Update Category
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
  $('#catName').on('keyup', function() {
    $('#catSlug').val(generateSlug($(this).val()));
  });
});
</script>
JS;
require_once __DIR__ . '/includes/footer.php';
?>
