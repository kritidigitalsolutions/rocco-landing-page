<?php
/**
 * Rocco Play Admin — Media Library
 * Images grouped by category with accordion, bulk select, and category filter
 */
$pageTitle = 'Media Library';
$breadcrumb = [['label' => 'Media Library']];

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireAuth();

// Fetch categories with their images
$categories = $pdo->query("SELECT * FROM categories ORDER BY display_order ASC")->fetchAll();
$mediaByCategory = [];
$totalImages = 0;

foreach ($categories as $cat) {
    $stmt = $pdo->prepare("SELECT * FROM media WHERE category_id = :cid ORDER BY display_order ASC");
    $stmt->execute(['cid' => $cat['id']]);
    $images = $stmt->fetchAll();
    $mediaByCategory[$cat['id']] = $images;
    $totalImages += count($images);
}

$csrf_token = generateCsrfToken();
$currentPage = 'media_library';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Media Library — Rocco Play Admin</title>
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

      <!-- Page Header -->
      <div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
        <div>
          <h1 class="page-title">Media Library</h1>
          <p class="page-subtitle"><?php echo $totalImages; ?> images across <?php echo count($categories); ?> categories</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
          <button class="btn-admin btn-outline-admin" id="bulkModeBtn">
            <i class="fas fa-check-double"></i> Bulk Select
          </button>
          <button class="btn-admin btn-danger-admin" id="bulkDeleteBtn" style="display:none;">
            <i class="fas fa-trash"></i> Delete Selected (<span id="selectedCount">0</span>)
          </button>
          <button class="btn-admin btn-primary-admin" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="fas fa-upload"></i> Upload Images
          </button>
        </div>
      </div>

      <!-- Category Filter -->
      <div class="card" style="margin-bottom:20px;padding:16px 20px;">
        <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
          <label style="font-size:0.85rem;font-weight:600;color:var(--text-secondary);white-space:nowrap;">
            <i class="fas fa-filter" style="color:var(--brand-gold);margin-right:6px;"></i> Filter by Category:
          </label>
          <select class="form-control-admin" id="categoryFilter" style="max-width:300px;">
            <option value="all">All Categories</option>
            <?php foreach ($categories as $cat): ?>
              <option value="cat_<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?> (<?php echo count($mediaByCategory[$cat['id']]); ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Accordion Sections -->
      <?php foreach ($categories as $cat): ?>
      <div class="accordion-section open" id="cat_<?php echo $cat['id']; ?>">
        <div class="accordion-header" onclick="toggleAccordion(this)">
          <div class="accordion-header-left">
            <i class="<?php echo htmlspecialchars($cat['icon'] ?? 'fas fa-folder'); ?>" style="color:var(--brand-gold);"></i>
            <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
            <span class="count-badge"><?php echo count($mediaByCategory[$cat['id']]); ?> images</span>
          </div>
          <i class="fas fa-chevron-down chevron"></i>
        </div>
        <div class="accordion-body">
          <div class="accordion-body-inner">
            <?php if (count($mediaByCategory[$cat['id']]) > 0): ?>
            <div class="image-grid">
              <?php foreach ($mediaByCategory[$cat['id']] as $img): ?>
              <div class="image-card" data-id="<?php echo $img['id']; ?>">
                <input type="checkbox" class="image-card-checkbox bulk-check" data-id="<?php echo $img['id']; ?>">
                <?php 
                   $imgSrc = $img['file_path'] ?: $img['img_path']; 
                   if (str_starts_with($imgSrc, 'admin/')) {
                       $imgSrc = substr($imgSrc, 6); // use relative 'uploads/...'
                   } elseif (!str_starts_with($imgSrc, 'http') && !str_starts_with($imgSrc, '/')) {
                       $imgSrc = '../' . $imgSrc;
                   }
                ?>
                <img src="<?php echo htmlspecialchars($imgSrc); ?>" 
                     alt="<?php echo htmlspecialchars($img['original_name'] ?? $img['title'] ?? ''); ?>" 
                     class="image-card-thumb" loading="lazy">
                <div class="image-card-body">
                  <div class="image-card-name"><?php echo htmlspecialchars($img['original_name'] ?? $img['filename'] ?? $img['title'] ?? 'Untitled'); ?></div>
                  <div class="image-card-meta">
                    <span><i class="fas fa-weight-hanging"></i> <?php echo $img['file_size'] ? round($img['file_size']/1024) . ' KB' : 'N/A'; ?></span>
                    <span><i class="fas fa-clock"></i> <?php echo date('M d', strtotime($img['uploaded_at'])); ?></span>
                    <?php if ($img['tag'] || $img['badge_text']): ?>
                      <span class="badge-admin badge-tag"><?php echo htmlspecialchars($img['tag'] ?: $img['badge_text']); ?></span>
                    <?php endif; ?>
                  </div>
                  <div class="image-card-actions">
                    <a href="slider_manage.php?category_id=<?php echo $cat['id']; ?>" class="btn-admin btn-sm-admin btn-info-admin" title="Manage">
                      <i class="fas fa-pen"></i>
                    </a>
                    <button class="btn-admin btn-sm-admin btn-danger-admin delete-image" data-id="<?php echo $img['id']; ?>" title="Delete">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state" style="padding:30px;">
              <div class="empty-state-icon" style="font-size:2rem;"><i class="fas fa-image"></i></div>
              <div class="empty-state-hint">No images in this category</div>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>

      <?php if (count($categories) === 0): ?>
      <div class="card">
        <div class="empty-state">
          <div class="empty-state-icon"><i class="fas fa-layer-group"></i></div>
          <div class="empty-state-text">No categories created yet</div>
          <div class="empty-state-hint">Create a category first, then upload images.</div>
          <a href="category_add.php" class="btn-admin btn-primary-admin" style="margin-top:16px;">
            <i class="fas fa-plus"></i> Add Category
          </a>
        </div>
      </div>
      <?php endif; ?>

  <!-- Upload Modal -->
  <div class="modal fade modal-admin" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-upload" style="color:var(--brand-gold);margin-right:8px;"></i> Upload Images</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label">Select Category</label>
            <select class="form-control-admin" id="modalCategorySelect">
              <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="upload-zone" id="modalUploadZone">
            <div class="upload-zone-icon"><i class="fas fa-cloud-arrow-up"></i></div>
            <div class="upload-zone-text">Drag & Drop images here or click to browse</div>
            <div class="upload-zone-hint">Accepted: JPG, JPEG, PNG, WEBP — Max 5MB each</div>
            <input type="file" id="modalFileInput" multiple accept="image/jpeg,image/jpg,image/png,image/webp" style="display:none;">
          </div>
          <div class="upload-preview-grid" id="modalPreviewGrid" style="margin-top:12px;"></div>
        </div>
        <div class="modal-footer">
          <button class="btn-admin btn-outline-admin" data-bs-dismiss="modal">Cancel</button>
          <button class="btn-admin btn-primary-admin" id="modalUploadBtn">
            <i class="fas fa-upload"></i> Upload All
          </button>
        </div>
      </div>
    </div>
  </div>

