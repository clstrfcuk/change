<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/STYLES.PHP
// -----------------------------------------------------------------------------
// Enqueue all styles for the Google Analytics.
// =============================================================================

// Register and Enqueue Site Styles
// =============================================================================

function x_google_analytics_enqueue_admin_styles( $hook ) {

  if ( $hook == 'addons_page_x-extensions-google-analytics' ) {

    wp_enqueue_style( 'x-google-analytics-admin-css', X_GOOGLE_ANALYTICS_URL . '/css/admin/style.css', NULL, NULL, 'all' );

  }

}

add_action( 'admin_enqueue_scripts', 'x_google_analytics_enqueue_admin_styles' );