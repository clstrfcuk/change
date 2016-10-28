<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/ADMIN/SCRIPTS.PHP
// -----------------------------------------------------------------------------
// Enqueue admin scripts for the plugin. This file is included within the
// 'admin_enqueue_scripts' action.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Admin Scripts
// =============================================================================

// Enqueue Admin Scripts
// =============================================================================

$screen = get_current_screen();

if ( $screen->id == 'addons_page_x-extensions-email-forms' || $screen->id == 'x-email-forms' ) {

  wp_enqueue_script( $plugin_title . '-admin-js', $plugin_url . '/js/admin/main.js', array( 'jquery' ), NULL, true );
  wp_enqueue_media();

}