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

function x_email_mailchimp_enqueue_admin_scripts( $hook ) {

  if ( $hook == 'addons_page_x-extensions-email-forms' && ( isset( $_GET['tab'] ) && $_GET['tab'] == 'mailchimp' ) ) {

    wp_enqueue_script( 'x-email-mailchimp-admin-js', X_EMAIL_MAILCHIMP_URL . '/js/admin/main.js', array( 'jquery' ), NULL, true );

  }

}

add_action( 'admin_enqueue_scripts', 'x_email_mailchimp_enqueue_admin_scripts' );