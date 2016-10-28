<?php
if (! class_exists ( 'DTPortfolioPostType' )) {
	class DTPortfolioPostType {
		
		/**
		 */
		function __construct() {
			// Add Hook into the 'init()' action
			add_action ( 'init', array (
					$this,
					'dt_init' 
			) );
			
			// Add Hook into the 'admin_init()' action
			add_action ( 'admin_init', array (
					$this,
					'dt_admin_init' 
			) );
			
			add_filter ( 'template_include', array (
					$this,
					'dt_template_include' 
			) );
		}
		
		/**
		 * A function hook that the WordPress core launches at 'init' points
		 */
		function dt_init() {
			$this->createPostType ();
			add_action ( 'save_post', array (
					$this,
					'save_post_meta' 
			) );
			
			add_action ( 'pre_post_update', array (
					$this,
					'save_post_meta' 
			) );
		}
		
		/**
		 * A function hook that the WordPress core launches at 'admin_init' points
		 */
		function dt_admin_init() {
			wp_enqueue_script ( 'jquery-ui-sortable' );
			
			remove_filter( 'manage_posts_custom_column', 'likeThisDisplayPostLikes'); # Fix for http://wordpress.org/plugins/roses-like-this/
			
			add_action ( 'add_meta_boxes', array (
					$this,
					'dt_add_portfolio_meta_box' 
			) );
			
			add_filter ( "manage_edit-dt_portfolios_columns", array (
					$this,
					"dt_portfolios_edit_columns" 
			) );
			
			add_action ( "manage_posts_custom_column", array (
					$this,
					"dt_portfolios_columns_display" 
			), 10, 2 );
		}
		
		/**
		 */
		function createPostType() {
			$labels = array (
					'name' => __ ( 'Portfolios', 'dt_themes' ),
					'all_items' => __ ( 'All Portfolios', 'dt_themes' ),
					'singular_name' => __ ( 'Portfolio', 'dt_themes' ),
					'add_new' => __ ( 'Add New', 'dt_themes' ),
					'add_new_item' => __ ( 'Add New Portfolio', 'dt_themes' ),
					'edit_item' => __ ( 'Edit Portfolio', 'dt_themes' ),
					'new_item' => __ ( 'New Portfolio', 'dt_themes' ),
					'view_item' => __ ( 'View Portfolio', 'dt_themes' ),
					'search_items' => __ ( 'Search Portfolios', 'dt_themes' ),
					'not_found' => __ ( 'No portfolios found', 'dt_themes' ),
					'not_found_in_trash' => __ ( 'No portfolios found in Trash', 'dt_themes' ),
					'parent_item_colon' => __ ( 'Parent Portfolio:', 'dt_themes' ),
					'menu_name' => __ ( 'Portfolios', 'dt_themes' ) 
			);
			
			$args = array (
					'labels' => $labels,
					'hierarchical' => false,
					'description' => 'This is custom post type portfolios',
					'supports' => array (
							'title',
							'editor',
							'excerpt',
							'comments' 
					),
					
					'public' => true,
					'show_ui' => true,
					'show_in_menu' => true,
					'menu_position' => 5,
					'menu_icon' => plugin_dir_url ( __FILE__ ) . 'images/icon_portfolio.png',
					
					'show_in_nav_menus' => true,
					'publicly_queryable' => true,
					'exclude_from_search' => false,
					'has_archive' => true,
					'query_var' => true,
					'can_export' => true,
					'rewrite' => array( 'slug' => 'portfolios', 'hierarchical' => true, 'with_front' => false ),
					'capability_type' => 'page' 
			);
			
			register_post_type ( 'dt_portfolios', $args );
			
			register_taxonomy ( "portfolio_entries", array (
					"dt_portfolios" 
			), array (
					"hierarchical" => true,
					"label" => "Categories",
					"singular_label" => "Category",
					"show_admin_column" => true,
					"rewrite" => array( 'slug' => 'portfolio-categories', 'hierarchical' => true, 'with_front' => false ),
					"query_var" => true 
			) );
		}
		
		/**
		 */
		function dt_add_portfolio_meta_box() {
			add_meta_box ( "dt-portfolio-default-metabox", __ ( 'Portfolio Options', 'dt_themes' ), array (
					$this,
					'dt_default_metabox' 
			), 'dt_portfolios', "normal", "default" );
		}
		
		/**
		 */
		function dt_default_metabox() {
			include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/dt_portdolio_default_metabox.php';
		}
		
		/**
		 *
		 * @param unknown $columns        	
		 * @return multitype:
		 */
		function dt_portfolios_edit_columns($columns) {
			$newcolumns = array (
					"cb" => "<input type=\"checkbox\" />",
					"thumb column-comments" => "Image",
					"title" => "Title",
					"author" => "Author"					
			);
			$columns = array_merge ( $newcolumns, $columns );
			return $columns;
		}
		
		/**
		 *
		 * @param unknown $columns        	
		 * @param unknown $id        	
		 */
		function dt_portfolios_columns_display($columns, $id) {
			global $post;
			switch ($columns) {
				
				case "thumb column-comments" :
					$portfolio_settings = get_post_meta ( $post->ID, '_portfolio_settings', TRUE );
					$portfolio_settings = is_array ( $portfolio_settings ) ? $portfolio_settings : array ();
					
					if (array_key_exists ( "items_thumbnail", $portfolio_settings )) {
						$item = $portfolio_settings ['items_thumbnail'] [0];
						$name = $portfolio_settings ['items_name'] [0];
						
						if ("video" === $name) {
							echo '<span class="dt-video"></span>';
						} else {
							echo "<img src='{$item}' height='60px' width='60px' alt='' />";
						}
					}
					break;

				case "likes":
					$likes = get_post_meta($post->ID, "_likes");
					if ($likes) {
					  echo $likes[0];
					} else {
					  echo 0;
					}
				break;
					
			}
		}
		
		/**
		 */
		function save_post_meta($post_id) {
			if (defined ( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE)
				return $post_id;
			
			$settings = array ();
			
			if(isset($_POST['layout'])) :
				
				$settings ['sub-title'] = isset ( $_POST ['sub-title'] ) ? stripslashes ( $_POST ['sub-title'] ) : "";
				
				$settings ['client-name'] = isset ( $_POST ['client-name'] ) ? stripslashes ( $_POST ['client-name'] ) : "";
				$settings ['website-link'] = isset ( $_POST ['website-link'] ) ? stripslashes ( $_POST ['website-link'] ) : "";
				
				
				$settings ['layout'] = isset ( $_POST ['layout'] ) ? $_POST ['layout'] : "";
				$settings ['show-social-share'] = isset ( $_POST ['mytheme-social-share'] ) ? $_POST ['mytheme-social-share'] : "";
				$settings ['show-related-items'] = isset ( $_POST ['mytheme-related-item'] ) ? $_POST ['mytheme-related-item'] : "";
				
				$settings ['items'] = isset ( $_POST ['items'] ) ? $_POST ['items'] : "";
				$settings ['items_thumbnail'] = isset ( $_POST ['items_thumbnail'] ) ? $_POST ['items_thumbnail'] : "";
				$settings ['items_name'] = isset ( $_POST ['items_name'] ) ? $_POST ['items_name'] : "";
				
				update_post_meta ( $post_id, "_portfolio_settings", array_filter ( $settings ) );
				
				/* TO set Default Category */
				$terms = wp_get_object_terms ( $post_id, 'portfolio_entries' );
				if (empty ( $terms )) :
					wp_set_object_terms ( $post_id, 'Uncategorized', 'portfolio_entries', true );
				endif;
			
			endif;
			
		}
		
		/**
		 * To load portfolio pages in front end
		 *
		 * @param string $template        	
		 * @return string
		 */
		function dt_template_include($template) {
			if (is_singular () && 'dt_portfolios' === get_post_type ()) {
				
				if (! file_exists ( get_stylesheet_directory () . '/single-dt_portfolios.php' )) {
					$template = plugin_dir_path ( __FILE__ ) . 'templates/single-dt_portfolios.php';
				}
			} elseif (is_tax ( 'portfolio_entries' )) {
				if (! file_exists ( get_stylesheet_directory () . '/taxonomy-portfolio_entries.php' )) {
					$template = plugin_dir_path ( __FILE__ ) . 'templates/taxonomy-portfolio_entries.php';
				}
			} elseif( is_post_type_archive( 'dt_portfolios' ) ) {
				if (! file_exists ( get_stylesheet_directory () . '/archive-dt_portfolios.php' )) {
					$template = plugin_dir_path ( __FILE__ ) . 'templates/archive-dt_portfolios.php';
				}
			}
			
			return $template;
		}
	}
}
?>