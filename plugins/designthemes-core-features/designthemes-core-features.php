<?php
/*
 * Plugin Name:	DesignThemes Core Features Plugin 
 * URI: 	http://wedesignthemes.com/plugins/designthemes-core-features 
 * Description: A simple wordpress plugin designed to implements <strong>core features of DesignThemes</strong> 
 * Version: 	1.2
 * Author: 		DesignThemes 
 * Author URI:	http://themeforest.net/user/designthemes
 */
if (! class_exists ( 'DTCorePlugin' )) {
	
	/**
	 * Basic class to load Shortcodes & Custom Posts
	 *
	 * @author iamdesigning11
	 */
	class DTCorePlugin {

		function __construct() {
			
			// Add Hook into the 'init()' action
			add_action ( 'init', array ( $this,'dtLoadPluginTextDomain') );

			// Register Shortcodes
			require_once plugin_dir_path ( __FILE__ ) . '/shortcodes/register-shortcodes.php';
			
			if (class_exists ( 'DTCoreShortcodes' )) {
				$dt_core_shortcodes = new DTCoreShortcodes();
			}
			
			// Register Custom Post Types
			require_once plugin_dir_path ( __FILE__ ) . '/custom-post-types/register-post-types.php';
			
			if (class_exists ( 'DTCoreCustomPostTypes' )) {
				$dt_core_custom_posts = new DTCoreCustomPostTypes();
			}
			
			// Register Page Builder
			require_once plugin_dir_path ( __FILE__ ) . '/page-builder/register-page-builder.php';
			
			if (class_exists ( 'DTCorePageBuilder' )) {
				$dt_core_page_builder = new DTCorePageBuilder ();
			}
			
			require_once plugin_dir_path ( __FILE__ ) . '/functions.php';
		}
		
		/**
		 * To load text domain
		 */
		function dtLoadPluginTextDomain() {
			load_plugin_textdomain ( 'dt_themes', false, dirname ( plugin_basename ( __FILE__ ) ) . '/languages/' );
		}
		
		/**
		 */
		public static function dtCorePluginActivate() {
		}
		
		/**
		 */
		public static function dtCorePluginDectivate() {
		}
	}
}

if (class_exists ( 'DTCorePlugin' )) {
	
	register_activation_hook ( __FILE__, array ('DTCorePlugin','dtCorePluginActivate') );
	register_deactivation_hook ( __FILE__, array ('DTCorePlugin','dtCorePluginDectivate') );
	$dt_core_plugin = new DTCorePlugin();
	
	if( !function_exists( 'dttheme_option' ) ){
		function dttheme_option($key1, $key2 = '') {
			$options = get_option ( 'mytheme' );
			$output = NULL;
		
			if (is_array ( $options )) {
		
				if (array_key_exists ( $key1, $options )) {
					$output = $options [$key1];
					if (is_array ( $output ) && ! empty ( $key2 )) {
						$output = (array_key_exists ( $key2, $output ) && (! empty ( $output [$key2] ))) ? $output [$key2] : NULL;
					}
				} else {
					$output = $output;
				}
			}
			return $output;
		}
	}
	
}
?>