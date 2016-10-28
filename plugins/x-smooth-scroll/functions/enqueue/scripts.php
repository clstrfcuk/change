<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/SCRIPTS.PHP
// -----------------------------------------------------------------------------
// Plugin scripts.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Site Scripts
//   02. Enqueue Admin Scripts
// =============================================================================

// Enqueue Site Scripts
// =============================================================================

function x_smooth_scroll_enqueue_site_scripts() {

  require( X_SMOOTH_SCROLL_PATH . '/functions/options.php' );

  if ( isset( $x_smooth_scroll_enable ) && $x_smooth_scroll_enable == 1 ) {

    wp_enqueue_script( 'x-smooth-scroll-site-js', X_SMOOTH_SCROLL_URL . '/js/site/nicescroll.js', array( 'jquery' ), NULL, true );

  }

}

add_action( 'wp_enqueue_scripts', 'x_smooth_scroll_enqueue_site_scripts' );



// Enqueue Admin Scripts
// =============================================================================

function x_smooth_scroll_enqueue_admin_scripts( $hook ) {

  if ( $hook == 'addons_page_x-extensions-smooth-scroll' ) {

    wp_enqueue_script( 'x-smooth-scroll-admin-js', X_SMOOTH_SCROLL_URL . '/js/admin/main.js', array( 'jquery' ), NULL, true );

  }

}

add_action( 'admin_enqueue_scripts', 'x_smooth_scroll_enqueue_admin_scripts' );