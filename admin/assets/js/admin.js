/**
 * Rocco Play Admin — Common JavaScript
 */

$(document).ready(function() {

  // ===================== Toastr Config =====================
  toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: 3000,
    showEasing: 'swing',
    hideEasing: 'linear',
    showMethod: 'fadeIn',
    hideMethod: 'fadeOut'
  };

  // ===================== Sidebar Toggle =====================
  const $wrapper = $('#adminWrapper');
  const $sidebar = $('#sidebar');
  const $overlay = $('#sidebarOverlay');

  $('#sidebarToggle').on('click', function() {
    if (window.innerWidth <= 992) {
      $sidebar.toggleClass('mobile-open');
      $overlay.toggleClass('show');
    } else {
      $wrapper.toggleClass('sidebar-collapsed');
      localStorage.setItem('sidebarCollapsed', $wrapper.hasClass('sidebar-collapsed') ? '1' : '0');
    }
  });

  $overlay.on('click', function() {
    $sidebar.removeClass('mobile-open');
    $overlay.removeClass('show');
  });

  // Restore sidebar state
  if (localStorage.getItem('sidebarCollapsed') === '1' && window.innerWidth > 992) {
    $wrapper.addClass('sidebar-collapsed');
  }

  // ===================== Animated Counters =====================
  function animateCounters() {
    $('.stat-card-value[data-count]').each(function() {
      const $el = $(this);
      if ($el.data('animated')) return;
      $el.data('animated', true);
      const target = parseInt($el.data('count'));
      const duration = 1500;
      const start = 0;
      const startTime = performance.now();

      function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3); // easeOutCubic
        const current = Math.floor(start + (target - start) * eased);
        $el.text(current.toLocaleString());
        if (progress < 1) {
          requestAnimationFrame(update);
        }
      }
      requestAnimationFrame(update);
    });
  }
  animateCounters();

  // ===================== Flash Messages Auto-hide =====================
  setTimeout(function() {
    $('.flash-message').fadeOut(400, function() { $(this).remove(); });
  }, 5000);

  // ===================== Spinner Helpers =====================
  window.showSpinner = function() {
    $('#spinnerOverlay').addClass('show');
  };
  window.hideSpinner = function() {
    $('#spinnerOverlay').removeClass('show');
  };

  // ===================== CSRF Token for AJAX =====================
  window.csrfToken = $('meta[name="csrf-token"]').attr('content') || 
                     $('input[name="csrf_token"]').first().val() || '';

  // ===================== AJAX Helper =====================
  window.adminAjax = function(url, data, successCb, errorCb) {
    data.csrf_token = window.csrfToken;
    showSpinner();
    $.ajax({
      url: url,
      type: 'POST',
      data: data,
      dataType: 'json',
      success: function(res) {
        hideSpinner();
        if (res.success) {
          toastr.success(res.message || 'Done!');
          if (successCb) successCb(res);
        } else {
          toastr.error(res.message || 'Something went wrong.');
          if (errorCb) errorCb(res);
        }
      },
      error: function(xhr) {
        hideSpinner();
        toastr.error('Server error. Please try again.');
        if (errorCb) errorCb(xhr);
      }
    });
  };

  // ===================== Slug Generator =====================
  window.generateSlug = function(text) {
    return text.toLowerCase()
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .trim();
  };

  // ===================== Delete Confirmation =====================
  window.confirmDelete = function(message, callback) {
    Swal.fire({
      title: 'Are you sure?',
      text: message || 'This action cannot be undone.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, Delete',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#E63946',
      cancelButtonColor: '#333',
      reverseButtons: true
    }).then(function(result) {
      if (result.isConfirmed && callback) {
        callback();
      }
    });
  };

  // ===================== Real-time Table Search =====================
  $(document).on('input', '#tableSearch', function() {
    const query = $(this).val().toLowerCase();
    const $rows = $(this).closest('.card').find('tbody tr');
    $rows.each(function() {
      const text = $(this).text().toLowerCase();
      $(this).toggle(text.includes(query));
    });
  });

});
