<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/SCRIPTS.PHP
// -----------------------------------------------------------------------------
// Enqueue all scripts for the Google Analytics.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Site Scripts
// =============================================================================

// Enqueue Site Scripts
// =============================================================================

function x_google_analytics_enqueue_admin_scripts( $hook ) {

  if ( $hook == 'addons_page_x-extensions-google-analytics' ) {

    wp_enqueue_script( 'x-google-analytics-admin-js', X_GOOGLE_ANALYTICS_URL . '/js/admin/main.js', array( 'jquery' ), NULL, true );

  }

}

add_action( 'admin_enqueue_scripts', 'x_google_analytics_enqueue_admin_scripts' );