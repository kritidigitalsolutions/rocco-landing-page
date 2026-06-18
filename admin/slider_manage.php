<?php
/**
 * Rocco Play Admin — Slider Image Manager
 * The most important module — per-category image management
 */
$pageTitle = 'Manage Slider';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireAuth();

$category_id = intval($_GET['category_id'] ?? 0);
if (!$category_id) {
    header('Location: categories.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id");
$stmt->execute(['id' => $category_id]);
$category = $stmt->fetch();

if (!$category) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Category not found.'];
    header('Location: categories.php');
    exit;
}

// Fetch images for this category
$stmt = $pdo->prepare("SELECT * FROM media WHERE category_id = :cid ORDER BY display_order ASC");
$stmt->execute(['cid' => $category_id]);
$images = $stmt->fetchAll();

$breadcrumb = [
    ['label' => 'Categories', 'url' => 'categories.php'],
    ['label' => htmlspecialchars($category['name']), 'url' => 'categories.php'],
    ['label' => 'Manage Slider']
];

$csrf_token = generateCsrfToken();
$currentPage = 'slider_manage';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Slider — <?php echo htmlspecialchars($category['name']); ?> — Rocco Play Admin</title>
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
      <div class="page-header">
        <h1 class="page-title"><?php echo htmlspecialchars($category['name']); ?></h1>
        <p class="page-subtitle">Manage slider images for this category. Drag to reorder, click to edit tags.</p>
        <div class="accent-bar"></div>
      </div>

      <!-- Section A: Upload Panel -->
      <div class="card" style="margin-bottom:24px;">
        <div class="card-header-custom">
          <div class="card-title-custom">
            <i class="fas fa-cloud-arrow-up"></i> Upload Images
          </div>
        </div>

        <div class="upload-zone" id="uploadZone">
          <div class="upload-zone-icon"><i class="fas fa-cloud-arrow-up"></i></div>
          <div class="upload-zone-text">Drag & Drop images here or click to browse</div>
          <div class="upload-zone-hint">Accepted: JPG, JPEG, PNG, WEBP — Max 5MB each</div>
          <input type="file" id="fileInput" multiple accept="image/jpeg,image/jpg,image/png,image/webp" style="display:none;">
        </div>

        <!-- Upload Previews -->
        <div class="upload-preview-grid" id="previewGrid" style="display:none;"></div>

        <div style="margin-top:16px;display:none;" id="uploadActions">
          <button class="btn-admin btn-primary-admin" id="uploadAllBtn">
            <i class="fas fa-upload"></i> Upload All
          </button>
          <button class="btn-admin btn-outline-admin" id="clearAllBtn" style="margin-left:8px;">
            <i class="fas fa-times"></i> Clear
          </button>
        </div>
      </div>

      <!-- Section B: Existing Images Grid -->
      <div class="card">
        <div class="card-header-custom">
          <div class="card-title-custom">
            <i class="fas fa-images"></i> Slider Images (<span id="imageCount"><?php echo count($images); ?></span>)
          </div>
        </div>

        <?php if (count($images) > 0): ?>
        <div class="image-grid" id="imageGrid">
          <?php foreach ($images as $img): ?>
          <div class="image-card" data-id="<?php echo $img['id']; ?>">
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
              <div class="image-card-name" title="<?php echo htmlspecialchars($img['original_name'] ?? $img['filename'] ?? $img['title'] ?? ''); ?>">
                <?php echo htmlspecialchars($img['original_name'] ?? $img['filename'] ?? $img['title'] ?? 'Untitled'); ?>
              </div>
              <input type="text" class="image-card-tag tag-input" data-id="<?php echo $img['id']; ?>" 
                     value="<?php echo htmlspecialchars($img['tag'] ?? $img['badge_text'] ?? ''); ?>" 
                     placeholder="Tag (e.g. NEW, HD)">
              <div class="image-card-actions">
                <input type="number" class="image-card-order order-input" data-id="<?php echo $img['id']; ?>" 
                       value="<?php echo $img['display_order']; ?>" min="0" title="Display Order">
                <label class="toggle-switch" style="flex-shrink:0;">
                  <input type="checkbox" class="active-toggle" data-id="<?php echo $img['id']; ?>" 
                         <?php echo $img['is_active'] ? 'checked' : ''; ?>>
                  <span class="toggle-slider"></span>
                </label>
                <button class="btn-admin btn-sm-admin btn-info-admin edit-image" 
                        data-id="<?php echo $img['id']; ?>" 
                        data-title="<?php echo htmlspecialchars($img['title'] ?? ''); ?>"
                        data-rating="<?php echo htmlspecialchars($img['rating'] ?? ''); ?>"
                        data-year="<?php echo htmlspecialchars($img['year_or_seasons'] ?? ''); ?>"
                        data-genre="<?php echo htmlspecialchars($img['genre'] ?? ''); ?>"
                        title="Edit Metadata">
                  <i class="fas fa-pen"></i>
                </button>
                <button class="btn-admin btn-sm-admin btn-danger-admin delete-image" 
                        data-id="<?php echo $img['id']; ?>" title="Delete">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state" id="emptyState">
          <div class="empty-state-icon"><i class="fas fa-image"></i></div>
          <div class="empty-state-text">No images in this category yet</div>
          <div class="empty-state-hint">Upload images above to create your slider.</div>
        </div>
        <?php endif; ?>
      </div>

      <!-- Edit Metadata Modal -->
      <div class="modal fade modal-admin" id="editImageModal" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><i class="fas fa-edit" style="color:var(--brand-gold);margin-right:8px;"></i> Edit Image Metadata</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <form id="editImageForm" enctype="multipart/form-data">
                <input type="hidden" id="edit_image_id" name="id">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group" style="text-align:center; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--glass-border);">
                  <img id="editImageUrl" src="" style="max-height: 120px; border-radius: 8px; margin-bottom: 15px; border: 1px solid var(--glass-border); display: block; margin: 0 auto 10px auto; object-fit: contain;">
                  
                  <div style="display: flex; justify-content: center; gap: 10px;">
                    <label for="edit_image_file" class="btn-admin btn-sm-admin btn-outline-admin" style="cursor:pointer; margin-bottom:0;">
                      <i class="fas fa-upload"></i> Upload New Image
                    </label>
                    <button type="button" class="btn-admin btn-sm-admin btn-danger-admin" id="removeImageBtn" style="margin-bottom:0;">
                      <i class="fas fa-trash"></i> Remove Image
                    </button>
                  </div>
                  <input type="file" id="edit_image_file" name="image" accept="image/jpeg,image/jpg,image/png,image/webp" style="display:none;" onchange="if(this.files[0]) { document.getElementById('editImageUrl').src = window.URL.createObjectURL(this.files[0]); document.getElementById('remove_image_flag').value = '0'; }">
                  <input type="hidden" id="remove_image_flag" name="remove_image" value="0">
                  <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 8px;">Leave empty to keep the current image.</div>
                </div>

                <div class="form-group">
                  <label class="form-label">Title</label>
                  <input type="text" class="form-control-admin" id="edit_title" name="title" placeholder="Movie or Show Title">
                </div>
                
                <div class="form-group">
                  <label class="form-label">Rating</label>
                  <input type="number" step="0.1" max="10" min="0" class="form-control-admin" id="edit_rating" name="rating" placeholder="e.g. 8.5">
                </div>
                
                <div class="form-group">
                  <label class="form-label">Year / Seasons</label>
                  <input type="text" class="form-control-admin" id="edit_year" name="year_or_seasons" placeholder="e.g. 2024 or 3 Seasons">
                </div>
                
                <div class="form-group">
                  <label class="form-label">Genre</label>
                  <input type="text" class="form-control-admin" id="edit_genre" name="genre" placeholder="e.g. Action, Drama">
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn-admin btn-outline-admin" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn-admin btn-primary-admin" id="saveImageMetadataBtn">
                <i class="fas fa-save"></i> Save Changes
              </button>
            </div>
          </div>
        </div>
      </div>

