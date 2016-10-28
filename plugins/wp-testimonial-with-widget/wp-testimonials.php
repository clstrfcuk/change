<?php
/**
 * Plugin Name: WP Testimonials with rotator widget
 * Plugin URI: http://www.wponlinesupport.com/
 * Text Domain: wp-testimonial-with-widget
 * Domain Path: /languages/
 * Description: Easy to add and display client's testimonial on your website with rotator widget. 
 * Author: WP Online Support
 * Version: 2.2.4
 * Author URI: http://www.wponlinesupport.com/
 *
 * @package WordPress
 * @author WP Online Support
 */

if( !defined( 'WTWP_VERSION' ) ) {
    define( 'WTWP_VERSION', '2.2.4' ); // Version of plugin
}

/**
 * Activation Hook
 * 
 * Register plugin activation hook.
 * 
 * @package WP Testimonials with rotator widget
 * @since 1.0.0
 */
register_activation_hook( __FILE__, 'wtwp_install' );

/**
 * Plugin Setup (On Activation)
 * 
 * Does the initial setup,
 * stest default values for the plugin options.
 * 
 * @package WP Testimonials with rotator widget
 * @since 1.0.0
 */
function wtwp_install() {
	// To deactivate the free version of plugin
	if( is_plugin_active('wp-testimonial-with-widget-pro/wp-testimonials.php') ){
     	add_action( 'update_option_active_plugins', 'wtwp_deactivate_version' );
    }
}

/**
 * Function to deactivate the free version plugin
 * 
 * @package WP Testimonials with rotator widget
 * @since 1.0.0
 */
function wtwp_deactivate_version(){
	deactivate_plugins( 'wp-testimonial-with-widget-pro/wp-testimonials.php', true );
}

// Action to add admin notice
add_action( 'admin_notices', 'wtwp_admin_notice');

/**
 * Admin notice
 * 
 * @package WP Testimonials with rotator widget
 * @since 1.0.0
 */
function wtwp_admin_notice() {

    $dir = ABSPATH . 'wp-content/plugins/wp-testimonial-with-widget-pro/wp-testimonials.php';
    
    if( is_plugin_active( 'wp-testimonial-with-widget/wp-testimonials.php' ) && file_exists($dir) ) {
        global $pagenow;
        if( $pagenow == 'plugins.php' ) {
            
            deactivate_plugins ( 'wp-testimonial-with-widget-pro/wp-testimonials.php',true);

            if ( current_user_can( 'install_plugins' ) ) {
                echo '<div id="message" class="updated notice is-dismissible"><p><strong>Thank you for activating WP Testimonials with rotator widget</strong>.<br /> It looks like you had PRO version <strong>(<em>WP Testimonials with rotator widget Pro</em>)</strong> of this plugin activated. To avoid conflicts the extra version has been deactivated and we recommend you delete it. </p></div>';
            }
        }
    }
}

add_action('plugins_loaded', 'wp_testimonialsandw_load_textdomain');
function wp_testimonialsandw_load_textdomain() {
	load_plugin_textdomain( 'wp-testimonial-with-widget', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
} 

/**
 * Function to get plugin image sizes array
 * 
 * @package WP Testimonials with rotator widget
 * @since 2.2.4
 */
function wtwp_get_unique() {
    static $unique = 0;
    $unique++;

    return $unique;
}

add_action( 'wp_enqueue_scripts','testimonials_style_css' );
function testimonials_style_css() {

	// Registring font awesome style
	wp_register_style( 'wtwp-font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', null, WTWP_VERSION );
	wp_enqueue_style( 'wtwp-font-awesome' );

	wp_enqueue_style( 'testimonials-sp',  plugin_dir_url( __FILE__ ). 'assets/css/testimonials-style.css', null, WTWP_VERSION );
	wp_enqueue_script( 'testimonials_slick_jquery', plugin_dir_url( __FILE__ ) . 'assets/js/slick.min.js', array( 'jquery' ), WTWP_VERSION );
	wp_enqueue_style( 'testimonials_slick_style',  plugin_dir_url( __FILE__ ) . 'assets/css/slick.css', null, WTWP_VERSION);
}

require_once( 'includes/testimonials-functions.php' );
require_once( 'templates/wp-widget-testimonials.php' );
require_once( 'includes/testimonials_menu_function.php' );
require_once( 'templates/wp-testimonials-template.php' );
require_once( 'templates/wp-testimonial-slider-template.php' );