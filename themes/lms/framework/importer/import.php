<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/** ---------------------------------------------------------------------------
 * Import Demo Data
 * @author DesignThemes
 * @version 1.1
 * ---------------------------------------------------------------------------- */
class dtthemesImport {

	public $error	= '';

	// TODO: replace below with function getReplaceURL( $demo )
	public $urls	= array(
		'lms'	=> 'http://wedesignthemes.com/themes/dummy-lms/'
	);


	/** ---------------------------------------------------------------------------
	 * Import | Content
	 * ---------------------------------------------------------------------------- */
	function import_content( $file = 'default/all.xml.gz' ){
		$import = new WP_Import();
		$xml = IAMD_TD . '/framework/importer/demo/'. $file;

		$import->fetch_attachments = ( $_REQUEST && key_exists('attachments', $_REQUEST['data']) && $_REQUEST['data']['attachments'] ) ? true : false;

		ob_start();
		$import->import( $xml );
		ob_end_clean();
	}
	
	
	/** ---------------------------------------------------------------------------
	 * Import | Menu - Locations 
	 * ---------------------------------------------------------------------------- */
	function import_menu_location( $file = 'default/menu.txt' ){
		$file_path 	= IAMD_THEME_URI . '/framework/importer/demo/'. $file;
		$file_data 	= wp_remote_get( $file_path );
		$data 		= unserialize( base64_decode( $file_data['body']));
		$menus 		= wp_get_nav_menus();
			
		foreach( $data as $key => $val ){
			foreach( $menus as $menu ){
				if( $val && $menu->slug == $val ){
					$data[$key] = absint( $menu->term_id );
				}
			}
		}
		
		set_theme_mod( 'nav_menu_locations', $data );
	}
	
	
	/** ---------------------------------------------------------------------------
	 * Import | Theme Options
	 * ---------------------------------------------------------------------------- */
	function import_options( $file = 'default/options.txt', $url = false ){
		$file_path 	= IAMD_THEME_URI . '/framework/importer/demo/'. $file;
		$file_data 	= wp_remote_get( $file_path );
		$data 		= unserialize( base64_decode( $file_data['body'] ) );

		// images URL | replace exported URL with destination URL
		if( $url &&  is_array( $data ) ){
			$replace = esc_url( home_url('/') );
			foreach( $data as $key => $option ){
				if( is_string( $option ) ){						// variable type string only
					$data[$key] = str_replace( $url, $replace, $option );
				}
			}
		}

		update_option( IAMD_THEME_SETTINGS, $data );
	}
	
	
	/** ---------------------------------------------------------------------------
	 * Import | Widgets
	 * ---------------------------------------------------------------------------- */
	function import_widget( $file = 'default/widget_data.json' ){
		$file_path 	= IAMD_THEME_URI . '/framework/importer/demo/'. $file;
		$file_data 	= wp_remote_get( $file_path );
		$data 		= $file_data['body'];
	
		$this->import_widget_data( $data );
	}
	

	/** ---------------------------------------------------------------------------
	 * Import | Migrate CB DesignThemes Builder
	 * ---------------------------------------------------------------------------- */

	// FIX | Multisite 'uploads' directory url
	function migrate_cb_ms( $field ){
		if ( is_multisite() ){
			global $current_blog;
			if( $current_blog->blog_id > 1 ){
				$old_url 	= '/wp-content/uploads/';
				$new_url 	= '/wp-content/uploads/sites/'. $current_blog->blog_id .'/';
				$field 		= str_replace( $old_url, $new_url, $field );
			}
		}
		return $field;
	}

