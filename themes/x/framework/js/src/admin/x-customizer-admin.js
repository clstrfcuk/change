
// =============================================================================
// JS/ADMIN/X-CUSTOMIZER-ADMIN.JS
// -----------------------------------------------------------------------------
// Customizer admin scripts.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Customizer Admin Scripts
// =============================================================================

// Customizer Admin Scripts
// =============================================================================

jQuery(document).ready(function($) {

  $('#x-addons-customizer-manager-import').change(function() {
    $('#x-addons-customizer-manager-import-submit').removeAttr('disabled');
  });

  xAdminConfirmForm($('.x-addons-postbox.customizer-manager.reset form'), 'error', 'This will reset your Customizer settings and is not reversible unless you have previously made a backup of your settings. Are you sure you want to proceed?');

});