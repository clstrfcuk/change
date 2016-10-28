<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/SITE/SCRIPTS.PHP
// -----------------------------------------------------------------------------
// Enqueue site scripts for the plugin. This file is included within the
// 'wp_enqueue_scripts' action.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Site Scripts
// =============================================================================

// Enqueue Site Scripts
// =============================================================================

wp_enqueue_script( $plugin_title . '-site-js', $plugin_url . '/js/site/main.js', array( 'jquery' ), NULL, true );

wp_localize_script( $plugin_title . '-site-js', 'x_email_forms' , array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );