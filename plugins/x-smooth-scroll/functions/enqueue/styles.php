<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/STYLES.PHP
// -----------------------------------------------------------------------------
// Plugin styles.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Output Site Styles
//   02. Enqueue Admin Styles
// =============================================================================

// Output Site Styles
// =============================================================================

function x_smooth_scroll_output_site_styles() {

  require( X_SMOOTH_SCROLL_PATH . '/functions/options.php' );

  if ( isset( $x_smooth_scroll_enable ) && $x_smooth_scroll_enable == 1 ) : ?>

    html.x-smooth-scroll {
      overflow-x: hidden !important;
      overflow-y: auto !important;
    }

    html.x-smooth-scroll .nicescroll-rails {
      display: none !important;
    }

  <?php endif;

}

add_action( 'x_head_css', 'x_smooth_scroll_output_site_styles' );



// Enqueue Admin Styles
// =============================================================================

function x_smooth_scroll_enqueue_admin_styles( $hook ) {

  if ( $hook == 'addons_page_x-extensions-smooth-scroll' ) {

    wp_enqueue_style( 'x-smooth-scroll-admin-css', X_SMOOTH_SCROLL_URL . '/css/admin/style.css', NULL, NULL, 'all' );

  }

}

add_action( 'admin_enqueue_scripts', 'x_smooth_scroll_enqueue_admin_styles' );