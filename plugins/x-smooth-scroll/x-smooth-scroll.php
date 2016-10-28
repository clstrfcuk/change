<?php

/*

Plugin Name: X &ndash; Smooth Scroll
Plugin URI: http://theme.co/x/
Description: Enabling smooth scrolling on your website allows you to manage the physics of your scroll bar! This fun effect is great if you happen to have a lot users who utilize a mousewheel.
Version: 1.0.2
Author: Themeco
Author URI: http://theme.co/
Text Domain: __x__
X Plugin: x-smooth-scroll

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

define( 'X_SMOOTH_SCROLL_VERSION', '1.0.1' );
define( 'X_SMOOTH_SCROLL_URL', plugins_url( '', __FILE__ ) );
define( 'X_SMOOTH_SCROLL_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );


//
// Global variables.
//

$x_smooth_scroll_options = array();



// Setup Menu
// =============================================================================

function x_smooth_scroll_options_page() {
  require( 'views/admin/options-page.php' );
}

function x_smooth_scroll_menu() {
  add_submenu_page( 'x-addons-home', __( 'Smooth Scroll', '__x__' ), __( 'Smooth Scroll', '__x__' ), 'manage_options', 'x-extensions-smooth-scroll', 'x_smooth_scroll_options_page' );
}

add_action( 'admin_menu', 'x_smooth_scroll_menu', 100 );



// Initialize
// =============================================================================

function x_smooth_scroll_init() {

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

add_action( 'init', 'x_smooth_scroll_init' );