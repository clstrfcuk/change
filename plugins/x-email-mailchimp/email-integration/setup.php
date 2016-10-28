<?php

// =============================================================================
// SETUP.PHP
// -----------------------------------------------------------------------------
// Email integration framework.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Define Constants and Global Variables
//   02. Setup Menu
//   03. Initialize
// =============================================================================

// Define Constants and Global Variables
// =============================================================================

//
// Constants.
//

define( 'X_EMAIL_INTEGRATION_IS_LOADED', true );
define( 'X_EMAIL_INTEGRATION_URL', plugins_url( '', __FILE__ ) );
define( 'X_EMAIL_INTEGRATION_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );


//
// Global variables.
//

GLOBAL $x_email_forms;



// Setup Menu
// =============================================================================

function x_email_forms_admin_menu() {
  GLOBAL $x_email_forms;
  add_submenu_page( 'x-addons-home', __( 'Email Forms', '__x__' ), __( 'Email Forms', '__x__' ), 'manage_options', 'x-extensions-email-forms', array( $x_email_forms, 'admin_controller' ) );
}

add_action( 'admin_menu', 'x_email_forms_admin_menu', 100 );



// Initialize
// =============================================================================

require( X_EMAIL_INTEGRATION_PATH . '/functions/framework/init.php' );
require( X_EMAIL_INTEGRATION_PATH . '/functions/plugin.php' );

$x_email_forms = new X_Email_Integration( __FILE__, 'x_email_forms' );