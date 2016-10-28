// =============================================================================
// JS/ADMIN/MAIN.JS
// -----------------------------------------------------------------------------
// Plugin admin scripts.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Provider Settings Screen
// =============================================================================

// Provider Settings Screen
// =============================================================================

jQuery(document).ready(function($) {

  //
  // Toggle send welcome email visibility.
  //

  $row_send_welcome = $('#x_email_forms_mc_send_welcome_yes').parents('tr');

  if ( $('input[name="x_email_forms[mc_skip_double_opt_in]"]:checked').val() === 'no' ) {
    $row_send_welcome.hide();
  }

  $('input[name="x_email_forms[mc_skip_double_opt_in]"]').change(function(){
    $row_send_welcome.toggle();
  });

});