<?php
$extraScripts = <<<'JS'
<script>
function toggleAccordion(header) {
  $(header).closest('.accordion-section').toggleClass('open');
}

$(document).ready(function() {

  // ===== Category Filter =====
  $('#categoryFilter').on('change', function() {
    var val = $(this).val();
    if (val === 'all') {
      $('.accordion-section').show();
    } else {
      $('.accordion-section').hide();
      $('#' + val).show().addClass('open');
    }
  });

  // ===== Bulk Mode =====
  var bulkMode = false;
  $('#bulkModeBtn').on('click', function() {
    bulkMode = !bulkMode;
    if (bulkMode) {
      $(this).html('<i class="fas fa-times"></i> Cancel Selection');
      $('.admin-wrapper').addClass('bulk-mode');
    } else {
      $(this).html('<i class="fas fa-check-double"></i> Bulk Select');
      $('.admin-wrapper').removeClass('bulk-mode');
      $('.bulk-check').prop('checked', false);
      $('#bulkDeleteBtn').hide();
      $('#selectedCount').text('0');
    }
  });

  $(document).on('change', '.bulk-check', function() {
    var count = $('.bulk-check:checked').length;
    $('#selectedCount').text(count);
    $('#bulkDeleteBtn').toggle(count > 0);
  });

  $('#bulkDeleteBtn').on('click', function() {
    var ids = [];
    $('.bulk-check:checked').each(function() { ids.push($(this).data('id')); });
    if (ids.length === 0) return;
    
    confirmDelete('Delete ' + ids.length + ' selected images permanently?', function() {
      ids.forEach(function(id) {
        adminAjax('media_delete.php', { id: id }, function() {
          $('[data-id="' + id + '"].image-card').fadeOut(200, function() { $(this).remove(); });
        });
      });
    });
  });

  // ===== Delete Single Image =====
  $(document).on('click', '.delete-image', function(e) {
    e.stopPropagation();
    var id = $(this).data('id');
    var $card = $(this).closest('.image-card');
    confirmDelete('This image will be permanently deleted.', function() {
      adminAjax('media_delete.php', { id: id }, function() {
        $card.fadeOut(300, function() { $(this).remove(); });
      });
    });
  });

  // ===== Modal Upload =====
  var modalFiles = [];
  var $mZone = $('#modalUploadZone');
  var $mInput = $('#modalFileInput');
  var $mGrid = $('#modalPreviewGrid');

  $mZone.on('click', function(e) { 
    if (e.target.id !== 'modalFileInput') {
      $mInput[0].click();
    }
  });
  $mZone.on('dragover', function(e) { e.preventDefault(); $(this).addClass('dragover'); });
  $mZone.on('dragleave drop', function(e) { e.preventDefault(); $(this).removeClass('dragover'); });
  $mZone.on('drop', function(e) { handleModalFiles(e.originalEvent.dataTransfer.files); });
  $mInput.on('change', function() { handleModalFiles(this.files); });

  function handleModalFiles(files) {
    for (var i = 0; i < files.length; i++) {
      var f = files[i];
      if (f.size > 5*1024*1024) { toastr.error(f.name + ' exceeds 5MB.'); continue; }
      modalFiles.push(f);
      var reader = new FileReader();
      reader.onload = (function(idx) {
        return function(e) {
          $mGrid.append('<div class="upload-preview-item"><img src="'+e.target.result+'" alt="Preview"><div class="progress-bar-wrap"><div class="progress-bar-fill" id="mprog_'+idx+'"></div></div></div>');
        };
      })(modalFiles.length - 1);
      reader.readAsDataURL(f);
    }
  }

  $('#modalUploadBtn').on('click', function() {
    var catId = $('#modalCategorySelect').val();
    if (!catId) { toastr.error('Select a category.'); return; }
    var filesToUpload = modalFiles.filter(Boolean);
    if (filesToUpload.length === 0) { toastr.error('No files selected.'); return; }

    showSpinner();
    var done = 0;
    filesToUpload.forEach(function(f, i) {
      var fd = new FormData();
      fd.append('image', f);
      fd.append('category_id', catId);
      fd.append('csrf_token', window.csrfToken);
      $.ajax({ url:'media_upload.php', type:'POST', data:fd, processData:false, contentType:false,
        xhr: function() {
          var xhr = new XMLHttpRequest();
          xhr.upload.addEventListener('progress', function(e) {
            if(e.lengthComputable) $('#mprog_'+i).css('width', Math.round(e.loaded/e.total*100)+'%');
          });
          return xhr;
        },
        success: function(res) {
          done++;
          if(done===filesToUpload.length) {
            hideSpinner();
            toastr.success(done + ' images uploaded!');
            setTimeout(function() { location.reload(); }, 1000);
          }
        },
        error: function() { done++; if(done===filesToUpload.length) { hideSpinner(); location.reload(); } }
      });
    });
  });

  // Reset modal on close
  $('#uploadModal').on('hidden.bs.modal', function() {
    modalFiles = [];
    $mGrid.html('');
    $mInput.val('');
  });

});
</script>
JS;
require_once __DIR__ . '/includes/footer.php';
?>
