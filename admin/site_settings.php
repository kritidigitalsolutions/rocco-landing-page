<?php
/**
 * Rocco Play Admin — Site Settings
 * Tabbed layout: General, App Links, Analytics, Danger Zone
 */
$pageTitle = 'Site Settings';
$breadcrumb = [['label' => 'Site Settings']];

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireAuth();

// Fetch current settings
$settings = $pdo->query("SELECT * FROM site_settings WHERE id = 1")->fetch();
if (!$settings) {
    $pdo->exec("INSERT INTO site_settings (id, copyright_text) VALUES (1, '&copy; " . date('Y') . " RoccoPlay. All rights reserved.')");
    $settings = $pdo->query("SELECT * FROM site_settings WHERE id = 1")->fetch();
}

$csrf_token = generateCsrfToken();
$currentPage = 'site_settings';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Site Settings — Rocco Play Admin</title>
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
        <h1 class="page-title">Site Settings</h1>
        <p class="page-subtitle">Configure your website's global settings, links, and analytics.</p>
      </div>

      <!-- Tabs -->
      <div class="tabs-admin">
        <button class="tab-btn active" data-tab="general">
          <i class="fas fa-gear"></i> General & Footer
        </button>
        <button class="tab-btn" data-tab="applinks">
          <i class="fas fa-mobile-screen"></i> App Links
        </button>
        <button class="tab-btn" data-tab="analytics">
          <i class="fas fa-chart-line"></i> Analytics
        </button>
        <button class="tab-btn" data-tab="danger">
          <i class="fas fa-skull-crossbones"></i> Danger Zone
        </button>
      </div>

      <!-- Tab 1: General -->
      <div class="tab-pane active" id="tab-general">
        <div class="card" style="max-width:700px;">
          <div class="card-header-custom">
            <div class="card-title-custom">
              <i class="fas fa-copyright"></i> Footer & Copyright
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Copyright Text</label>
            <textarea class="form-control-admin" id="copyrightText" rows="3"><?php echo htmlspecialchars($settings['copyright_text'] ?? ''); ?></textarea>
            <div class="form-hint">HTML allowed. Will be displayed in the website footer.</div>
          </div>

          <!-- Preview -->
          <div style="background:var(--bg-input);border:1px solid var(--glass-border);border-radius:10px;padding:16px;margin-bottom:20px;">
            <div style="font-size:0.75rem;color:var(--text-muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:1px;">Footer Preview</div>
            <div id="copyrightPreview" style="font-size:0.85rem;color:var(--text-secondary);">
              <?php echo $settings['copyright_text'] ?? ''; ?>
            </div>
          </div>

          <button class="btn-admin btn-primary-admin" onclick="saveSettings('general')">
            <i class="fas fa-save"></i> Save Changes
          </button>
        </div>
      </div>

      <!-- Tab 2: App Links -->
      <div class="tab-pane" id="tab-applinks">
        <div class="card" style="max-width:700px;">
          <div class="card-header-custom">
            <div class="card-title-custom">
              <i class="fas fa-link"></i> App Download Links
            </div>
          </div>

          <div class="form-group">
            <label class="form-label"><i class="fab fa-google-play" style="color:#34a853;margin-right:6px;"></i> Google Play Store URL</label>
            <input type="url" class="form-control-admin" id="playstoreLink" 
                   value="<?php echo htmlspecialchars($settings['playstore_link'] ?? ''); ?>" 
                   placeholder="https://play.google.com/store/apps/details?id=...">
            <div class="form-error" id="playstoreError">Please enter a valid URL.</div>
          </div>

          <div class="form-group">
            <label class="form-label"><i class="fab fa-apple" style="color:#999;margin-right:6px;"></i> Apple App Store URL</label>
            <input type="url" class="form-control-admin" id="appstoreLink" 
                   value="<?php echo htmlspecialchars($settings['appstore_link'] ?? ''); ?>" 
                   placeholder="https://apps.apple.com/app/...">
            <div class="form-error" id="appstoreError">Please enter a valid URL.</div>
          </div>

          <div class="form-group">
            <label class="form-label"><i class="fas fa-link" style="color:var(--brand-gold);margin-right:6px;"></i> Custom App Link (Optional)</label>
            <input type="url" class="form-control-admin" id="customAppLink" 
                   value="<?php echo htmlspecialchars($settings['custom_app_link'] ?? ''); ?>" 
                   placeholder="https://your-custom-link.com">
          </div>

          <button class="btn-admin btn-primary-admin" onclick="saveSettings('applinks')">
            <i class="fas fa-save"></i> Save Links
          </button>
        </div>
      </div>

      <!-- Tab 3: Analytics -->
      <div class="tab-pane" id="tab-analytics">
        <div class="card" style="max-width:700px;">
          <div class="card-header-custom">
            <div class="card-title-custom">
              <i class="fas fa-chart-line"></i> Analytics & Tracking Codes
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Google Tag Manager Code</label>
            <textarea class="form-control-admin" id="gtmCode" rows="6" style="font-family:monospace;font-size:0.82rem;"><?php echo htmlspecialchars($settings['gtm_code'] ?? ''); ?></textarea>
            <div class="form-hint">Paste the full GTM &lt;script&gt; snippet or just the container ID (e.g. GTM-XXXXX).</div>
          </div>

          <div class="form-group">
            <label class="form-label">Google Analytics 4 Code</label>
            <textarea class="form-control-admin" id="gaCode" rows="6" style="font-family:monospace;font-size:0.82rem;"><?php echo htmlspecialchars($settings['ga_code'] ?? ''); ?></textarea>
            <div class="form-hint">Paste the full GA4 &lt;script&gt; snippet or measurement ID (e.g. G-XXXXXXXXXX).</div>
          </div>

          <div class="flash-message flash-error" style="display:flex;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>These scripts will be automatically added to the &lt;head&gt; section of <strong>all pages</strong> on the website.</span>
          </div>

          <button class="btn-admin btn-primary-admin" onclick="saveSettings('analytics')">
            <i class="fas fa-save"></i> Save Analytics
          </button>
        </div>
      </div>

      <!-- Tab 4: Danger Zone -->
      <div class="tab-pane" id="tab-danger">
        <div class="card" style="max-width:700px;">
          <div class="danger-zone">
            <h3><i class="fas fa-skull-crossbones"></i> Danger Zone</h3>
            <p>These actions are destructive and cannot be undone easily.</p>

            <div style="display:flex;flex-direction:column;gap:16px;">
              <div style="display:flex;align-items:center;justify-content:space-between;padding:16px;background:rgba(230,57,70,0.05);border-radius:10px;border:1px solid rgba(230,57,70,0.1);">
                <div>
                  <strong style="font-size:0.9rem;">Reset Settings to Default</strong>
                  <p style="font-size:0.8rem;color:var(--text-muted);margin:4px 0 0;">Clears all settings fields back to empty defaults.</p>
                </div>
                <button class="btn-admin btn-sm-admin btn-danger-admin" onclick="resetSettings()">
                  <i class="fas fa-rotate-left"></i> Reset
                </button>
              </div>

              <div style="display:flex;align-items:center;justify-content:space-between;padding:16px;background:rgba(230,57,70,0.05);border-radius:10px;border:1px solid rgba(230,57,70,0.1);">
                <div>
                  <strong style="font-size:0.9rem;">Clear All Media</strong>
                  <p style="font-size:0.8rem;color:var(--text-muted);margin:4px 0 0;">Delete all uploaded images from the server and database.</p>
                </div>
                <button class="btn-admin btn-sm-admin btn-danger-admin" onclick="clearAllMedia()">
                  <i class="fas fa-trash"></i> Clear
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

