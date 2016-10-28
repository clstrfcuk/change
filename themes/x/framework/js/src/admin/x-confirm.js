// =============================================================================
// JS/ADMIN/X-CONFIRM.JS
// -----------------------------------------------------------------------------
// Confirm functionality.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Confirm Functionality
// =============================================================================

// Confirm Functionality
// =============================================================================

jQuery(document).ready(function($) {

  //
  // Confirm.
  //

  window.xAdminConfirm = function(type, message, callback) {

    $('body').append('<div class="x-admin-confirm-outer">' +
                       '<div class="x-admin-confirm-inner">' +
                         '<div class="x-admin-confirm-content">' +
                           '<p class="x-admin-confirm-message">' + message + '</p>' +
                           '<div class="x-admin-confirm-actions">' +
                             '<button class="nope">Nop<span>e</span></button>' +
                             '<button class="yep ' + type + '">Ye<span>p</span></button>' +
                           '</div>' +
                         '</div>' +
                       '</div>' +
                     '</div>');

    $('.x-admin-confirm-actions button').on('click', function(e) {

      $('.x-admin-confirm-outer').remove();

      if ( $(this).hasClass('yep') ) {
        callback();
      }

    });

  };


  //
  // Confirm form.
  //

  window.xAdminConfirmForm = function(form, type, message) {

    form.on('submit', function(e) {

      e.preventDefault();

      xAdminConfirm(type, message, function() {
        form[0].submit();
      });

    });

  };

});