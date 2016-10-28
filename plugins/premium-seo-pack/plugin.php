<?php
/*
Plugin Name: 	Premium SEO pack - Wordpress Plugin
Plugin URI: 	http://codecanyon.net/user/AA-Team/portfolio
Description: 	Premium SEO Pack is the newest and most complete SEO Wordpress Plugin on the market! Also it has the most unique feature, that cannot be found on any existing plugins on the market. It’s called SEO MASS OPTIMIZATION and it allows you to mass optimize all your post/pages/custom post types in just seconds!
Version: 		1.9.0
Author: 		AA-Team
Author URI: 	http://codecanyon.net/user/AA-Team/portfolio
*/
! defined( 'ABSPATH' ) and exit;

// Derive the current path and load up psp
$plugin_path = dirname(__FILE__) . '/';
if(class_exists('psp') != true) {
    require_once($plugin_path . 'aa-framework/framework.class.php');

	// Initalize the your plugin
	$psp = new psp();

	// Add an activation hook
	register_activation_hook(__FILE__, array(&$psp, 'activate'));
	
	// load textdomain
	add_action( 'plugins_loaded', 'psp_load_textdomain' );
	
	function psp_load_textdomain() {   
		load_plugin_textdomain( 'psp', false, dirname( plugin_basename( __FILE__ ) ) . '/' );
	}
	
}