<?php
$csrfJS = htmlspecialchars($csrf_token);
$extraScripts = <<<JSBLOCK
<script>
$(document).ready(function() {
  // Tab switching
  $('.tab-btn').on('click', function() {
    $('.tab-btn').removeClass('active');
    $(this).addClass('active');
    $('.tab-pane').removeClass('active');
    $('#tab-' + $(this).data('tab')).addClass('active');
  });

  // Live copyright preview
  $('#copyrightText').on('input', function() {
    $('#copyrightPreview').html($(this).val());
  });
});

function saveSettings(section) {
  var data = { csrf_token: '{$csrfJS}', section: section };

  if (section === 'general') {
    data.copyright_text = $('#copyrightText').val();
  } else if (section === 'applinks') {
    data.playstore_link = $('#playstoreLink').val();
    data.appstore_link = $('#appstoreLink').val();
    data.custom_app_link = $('#customAppLink').val();
    // Validate URLs
    var urlPattern = /^(https?:\/\/|$)/;
    var valid = true;
    if (data.playstore_link && !urlPattern.test(data.playstore_link)) {
      $('#playstoreError').addClass('show'); valid = false;
    } else { $('#playstoreError').removeClass('show'); }
    if (data.appstore_link && !urlPattern.test(data.appstore_link)) {
      $('#appstoreError').addClass('show'); valid = false;
    } else { $('#appstoreError').removeClass('show'); }
    if (!valid) return;
  } else if (section === 'analytics') {
    data.gtm_code = $('#gtmCode').val();
    data.ga_code = $('#gaCode').val();
  }

  adminAjax('settings_save.php', data);
}

function resetSettings() {
  Swal.fire({
    title: 'Reset All Settings?',
    text: 'This will clear all settings fields to defaults.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, Reset',
    confirmButtonColor: '#E63946'
  }).then(function(result) {
    if (result.isConfirmed) {
      adminAjax('settings_save.php', { csrf_token: '{$csrfJS}', action: 'reset' }, function() {
        setTimeout(function() { location.reload(); }, 1000);
      });
    }
  });
}

function clearAllMedia() {
  Swal.fire({
    title: 'Delete ALL Media?',
    text: 'This will permanently delete every uploaded image from the server and database.',
    icon: 'error',
    showCancelButton: true,
    confirmButtonText: 'Yes, Delete Everything',
    confirmButtonColor: '#E63946'
  }).then(function(result) {
    if (result.isConfirmed) {
      adminAjax('settings_save.php', { csrf_token: '{$csrfJS}', action: 'clear_media' }, function() {
        toastr.success('All media cleared.');
      });
    }
  });
}
</script>
JSBLOCK;
require_once __DIR__ . '/includes/footer.php';
?>
