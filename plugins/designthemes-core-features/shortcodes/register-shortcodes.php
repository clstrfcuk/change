<?php
if (! class_exists ( 'DTCoreShortcodes' )) {
	
	/**
	 * Used to "Loades Core Shortcodes & Add button to tinymce"
	 *
	 * @author iamdesigning11
	 */
	class DTCoreShortcodes {
		
		/**
		 * Constructor for DTCoreShortcodes
		 */
		function __construct() {
			define ( 'DESIGNTHEMES_TINYMCE_URL', plugin_dir_url ( __FILE__ ) . 'tinymce' );
			define ( 'DESIGNTHEMES_TINYMCE_PATH', plugin_dir_path ( __FILE__ ) . 'tinymce' );
			
			require_once plugin_dir_path ( __FILE__ ) . 'shortcodes.php';
			
			// Add Hook into the 'init()' action
			add_action ( 'init', array ( $this,'dt_init') );
			
			// Add Hook into the 'admin_init()' action
			add_action ( 'admin_init', array ($this,'dt_admin_init') );
			  
			add_filter ( 'the_content', array ($this,'dt_the_content_filter') );
		}
		
		/**
		 * A function hook that the WordPress core launches at 'init' points
		 */
		function dt_init() {
			
			/* Front End CSS & jQuery */
			if (! is_admin ()) {

				wp_enqueue_style ( 'dt-animation-css', plugin_dir_url ( __FILE__ ) . 'css/animations.css' );
				wp_enqueue_style ( 'dt-sc-css', plugin_dir_url ( __FILE__ ) . 'css/shortcodes.css' );
				
				wp_enqueue_script ( 'jquery' );
				wp_enqueue_script ( 'jquery-ui-datepicker' );
				wp_enqueue_script ( 'dt-sc-timepicker-addon', plugin_dir_url ( __FILE__ ) . 'js/jquery-ui-timepicker-addon.js', array (), false, true );
				wp_enqueue_script ( 'dt-sc-inview-script', plugin_dir_url ( __FILE__ ) . 'js/inview.js', array (), false, true );
				wp_enqueue_script ( 'dt-sc-tabs-script', plugin_dir_url ( __FILE__ ) . 'js/jquery.tabs.min.js', array (), false, true );
				wp_enqueue_script ( 'dt-sc-viewport-script', plugin_dir_url ( __FILE__ ) . 'js/jquery.viewport.js', array (), false, true );
				wp_enqueue_script ( 'dt-sc-carouFredSel-script', plugin_dir_url ( __FILE__ ) . 'js/jquery.carouFredSel-6.2.1-packed.js', array (), false, true );
				wp_enqueue_script ( 'dt-sc-tipTip-script', plugin_dir_url ( __FILE__ ) . 'js/jquery.tipTip.minified.js', array (), false, true );
				wp_enqueue_script ( 'dt-sc-donutchart-script', plugin_dir_url ( __FILE__ ) . 'js/jquery.donutchart.js', array (), false, true );
				wp_enqueue_script ( 'dt-sc-countTo-script', plugin_dir_url ( __FILE__ ) . 'js/countTo.js',array(),false,true);
				wp_enqueue_script ( 'dt-sc-parallax-script', plugin_dir_url ( __FILE__ ) . 'js/jquery.parallax-1.1.3.js', array(), false, true);
				wp_enqueue_script ( 'dt-sc-script', plugin_dir_url ( __FILE__ ) . 'js/shortcodes.js', array (), false, true );
			}
			
			if (! current_user_can ( 'edit_posts' ) && ! current_user_can ( 'edit_pages' )) {
				return;
			}
			
			if ("true" === get_user_option ( 'rich_editing' )) {
				add_filter ( 'mce_buttons', array (
						$this,
						'dt_register_rich_buttons' 
				) );
				
				add_filter ( 'mce_external_plugins', array (
						$this,
						'dt_add_external_plugins' 
				) );
			}
		}
		
		/**
		 * A function hook that the WordPress core launches at 'admin_init' points
		 */
		function dt_admin_init() {
			wp_enqueue_style ( 'wp-color-picker' );
			wp_enqueue_script ( 'wp-color-picker' );
			
			// css
			wp_enqueue_style ( 'DTCorePlugin-sc-dialog', DESIGNTHEMES_TINYMCE_URL . '/css/styles.css', false, '1.0', 'all' );
			
			wp_localize_script ( 'jquery', 'DTCorePlugin', array (
					'plugin_folder' => WP_PLUGIN_URL . '/designthemes-core-features',
					'tinymce_folder' => DESIGNTHEMES_TINYMCE_URL 
			) );
		}
		
		/**
		 * A function hook that used to filter the content - to remove unwanted codes
		 *
		 * @param string $content        	
		 * @return string
		 */

		function dt_the_content_filter($content) {
			$dt_shortcodes = array("dt_sc_accordion_group","dt_sc_button","dt_sc_blockquote","dt_sc_callout_box","dt_sc_one_half","dt_sc_one_third","dt_sc_one_fourth","dt_sc_one_fifth","dt_sc_one_sixth",
				"dt_sc_two_sixth","dt_sc_two_third","dt_sc_three_fourth","dt_sc_two_fifth","dt_sc_three_fifth","dt_sc_four_four",
				"dt_sc_four_fifth","dt_sc_three_sixth","dt_sc_four_sixth","dt_sc_five_sixth","dt_sc_one_half_inner",
				"dt_sc_one_third_inner","dt_sc_one_fourth_inner","dt_sc_one_fifth_inner","dt_sc_one_sixth_inner",
				"dt_sc_two_sixth_inner","dt_sc_two_third_inner","dt_sc_three_fourth_inner","dt_sc_two_fifth_inner",
				"dt_sc_three_fifth_inner","dt_sc_four_four_inner","dt_sc_three_sixth_inner","dt_sc_four_sixth_inner",
				"dt_sc_five_sixth_inner","dt_sc_four_fifth_inner","dt_sc_address","dt_sc_phone","dt_sc_mobile","dt_sc_fax","dt_sc_email","dt_sc_web"
				,"dt_sc_clear","dt_sc_hr_border","dt_sc_hr","dt_sc_hr_medium","dt_sc_hr_large","dt_sc_hr_invisible",
				"dt_sc_hr_invisible_medium","dt_sc_hr_invisible_large","dt_sc_hr_invisible_small","dt_sc_clients_carousel","dt_sc_donutchart_small","dt_sc_donutchart_medium",
				"dt_sc_donutchart_large","dt_sc_icon_box","dt_sc_icon_box_colored","dt_sc_dropcap","dt_sc_code",
				"dt_sc_fancy_ol","dt_sc_fancy_ul","dt_sc_pricing_table","dt_sc_pricing_table_item",
				"dt_sc_progressbar","dt_sc_tab","dt_sc_tabs_horizontal","dt_sc_tabs_vertical",
				"dt_sc_team","dt_sc_testimonial","dt_sc_testimonial_carousel","dt_sc_h1","dt_sc_h2",
				"dt_sc_h3","dt_sc_h4","dt_sc_h5","dt_sc_h6","dt_sc_title_with_icon","dt_sc_toggle","dt_sc_toggle_framed","dt_sc_titled_box",
				"dt_sc_tooltip","dt_sc_pullquote","dt_sc_portfolio_item","dt_sc_portfolios","dt_sc_infographic_bar","dt_sc_fullwidth_section",
				"dt_sc_fullwidth_video","dt_sc_animation","dt_sc_post","dt_sc_recent_post","dt_sc_teacher","dt_sc_courses_sensei","dt_sc_counter",
				"dt_sc_events","dt_sc_courses","dt_sc_courses_search","dt_sc_timeline_section","dt_sc_timeline","dt_sc_timeline_item",
				"dt_sc_subscription_form","dt_sc_subscribed_courses","dt_sc_newsletter_section","dt_sc_slider_search","dt_sc_widgets","dt_sc_doshortcode","dt_sc_resizable","dt_sc_resizable_inner");

			$block = join("|", $dt_shortcodes );
			// opening tag
			$rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/","[$2$3]",$content);

			// closing tag
			$rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)?/","[/$2]",$rep);
			
			return $rep;
		}

		/**
		 * Adds DesignThemes custom shortcode rich buttons to TinyMCE
		 *
		 * @param unknown $buttons        	
		 * @return unknown
		 */
		function dt_register_rich_buttons($buttons) {
			array_push ( $buttons, "|", "designthemes_sc_button" );
			return $buttons;
		}
		
		/**
		 * Adds DesignThemes javascript to TinyMCE
		 *
		 * @param unknown $plugins        	
		 * @return unknown
		 */
		function dt_add_external_plugins($plugins) {
			global $wp_version;
			
			if(  version_compare( $wp_version, '3.9' , '<') ) {
				$url = DESIGNTHEMES_TINYMCE_URL . '/plugin-wp-3.8.js';
			} else {
				$url = DESIGNTHEMES_TINYMCE_URL . '/plugin-wp-3.9.js';
			}
			$plugins ['DTCoreShortcodePlugin'] = $url;
			return $plugins;
		}
	}
}
?>