<?php

// =============================================================================
// FUNCTIONS/OUTPUT.PHP
// -----------------------------------------------------------------------------
// Plugin output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Google Analytics
//   02. Output
// =============================================================================

// Google Analytics
// =============================================================================

function x_google_analytics_output() {

  require( X_GOOGLE_ANALYTICS_PATH . '/views/site/google-analytics.php' );

}



// Output
// =============================================================================

require( X_GOOGLE_ANALYTICS_PATH . '/functions/options.php' );

if ( isset( $x_google_analytics_enable ) && $x_google_analytics_enable == 1 ) {

  add_action( 'wp_' . $x_google_analytics_position, 'x_google_analytics_output', 9999 );

}