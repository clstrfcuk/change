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

GLOBAL $x_google_analytics_options;

if ( isset( $_POST['x_google_analytics_form_submitted'] ) ) {
  if ( strip_tags( $_POST['x_google_analytics_form_submitted'] ) == 'submitted' && current_user_can( 'manage_options' ) ) {
    
    $x_google_analytics_options['x_google_analytics_enable']   = ( isset( $_POST['x_google_analytics_enable'] ) ) ? strip_tags( $_POST['x_google_analytics_enable'] ) : '';
    $x_google_analytics_options['x_google_analytics_position'] = strip_tags( $_POST['x_google_analytics_position'] );
    $x_google_analytics_options['x_google_analytics_code']     = stripslashes( wp_kses( $_POST['x_google_analytics_code'], array( 'script' => array() ) ) );
    
    update_option( 'x_google_analytics', $x_google_analytics_options );

  }
}



// Get Options
// =============================================================================

$x_google_analytics_options = apply_filters( 'x_google_analytics_options', get_option( 'x_google_analytics' ) );

if ( $x_google_analytics_options != '' ) {

  $x_google_analytics_enable   = $x_google_analytics_options['x_google_analytics_enable'];
  $x_google_analytics_position = $x_google_analytics_options['x_google_analytics_position'];
  $x_google_analytics_code     = $x_google_analytics_options['x_google_analytics_code'];

}