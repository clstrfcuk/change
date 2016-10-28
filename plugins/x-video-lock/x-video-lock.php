<?php

/*

Plugin Name: X &ndash; Video Lock
Plugin URI: http://theme.co/x/
Description: You've never seen a video marketing tool quite like Video Lock. Place offers and a call to action in front of your users without any fuss.
Version: 1.1.1
Author: Themeco
Author URI: http://theme.co/
Text Domain: __x__
X Plugin: x-video-lock

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

define( 'X_VIDEO_LOCK_VERSION', '1.1.1' );
define( 'X_VIDEO_LOCK_URL', plugins_url( '', __FILE__ ) );
define( 'X_VIDEO_LOCK_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );


//
// Global variables.
//

$x_video_lock_options = array();



// Setup Menu
// =============================================================================

function x_video_lock_options_page() {
  require( 'views/admin/options-page.php' );
}

function x_video_lock_menu() {
  add_submenu_page( 'x-addons-home', __( 'Video Lock', '__x__' ), __( 'Video Lock', '__x__' ), 'manage_options', 'x-extensions-video-lock', 'x_video_lock_options_page' );
}

add_action( 'admin_menu', 'x_video_lock_menu', 100 );



// Initialize
// =============================================================================

function x_video_lock_init() {

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

add_action( 'init', 'x_video_lock_init' );