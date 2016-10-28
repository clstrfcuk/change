<?php

/*

Plugin Name: X &ndash; Email Forms (MailChimp)
Plugin URI: http://theme.co/x/
Description: Creating custom opt-in forms has never been this easy...or fun! Carefully craft every detail of your forms using this plugin and subscribe users to a MailChimp email list.
Version: 1.1.1
Author: Themeco
Author URI: http://theme.co/
Text Domain: __x__
X Plugin: x-email-mailchimp

*/

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Define Constants
//   02. Initialize
// =============================================================================

// Define Constants
// =============================================================================

define( 'X_EMAIL_MAILCHIMP_VERSION', '1.1.1' );
define( 'X_EMAIL_MAILCHIMP_ROOT_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );



// Initialize
// =============================================================================

//
// Framework. Only loaded once across all email form plugins.
//

if ( ! defined( 'X_EMAIL_INTEGRATION_IS_LOADED' ) ) {
  require( X_EMAIL_MAILCHIMP_ROOT_PATH . '/email-integration/setup.php' );
}


//
// Provider.
//

require( X_EMAIL_MAILCHIMP_ROOT_PATH . '/email-mailchimp/setup.php' );


//
// Textdomain.
//

function x_email_mailchimp_textdomain() {
  load_plugin_textdomain( '__x__', false, X_EMAIL_MAILCHIMP_ROOT_PATH . '/lang/' );
}

add_action( 'plugins_loaded', 'x_email_mailchimp_textdomain' );