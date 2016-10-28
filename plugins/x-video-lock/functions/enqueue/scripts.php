<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/SCRIPTS.PHP
// -----------------------------------------------------------------------------
// Plugin scripts.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Admin Scripts
// =============================================================================

// Enqueue Admin Scripts
// =============================================================================

function x_video_lock_enqueue_admin_scripts( $hook ) {

  if ( $hook == 'addons_page_x-extensions-video-lock' ) {

    wp_enqueue_script( 'x-video-lock-admin-js', X_VIDEO_LOCK_URL . '/js/admin/main.js', array( 'jquery' ), NULL, true );

  }

}

add_action( 'admin_enqueue_scripts', 'x_video_lock_enqueue_admin_scripts' );