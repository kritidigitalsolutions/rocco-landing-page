<?php
$pageTitle = 'Hero Slider';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/hero_slides_schema.php';

ensureHeroSlidesTable($pdo);

$slides = $pdo->query("SELECT * FROM hero_slides ORDER BY display_order ASC, id ASC")->fetchAll();
$breadcrumb = [
    ['label' => 'Hero Slider']
];

require_once __DIR__ . '/includes/sidebar.php';
?>

      <div class="page-header">
        <h1 class="page-title">Hero Slider</h1>
        <p class="page-subtitle">Manage homepage hero images, heading, paragraph, and badge text.</p>
        <div class="accent-bar"></div>
      </div>

      <div class="card" style="margin-bottom:24px;">
        <div class="card-header-custom">
          <div class="card-title-custom">
            <i class="fas fa-plus-circle"></i> Add Hero Slide
          </div>
        </div>

        <form id="heroSlideForm" enctype="multipart/form-data">
          <?php echo csrfField(); ?>
          <input type="hidden" name="id" id="hero_slide_id" value="">
          <input type="hidden" name="existing_image_path" id="existing_image_path" value="">

          <div class="row">
            <div class="col-lg-6">
              <div class="form-group">
                <label class="form-label">Hero Image</label>
                <input type="file" class="form-control-admin" name="image" id="hero_image" accept="image/jpeg,image/jpg,image/png,image/webp">
                <div style="font-size:0.82rem;color:var(--text-muted);margin-top:6px;">JPG, PNG, WEBP up to 5MB. Required for new slides.</div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group">
                <label class="form-label">Badge Text</label>
                <input type="text" class="form-control-admin" name="badge_text" id="badge_text" placeholder="Streaming Now - New Releases Every Week">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-6">
              <div class="form-group">
                <label class="form-label">Badge Icon Class</label>
                <input type="text" class="form-control-admin" name="badge_icon" id="badge_icon" value="fas fa-satellite-dish" placeholder="fas fa-satellite-dish">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group">
                <label class="form-label">Display Order</label>
                <input type="number" class="form-control-admin" name="display_order" id="display_order" min="0" value="<?php echo count($slides) + 1; ?>">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Heading</label>
            <input type="text" class="form-control-admin" name="heading" id="heading" required placeholder="Unlimited Movies, Shows & Originals">
          </div>

          <div class="form-group">
            <label class="form-label">Paragraph</label>
            <textarea class="form-control-admin" name="paragraph" id="paragraph" rows="3" placeholder="Hero paragraph text"></textarea>
          </div>

          <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <button type="submit" class="btn-admin btn-primary-admin" id="saveHeroSlideBtn">
              <i class="fas fa-save"></i> Save Slide
            </button>
            <button type="button" class="btn-admin btn-outline-admin" id="resetHeroFormBtn">
              <i class="fas fa-rotate-left"></i> Reset
            </button>
          </div>
        </form>
      </div>

      <div class="card">
        <div class="card-header-custom">
          <div class="card-title-custom">
            <i class="fas fa-images"></i> Hero Slides (<span id="heroSlideCount"><?php echo count($slides); ?></span>)
          </div>
        </div>

        <?php if (count($slides) > 0): ?>
        <div class="image-grid" id="heroSlidesGrid">
          <?php foreach ($slides as $slide): ?>
          <?php
            $imgSrc = $slide['image_path'];
            $adminImgSrc = $imgSrc;
            if (str_starts_with($adminImgSrc, 'admin/')) {
                $adminImgSrc = substr($adminImgSrc, 6);
            } elseif (!str_starts_with($adminImgSrc, 'http') && !str_starts_with($adminImgSrc, '/')) {
                $adminImgSrc = '../' . $adminImgSrc;
            }
          ?>
          <div class="image-card hero-slide-card" data-id="<?php echo (int) $slide['id']; ?>">
            <img src="<?php echo htmlspecialchars($adminImgSrc); ?>" alt="<?php echo htmlspecialchars($slide['heading']); ?>" class="image-card-thumb" loading="lazy">
            <div class="image-card-body">
              <div class="image-card-name" title="<?php echo htmlspecialchars($slide['heading']); ?>"><?php echo htmlspecialchars($slide['heading']); ?></div>
              <div class="image-card-tag" style="min-height:auto;"><?php echo htmlspecialchars($slide['badge_text'] ?? ''); ?></div>
              <div class="image-card-actions">
                <input type="number" class="image-card-order hero-order-input" data-id="<?php echo (int) $slide['id']; ?>" value="<?php echo (int) $slide['display_order']; ?>" min="0" title="Display Order">
                <label class="toggle-switch" style="flex-shrink:0;">
                  <input type="checkbox" class="hero-active-toggle" data-id="<?php echo (int) $slide['id']; ?>" <?php echo $slide['is_active'] ? 'checked' : ''; ?>>
                  <span class="toggle-slider"></span>
                </label>
                <button class="btn-admin btn-sm-admin btn-info-admin edit-hero-slide"
                        data-id="<?php echo (int) $slide['id']; ?>"
                        data-image="<?php echo htmlspecialchars($slide['image_path'], ENT_QUOTES); ?>"
                        data-badge="<?php echo htmlspecialchars($slide['badge_text'] ?? '', ENT_QUOTES); ?>"
                        data-icon="<?php echo htmlspecialchars($slide['badge_icon'] ?? 'fas fa-satellite-dish', ENT_QUOTES); ?>"
                        data-heading="<?php echo htmlspecialchars($slide['heading'], ENT_QUOTES); ?>"
                        data-paragraph="<?php echo htmlspecialchars($slide['paragraph'] ?? '', ENT_QUOTES); ?>"
                        data-order="<?php echo (int) $slide['display_order']; ?>">
                  <i class="fas fa-pen"></i>
                </button>
                <button class="btn-admin btn-sm-admin btn-danger-admin delete-hero-slide" data-id="<?php echo (int) $slide['id']; ?>">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state" id="emptyHeroSlides">
          <div class="empty-state-icon"><i class="fas fa-image"></i></div>
          <div class="empty-state-text">No hero slides yet</div>
          <div class="empty-state-hint">Add your first hero slide above.</div>
        </div>
        <?php endif; ?>
      </div>

