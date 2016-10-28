<?php

// =============================================================================
// FUNCTIONS/OPTIONS.PHP
// -----------------------------------------------------------------------------
// Plugin options.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Set Options
//   02. Get Options
// =============================================================================

// Set Options
// =============================================================================

//
// Set $_POST variables to options array and update option.
//

GLOBAL $x_smooth_scroll_options;

if ( isset( $_POST['x_smooth_scroll_form_submitted'] ) ) {
  if ( strip_tags( $_POST['x_smooth_scroll_form_submitted'] ) == 'submitted' && current_user_can( 'manage_options' ) ) {

    $x_smooth_scroll_options['x_smooth_scroll_enable'] = ( isset( $_POST['x_smooth_scroll_enable'] ) ) ? strip_tags( $_POST['x_smooth_scroll_enable'] ) : '';
    $x_smooth_scroll_options['x_smooth_scroll_step']   = strip_tags( $_POST['x_smooth_scroll_step'] );
    $x_smooth_scroll_options['x_smooth_scroll_speed']  = strip_tags( $_POST['x_smooth_scroll_speed'] );

    update_option( 'x_smooth_scroll', $x_smooth_scroll_options );

  }
}



// Get Options
// =============================================================================

$x_smooth_scroll_options = apply_filters( 'x_smooth_scroll_options', get_option( 'x_smooth_scroll' ) );

if ( $x_smooth_scroll_options != '' ) {

  $x_smooth_scroll_enable = $x_smooth_scroll_options['x_smooth_scroll_enable'];
  $x_smooth_scroll_step   = $x_smooth_scroll_options['x_smooth_scroll_step'];
  $x_smooth_scroll_speed  = $x_smooth_scroll_options['x_smooth_scroll_speed'];

}