	function migrate_cb( $old_url ){
		global $wpdb;
		
		$new_url = esc_url( home_url('/') );
		
		$results = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta
			WHERE `meta_key` = 'dtthemes-page-items'
		" );
		
		// posts loop -----------------
		if( is_array( $results ) ){
			foreach( $results as $result_key => $result ){
				$meta_id = $result->meta_id;
				$meta_value = unserialize( base64_decode( $result->meta_value ) );
		
				// print_r($meta_value);
		
				// sections loop ----------------
				if( is_array( $meta_value ) ){
					foreach( $meta_value as $sec_key => $sec ){
							
						// section attr loop ----------------
						if( is_array( $sec['attr'] ) ){
							foreach( $sec['attr'] as $attr_key => $attr ){
								$attr = str_replace( $old_url, $new_url, $attr );
								$meta_value[$sec_key]['attr'][$attr_key] = $attr;
							}
						}
							
						// items loop ----------------
						if( is_array( $sec['items'] ) ){
							foreach( $sec['items'] as $item_key => $item ){
				
								// meta fields loop ----------------
								if( is_array( $item['fields'] ) ){
									foreach( $item['fields'] as $field_key => $field ) {
											
										if( $field_key == 'tabs' ) {
											// tabs, accordion, faq, timeline
					
											// tabs loop --------------------
											if( is_array( $field ) ){
												foreach( $field as $tab_key => $tab ){
													$field = str_replace( $old_url, $new_url, $tab['content'] );
													$field = $this->migrate_cb_ms( $field );
													$meta_value[$sec_key]['items'][$item_key]['fields'][$field_key][$tab_key]['content'] = $field;
												}
											}
										} else {
											// default
											$field = str_replace( $old_url, $new_url, $field );
											$field = $this->migrate_cb_ms( $field );
											$meta_value[$sec_key]['items'][$item_key]['fields'][$field_key] = $field;
										}
									}
								}
								
							}
						}
						
					}
				}
				
				$meta_value = base64_encode( serialize( $meta_value ) );
				$wpdb->query( "UPDATE $wpdb->postmeta
					SET `meta_value` = '" . addslashes( $meta_value ) . "'
					WHERE `meta_key` = 'dtthemes-page-items'
					AND `meta_id`= " . $meta_id . "
				" );
			}
		}
	}
	
	
	/** ---------------------------------------------------------------------------
	 * Import
	 * ---------------------------------------------------------------------------- */
	function import(){
		global $wpdb;
		
		// Importer classes
		if( ! defined( 'WP_LOAD_IMPORTERS' ) ) define( 'WP_LOAD_IMPORTERS', true );
		
		if( ! class_exists( 'WP_Importer' ) ){
			require_once ABSPATH . 'wp-admin/includes/class-wp-importer.php';
		}
		
		if( ! class_exists( 'WP_Import' ) ){
			require_once IAMD_TD . '/framework/importer/wordpress-importer.php';
		}
		
		if( class_exists( 'WP_Importer' ) && class_exists( 'WP_Import' ) ){
			
			$import_demo = ($_REQUEST['data']['demo'] != '') ? $_REQUEST['data']['demo'] : 'default';
			switch( $_REQUEST['data']['import'] ) {
				
				case 'all':
					// Full Demo Data ---------------------------------
					$this->import_content($import_demo.'/all.xml.gz');
					$this->import_menu_location($import_demo.'/menu.txt');
					$this->import_options($import_demo.'/options.txt', $this->urls['lms']);
					$this->import_widget($import_demo.'/widget_data.json');
					
					// set home & blog page
					$home = get_page_by_title( 'Home' );
					$blog = get_page_by_title( 'Blog' );
					if( $home->ID && $blog->ID ) {
						update_option('show_on_front', 'page');
						update_option('page_on_front', $home->ID); // Front Page
						update_option('page_for_posts', $blog->ID); // Blog Page
					}
					break;
				
				case 'content':
					if( $_REQUEST['data']['content'] ){
						$_REQUEST['data']['content'] = htmlspecialchars( stripslashes( $_REQUEST['data']['content'] ) );
						$file = $import_demo.'/content/'. $_REQUEST['data']['content'] .'.xml.gz';
						$this->import_content( $file );
					} else {
						$this->import_content($import_demo.'/all.xml.gz');
					}
					break;
					
				case 'menu':
					// Menu -------------------------------------------
					$this->import_content( $import_demo.'/menu.xml.gz' );
					$this->import_menu_location($import_demo.'/menu.txt');
					break;
					
				case 'options':
					// Theme Options ----------------------------------
					$this->import_options($import_demo.'/options.txt', $this->urls['lms']);
					break;
					
				case 'widgets':
					// Widgets ----------------------------------------
					$this->import_widget($import_demo.'/widget_data.json');
					break;
					
				default:
					// Empty select.import
					$this->error = __('Please select data to import.','dt_themes');	
					break;
			}
			
			// message box
			if( $this->error ){
					echo '<strong>'. $this->error .'</strong>';
			} else {
				echo '<strong>'. __('All done. Have fun!','dt_themes') .'</strong>';
			}
		}
		die(0);
	}
	
	
	/** ---------------------------------------------------------------------------
	 * Parse JSON import file
	 * http://wordpress.org/plugins/widget-settings-importexport/
	 * ---------------------------------------------------------------------------- */
	function import_widget_data( $json_data ) {
	
		$json_data 		= json_decode( $json_data, true );
		$sidebar_data 	= $json_data[0];
		$widget_data 	= $json_data[1];	
	
		// prepare widgets table
		$widgets = array();
		foreach( $widget_data as $k_w => $widget_type ){
			if( $k_w ){
				$widgets[ $k_w ] = array();
				foreach( $widget_type as $k_wt => $widget ){
					if( is_int( $k_wt ) ) $widgets[$k_w][$k_wt] = 1;
				}
			}
		}

		// sidebars
		foreach ( $sidebar_data as $title => $sidebar ) {
			$count = count( $sidebar );
			for ( $i = 0; $i < $count; $i++ ) {
				$widget = array( );
				$widget['type'] = trim( substr( $sidebar[$i], 0, strrpos( $sidebar[$i], '-' ) ) );
				$widget['type-index'] = trim( substr( $sidebar[$i], strrpos( $sidebar[$i], '-' ) + 1 ) );
				if ( !isset( $widgets[$widget['type']][$widget['type-index']] ) ) {
					unset( $sidebar_data[$title][$i] );
				}
			}
			$sidebar_data[$title] = array_values( $sidebar_data[$title] );
		}
	
		// widgets
		foreach ( $widgets as $widget_title => $widget_value ) {
			foreach ( $widget_value as $widget_key => $widget_value ) {
				$widgets[$widget_title][$widget_key] = $widget_data[$widget_title][$widget_key];
			}
		}
		
		$sidebar_data = array( array_filter( $sidebar_data ), $widgets );
		$this->parse_import_data( $sidebar_data );
	}
	
	/** ---------------------------------------------------------------------------
	 * Import widgets
	 * http://wordpress.org/plugins/widget-settings-importexport/
	 * ---------------------------------------------------------------------------- */
	function parse_import_data( $import_array ) {
		$sidebars_data 		= $import_array[0];
		$widget_data 		= $import_array[1];
		
		//dtthemes_register_sidebars(); // fix for sidebars added in Theme Options
		$current_sidebars 	= get_option( 'sidebars_widgets' );
		$new_widgets 		= array( );

		foreach ( $sidebars_data as $import_sidebar => $import_widgets ) :
	
			foreach ( $import_widgets as $import_widget ) :
			
				// if NOT the sidebar exists
				if ( ! isset( $current_sidebars[$import_sidebar] ) ){
					$current_sidebars[$import_sidebar] = array();
				}

				$title = trim( substr( $import_widget, 0, strrpos( $import_widget, '-' ) ) );
				$index = trim( substr( $import_widget, strrpos( $import_widget, '-' ) + 1 ) );
				$current_widget_data = get_option( 'widget_' . $title );
				$new_widget_name = $this->get_new_widget_name( $title, $index );
				$new_index = trim( substr( $new_widget_name, strrpos( $new_widget_name, '-' ) + 1 ) );
			
				if ( !empty( $new_widgets[ $title ] ) && is_array( $new_widgets[$title] ) ) {
					while ( array_key_exists( $new_index, $new_widgets[$title] ) ) {
						$new_index++;
					}
				}
				$current_sidebars[$import_sidebar][] = $title . '-' . $new_index;
				if ( array_key_exists( $title, $new_widgets ) ) {
					$new_widgets[$title][$new_index] = $widget_data[$title][$index];
					
					// notice fix
					if( ! key_exists('_multiwidget',$new_widgets[$title]) ) $new_widgets[$title]['_multiwidget'] = '';
					
					$multiwidget = $new_widgets[$title]['_multiwidget'];
					unset( $new_widgets[$title]['_multiwidget'] );
					$new_widgets[$title]['_multiwidget'] = $multiwidget;
				} else {
					$current_widget_data[$new_index] = $widget_data[$title][$index];
					
					// notice fix
					if( ! key_exists('_multiwidget',$current_widget_data) ) $current_widget_data['_multiwidget'] = '';
					
					$current_multiwidget = $current_widget_data['_multiwidget'];
					$new_multiwidget = isset($widget_data[$title]['_multiwidget']) ? $widget_data[$title]['_multiwidget'] : false;
					$multiwidget = ($current_multiwidget != $new_multiwidget) ? $current_multiwidget : 1;
					unset( $current_widget_data['_multiwidget'] );
					$current_widget_data['_multiwidget'] = $multiwidget;
					$new_widgets[$title] = $current_widget_data;
				}
				
			endforeach;
		endforeach;
	
		if ( isset( $new_widgets ) && isset( $current_sidebars ) ) {
			update_option( 'sidebars_widgets', $current_sidebars );
	
			foreach ( $new_widgets as $title => $content )
				update_option( 'widget_' . $title, $content );
	
			return true;
		}
	
		return false;
	}
	
	
	/** ---------------------------------------------------------------------------
	 * Get new widget name
	 * http://wordpress.org/plugins/widget-settings-importexport/
	 * ---------------------------------------------------------------------------- */
	function get_new_widget_name( $widget_name, $widget_index ) {
		$current_sidebars = get_option( 'sidebars_widgets' );
		$all_widget_array = array( );
		foreach ( $current_sidebars as $sidebar => $widgets ) {
			if ( !empty( $widgets ) && is_array( $widgets ) && $sidebar != 'wp_inactive_widgets' ) {
				foreach ( $widgets as $widget ) {
					$all_widget_array[] = $widget;
				}
			}
		}
		while ( in_array( $widget_name . '-' . $widget_index, $all_widget_array ) ) {
			$widget_index++;
		}
		$new_widget_name = $widget_name . '-' . $widget_index;
		return $new_widget_name;
	}
	
}

$dtthemes_import = new dtthemesImport;
$dtthemes_import->import();
?>