<?php
$nextOrder = count($slides) + 1;
$extraScripts = <<<JSBLOCK
<script>
$(document).ready(function() {
  var nextOrder = {$nextOrder};

  function resetHeroForm() {
    $('#heroSlideForm')[0].reset();
    $('#hero_slide_id').val('');
    $('#existing_image_path').val('');
    $('#badge_icon').val('fas fa-satellite-dish');
    $('#display_order').val(nextOrder);
    $('#saveHeroSlideBtn').html('<i class="fas fa-save"></i> Save Slide');
  }

  $('#resetHeroFormBtn').on('click', resetHeroForm);

  $('#heroSlideForm').on('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);

    showSpinner();
    $.ajax({
      url: 'hero_slide_save.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function(res) {
        hideSpinner();
        if (res.success) {
          toastr.success(res.message || 'Hero slide saved.');
          window.location.reload();
        } else {
          toastr.error(res.message || 'Unable to save hero slide.');
        }
      },
      error: function() {
        hideSpinner();
        toastr.error('Server error.');
      }
    });
  });

  $(document).on('click', '.edit-hero-slide', function() {
    var btn = $(this);
    $('#hero_slide_id').val(btn.data('id'));
    $('#existing_image_path').val(btn.data('image'));
    $('#badge_text').val(btn.data('badge'));
    $('#badge_icon').val(btn.data('icon'));
    $('#heading').val(btn.data('heading'));
    $('#paragraph').val(btn.data('paragraph'));
    $('#display_order').val(btn.data('order'));
    $('#hero_image').val('');
    $('#saveHeroSlideBtn').html('<i class="fas fa-save"></i> Update Slide');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  $(document).on('change', '.hero-active-toggle', function() {
    adminAjax('hero_slide_save.php', {
      action: 'toggle_active',
      id: $(this).data('id'),
      is_active: $(this).is(':checked') ? 1 : 0
    });
  });

  $(document).on('change', '.hero-order-input', function() {
    adminAjax('hero_slide_save.php', {
      action: 'update_order',
      id: $(this).data('id'),
      display_order: $(this).val()
    });
  });

  $(document).on('click', '.delete-hero-slide', function() {
    var id = $(this).data('id');
    confirmDelete('This hero slide will be removed from the homepage.', function() {
      adminAjax('hero_slide_delete.php', { id: id }, function() {
        window.location.reload();
      });
    });
  });

  var grid = document.getElementById('heroSlidesGrid');
  if (grid) {
    new Sortable(grid, {
      animation: 200,
      ghostClass: 'sortable-ghost',
      dragClass: 'sortable-drag',
      onEnd: function() {
        var ids = [];
        $('#heroSlidesGrid .hero-slide-card').each(function() {
          ids.push($(this).data('id'));
        });
        adminAjax('hero_slide_reorder.php', { ids: ids }, function() {
          $('#heroSlidesGrid .hero-order-input').each(function(index) {
            $(this).val(index + 1);
          });
        });
      }
    });
  }
});
</script>
JSBLOCK;

require_once __DIR__ . '/includes/footer.php';
?>