<?php
$extraScripts = <<<JSBLOCK
<script>
$(document).ready(function() {
  var categoryId = {$category_id};
  var csrfToken = '{$csrf_token}';
  var selectedFiles = [];

  // ==================== Upload Zone ====================
  var \$zone = $('#uploadZone');
  var \$fileInput = $('#fileInput');
  var \$previewGrid = $('#previewGrid');
  var \$uploadActions = $('#uploadActions');

  \$zone.on('click', function(e) { 
    if (e.target.id !== 'fileInput') {
      \$fileInput[0].click(); 
    }
  });

  \$zone.on('dragover', function(e) {
    e.preventDefault();
    $(this).addClass('dragover');
  }).on('dragleave drop', function(e) {
    e.preventDefault();
    $(this).removeClass('dragover');
  });

  \$zone.on('drop', function(e) {
    var files = e.originalEvent.dataTransfer.files;
    handleFiles(files);
  });

  \$fileInput.on('change', function() {
    handleFiles(this.files);
  });

  function handleFiles(files) {
    var maxSize = 5 * 1024 * 1024; // 5MB
    var allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

    for (var i = 0; i < files.length; i++) {
      var file = files[i];
      if (!allowed.includes(file.type)) {
        toastr.error(file.name + ' is not an accepted image type.');
        continue;
      }
      if (file.size > maxSize) {
        toastr.error(file.name + ' exceeds 5MB limit.');
        continue;
      }
      selectedFiles.push(file);
      addPreview(file, selectedFiles.length - 1);
    }

    if (selectedFiles.length > 0) {
      \$previewGrid.show();
      \$uploadActions.show();
    }
  }

  function addPreview(file, index) {
    var reader = new FileReader();
    reader.onload = function(e) {
      var html = '<div class="upload-preview-item" data-index="' + index + '">' +
                 '<img src="' + e.target.result + '" alt="Preview">' +
                 '<div class="progress-bar-wrap"><div class="progress-bar-fill" id="prog_' + index + '"></div></div>' +
                 '<button class="remove-preview" data-index="' + index + '"><i class="fas fa-times"></i></button>' +
                 '</div>';
      \$previewGrid.append(html);
    };
    reader.readAsDataURL(file);
  }

  // Remove individual preview
  $(document).on('click', '.remove-preview', function() {
    var idx = $(this).data('index');
    selectedFiles[idx] = null;
    $(this).closest('.upload-preview-item').fadeOut(200, function() { $(this).remove(); });
    // Check if any files left
    if (selectedFiles.filter(Boolean).length === 0) {
      \$previewGrid.hide();
      \$uploadActions.hide();
    }
  });

  // Clear all
  $('#clearAllBtn').on('click', function() {
    selectedFiles = [];
    \$previewGrid.html('').hide();
    \$uploadActions.hide();
    \$fileInput.val('');
  });

  // Upload all files
  $('#uploadAllBtn').on('click', function() {
    var filesToUpload = selectedFiles.filter(Boolean);
    if (filesToUpload.length === 0) return;

    var uploadCount = 0;
    var totalFiles = filesToUpload.length;

    filesToUpload.forEach(function(file, i) {
      var realIndex = selectedFiles.indexOf(file);
      var formData = new FormData();
      formData.append('image', file);
      formData.append('category_id', categoryId);
      formData.append('csrf_token', csrfToken);

      $.ajax({
        url: 'media_upload.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        xhr: function() {
          var xhr = new XMLHttpRequest();
          xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
              var pct = Math.round((e.loaded / e.total) * 100);
              $('#prog_' + realIndex).css('width', pct + '%');
            }
          });
          return xhr;
        },
        success: function(res) {
          uploadCount++;
          if (res.success) {
            $('#prog_' + realIndex).css({'width':'100%','background':'#2ecc71'});
            // Add new image card to grid
            appendImageCard(res.data);
          } else {
            toastr.error(res.message || 'Upload failed for ' + file.name);
          }
          if (uploadCount === totalFiles) {
            toastr.success(totalFiles + ' image(s) uploaded successfully!');
            setTimeout(function() {
              selectedFiles = [];
              \$previewGrid.html('').hide();
              \$uploadActions.hide();
              \$fileInput.val('');
            }, 1000);
          }
        },
        error: function() {
          uploadCount++;
          toastr.error('Server error uploading ' + file.name);
        }
      });
    });
  });

    function appendImageCard(data) {
      // Remove empty state if present
      $('#emptyState').remove();

      var imgSrc = data.file_path;
      if (imgSrc.startsWith('admin/')) imgSrc = imgSrc.substring(6);

      var html = '<div class="image-card" data-id="' + data.id + '" style="animation:fadeInUp 0.3s ease;">' +
        '<img src="' + imgSrc + '" class="image-card-thumb" loading="lazy">' +
        '<div class="image-card-body">' +
        '<div class="image-card-name">' + (data.original_name || 'Image') + '</div>' +
        '<input type="text" class="image-card-tag tag-input" data-id="' + data.id + '" placeholder="Tag (e.g. NEW, HD)">' +
        '<div class="image-card-actions">' +
          '<input type="number" class="image-card-order order-input" data-id="' + data.id + '" value="' + (data.display_order || 0) + '" min="0">' +
          '<label class="toggle-switch" style="flex-shrink:0;">' +
            '<input type="checkbox" class="active-toggle" data-id="' + data.id + '" checked>' +
            '<span class="toggle-slider"></span>' +
          '</label>' +
          '<button class="btn-admin btn-sm-admin btn-danger-admin delete-image" data-id="' + data.id + '"><i class="fas fa-trash"></i></button>' +
        '</div>' +
      '</div></div>';

    var \$grid = $('#imageGrid');
    if (\$grid.length === 0) {
      // Create grid if doesn't exist
      $('.card').last().find('.card-header-custom').after('<div class="image-grid" id="imageGrid"></div>');
      \$grid = $('#imageGrid');
    }
    \$grid.append(html);
    $('#imageCount').text(\$grid.find('.image-card').length);
  }

  // ==================== SortableJS on Image Grid ====================
  var grid = document.getElementById('imageGrid');
  if (grid) {
    new Sortable(grid, {
      animation: 200,
      ghostClass: 'sortable-ghost',
      dragClass: 'sortable-drag',
      onEnd: function() {
        var ids = [];
        $('#imageGrid .image-card').each(function() {
          ids.push($(this).data('id'));
        });
        adminAjax('media_reorder.php', { ids: ids });
      }
    });
  }

  // ==================== Tag Update (on blur) ====================
  $(document).on('blur', '.tag-input', function() {
    var id = $(this).data('id');
    var tag = $(this).val().trim();
    adminAjax('tag_update.php', { id: id, tag: tag });
  });

  // ==================== Active Toggle ====================
  $(document).on('change', '.active-toggle', function() {
    var id = $(this).data('id');
    var isActive = $(this).is(':checked') ? 1 : 0;
    adminAjax('tag_update.php', { id: id, is_active: isActive, action: 'toggle_active' });
  });

  // ==================== Delete Image ====================
  $(document).on('click', '.delete-image', function() {
    var id = $(this).data('id');
    var \$card = $(this).closest('.image-card');
    confirmDelete('This image will be permanently deleted from the server.', function() {
      adminAjax('media_delete.php', { id: id }, function() {
        \$card.fadeOut(300, function() {
          $(this).remove();
          $('#imageCount').text($('#imageGrid .image-card').length);
          if ($('#imageGrid .image-card').length === 0) {
            $('#imageGrid').after('<div class="empty-state" id="emptyState"><div class="empty-state-icon"><i class="fas fa-image"></i></div><div class="empty-state-text">No images in this category yet</div></div>');
            $('#imageGrid').remove();
          }
        });
      });
    });
  });

  // ==================== Edit Metadata ====================
  $(document).on('click', '.edit-image', function(e) {
    e.stopPropagation();
    var btn = $(this);
    var imgSrc = btn.closest('.image-card').find('.image-card-thumb').attr('src');
    $('#editImageUrl').attr('src', imgSrc).show();
    $('#edit_image_file').val('');
    $('#remove_image_flag').val('0');
    $('#edit_image_id').val(btn.data('id'));
    $('#edit_title').val(btn.data('title'));
    $('#edit_rating').val(btn.data('rating'));
    $('#edit_year').val(btn.data('year'));
    $('#edit_genre').val(btn.data('genre'));
    $('#editImageModal').modal('show');
  });

  $('#removeImageBtn').on('click', function() {
    $('#editImageUrl').hide();
    $('#edit_image_file').val('');
    $('#remove_image_flag').val('1');
  });

  $('#saveImageMetadataBtn').on('click', function() {
    var form = document.getElementById('editImageForm');
    var formData = new FormData(form);
    showSpinner();
    $.ajax({
      url: 'media_update_meta.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(res) {
        hideSpinner();
        if (typeof res === 'string') {
          try { res = JSON.parse(res); } catch(e) {}
        }
        if(res.success) {
          $('#editImageModal').modal('hide');
          toastr.success(res.message || 'Updated successfully');
          
          var id = $('#edit_image_id').val();
          var btn = $('.edit-image[data-id="'+id+'"]');
          var newTitle = $('#edit_title').val();
          btn.data('title', newTitle);
          btn.data('rating', $('#edit_rating').val());
          btn.data('year', $('#edit_year').val());
          btn.data('genre', $('#edit_genre').val());
          
          var cardEl = btn.closest('.image-card');
          if(newTitle) {
              cardEl.find('.image-card-name').text(newTitle).attr('title', newTitle);
          }
          if(res.new_image_url) {
              cardEl.find('.image-card-thumb').attr('src', res.new_image_url);
          }
        } else {
          toastr.error(res.message || 'Error updating record');
        }
      },
      error: function() { hideSpinner(); toastr.error('Server error'); }
    });
  });

});
</script>
JSBLOCK;

require_once __DIR__ . '/includes/footer.php';
?>
