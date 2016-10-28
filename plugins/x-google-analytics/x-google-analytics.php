<?php

/*

Plugin Name: X &ndash; Google Analytics
Plugin URI: http://theme.co/x/
Description: Simply drop in your Google Analytics code snippet, select where you'd like it to be output, and you're good to go! Google Analytics made easy.
Version: 1.0.0
Author: Themeco
Author URI: http://theme.co/
Text Domain: __x__
X Plugin: x-google-analytics

*/

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

define( 'X_GOOGLE_ANALYTICS_VERSION', '1.0.0' );
define( 'X_GOOGLE_ANALYTICS_URL', plugins_url( '', __FILE__ ) );
define( 'X_GOOGLE_ANALYTICS_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );


//
// Global variables.
//

$x_google_analytics_options = array();



// Setup Menu
// =============================================================================

function x_google_analytics_options_page() {
  require( 'views/admin/options-page.php' );
}

function x_google_analytics_menu() {
  add_submenu_page( 'x-addons-home', __( 'Google Analytics', '__x__' ), __( 'Google Analytics', '__x__' ), 'manage_options', 'x-extensions-google-analytics', 'x_google_analytics_options_page' );
}

add_action( 'admin_menu', 'x_google_analytics_menu', 100 );



// Initialize
// =============================================================================

function x_google_analytics_init() {

  //
  // Textdomain.
  //

  load_plugin_textdomain( '__x__', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );


  //
  // Styles and scripts.
  //

  require( 'functions/enqueue/styles.php' );
  require( 'functions/enqueue/scripts.php' );


  //
  // Notices.
  //

  require( 'functions/notices.php' );


  //
  // Output.
  //

  require( 'functions/output.php' );

}

add_action( 'init', 'x_google_analytics_init' );