<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/ADMIN/STYLES.PHP
// -----------------------------------------------------------------------------
// Enqueue admin styles for the plugin. This file is included within the
// 'admin_enqueue_scripts' action.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Admin Styles
// =============================================================================

// Enqueue Admin Styles
// =============================================================================

$screen = get_current_screen();

if ( $screen->id == 'addons_page_x-extensions-email-forms' ) {

  wp_enqueue_style( $plugin_title . '-admin-css', $plugin_url . '/css/admin/style.css', NULL, NULL, 'all' );

}