<?php

// =============================================================================
// SETUP.PHP
// -----------------------------------------------------------------------------
// Email provider framework.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Define Constants
//   02. Enqueue Scripts
//   03. Load Provider
//   04. Register Provider
// =============================================================================

// Define Constants
// =============================================================================

define( 'X_EMAIL_MAILCHIMP_URL', plugins_url( '', __FILE__ ) );
define( 'X_EMAIL_MAILCHIMP_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );



// Enqueue Scripts
// =============================================================================

require_once( X_EMAIL_MAILCHIMP_PATH . '/functions/enqueue/scripts.php' );



// Load Provider
// =============================================================================

require_once( X_EMAIL_MAILCHIMP_PATH . '/functions/provider.php' );



// Register Provider
// =============================================================================

if ( defined( 'X_EMAIL_INTEGRATION_IS_LOADED' ) ) {
  GLOBAL $x_email_forms;
  $x_email_forms->register_provider( 'MailChimp', __FILE__ );
}