<?php
/*
Plugin Name: Transient Cleaner
Plugin URI: https://wordpress.org/plugins/artiss-transient-cleaner/
Description: Clean expired transients from your options table
Version: 1.4.2
Author: David Artiss
Author URI: http://www.artiss.co.uk
Text Domain: artiss-transient-cleaner
Domain Path: /languages
*/

/**
* Plugin initialisation
*
* Loads the plugin's translated strings
*
* @since	1.2
*/

function tc_plugin_init() {

	$language_dir = plugin_basename( dirname( __FILE__ ) ) . '/languages/';

	load_plugin_textdomain( 'artiss-transient-cleaner', false, $language_dir );

}

add_action( 'init', 'tc_plugin_init' );

/**
* Artiss Transient Cleaner
*
* Main code - include various functions
*
* @package	Artiss-Transient-Cleaner
* @since	1.2
*/

$functions_dir = plugin_dir_path( __FILE__ ) . 'includes/';

// Include all the various functions

include_once( $functions_dir . 'clean-transients.php' );     			// General configuration set-up

include_once( $functions_dir . 'shared-functions.php' );     			// Assorted shared functions

if ( is_admin() ) {

	include_once( $functions_dir . 'set-admin-config.php' );			// Administration configuration

}

?>
