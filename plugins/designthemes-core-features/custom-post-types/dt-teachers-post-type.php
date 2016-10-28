<?php
if (! class_exists ( 'DTTeachersPostType' )) {
	class DTTeachersPostType {
		
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
		}
		
		/**
		 * A function hook that the WordPress core launches at 'admin_init' points
		 */
		function dt_admin_init() {
			wp_enqueue_script ( 'jquery-ui-sortable' );
						
			remove_filter( 'manage_posts_custom_column', 'likeThisDisplayPostLikes');
			
			add_action ( 'add_meta_boxes', array (
					$this,
					'dt_add_teacher_meta_box' 
			) );
			
			add_filter ( "manage_edit-dt_teachers_columns", array (
					$this,
					"dt_teachers_edit_columns" 
			) );
			
			add_action ( "manage_posts_custom_column", array (
					$this,
					"dt_teachers_columns_display" 
			), 10, 2 );
		}
		
		/**
		 */
		function createPostType() {
			$labels = array (
					'name' => __ ( 'Teachers', 'dt_themes' ),
					'all_items' => __ ( 'All Teachers', 'dt_themes' ),
					'singular_name' => __ ( 'Teacher', 'dt_themes' ),
					'add_new' => __ ( 'Add New', 'dt_themes' ),
					'add_new_item' => __ ( 'Add New Teacher', 'dt_themes' ),
					'edit_item' => __ ( 'Edit Teacher', 'dt_themes' ),
					'new_item' => __ ( 'New Teacher', 'dt_themes' ),
					'view_item' => __ ( 'View Teacher', 'dt_themes' ),
					'search_items' => __ ( 'Search Teachers', 'dt_themes' ),
					'not_found' => __ ( 'No Teachers found', 'dt_themes' ),
					'not_found_in_trash' => __ ( 'No Teachers found in Trash', 'dt_themes' ),
					'parent_item_colon' => __ ( 'Parent Teacher:', 'dt_themes' ),
					'menu_name' => __ ( 'Teachers', 'dt_themes' ) 
			);
			
			$args = array (
					'labels' => $labels,
					'hierarchical' => false,
					'description' => 'This is custom post type teachers',
					'supports' => array (
							'title',
							'editor',
							'excerpt',
							'comments',
							'thumbnail'
					),
					
					'public' => true,
					'show_ui' => true,
					'show_in_menu' => true,
					'menu_position' => 5,
					'menu_icon' => 'dashicons-businessman',
					
					'show_in_nav_menus' => true,
					'publicly_queryable' => true,
					'exclude_from_search' => false,
					'has_archive' => true,
					'query_var' => true,
					'can_export' => true,
					'rewrite' => array( 'slug' => 'teachers', 'hierarchical' => true, 'with_front' => false ),
					'capability_type' => 'post' 
			);
			
			register_post_type ( 'dt_teachers', $args );
		}
		
		/**
		 */
		function dt_add_teacher_meta_box() {
			add_meta_box ( "dt-teacher-default-metabox", __ ( 'Teacher Options', 'dt_themes' ), array (
					$this,
					'dt_default_metabox' 
			), 'dt_teachers', "normal", "default" );
		}
		
		/**
		 */
		function dt_default_metabox() {
			include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/dt_teacher_default_metabox.php';
		}
		
		/**
		 *
		 * @param unknown $columns
		 * @return multitype:
		 */
		function dt_teachers_edit_columns($columns) {
			$newcolumns = array (
				"cb" => "<input type=\"checkbox\" />",
				"dt_teacher_thumb" => "Image",
				"title" => "Title",
				'rating' => "Rating",
				"role"	=> "Role",
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
		function dt_teachers_columns_display($columns, $id) {
			global $post;
			
			switch ($columns) {
				
				case "dt_teacher_thumb":
				    $image = wp_get_attachment_image(get_post_thumbnail_id($id), array(75,75));
					if(!empty($image))
					  	echo $image;
				break;
				
				case "rating":
					if(function_exists('the_ratings')) { echo do_shortcode('[ratings id="'.$id.'" results="true"]'); }
				break;
				
				case "role":
					$meta = get_post_meta ( $id, '_teacher_settings', TRUE );
					if (isset($meta['role']) && $meta['role'] != '')
					  echo $meta['role'];
				break;
			}
		}
		
		/**
		 */
		function save_post_meta($post_id) {
			if (defined ( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE)
				return $post_id;
				
			if (!current_user_can('edit_posts'))
		        return;

		    if (!isset($id))
		        $id = (int) $post_id;
		
			if(isset($_POST['layout'])) :
			
				$settings = array ();
				
				$settings ['role'] = isset ( $_POST ['_role'] ) ? stripslashes ( $_POST ['_role'] ) : "";
				$settings ['url'] = isset ( $_POST ['_url'] ) ? stripslashes ( $_POST ['_url'] ) : "";
				
				$settings ['exp'] = isset ( $_POST ['_exp'] ) ? stripslashes ( $_POST ['_exp'] ) : "";
				$settings ['special'] = isset ( $_POST ['_special'] ) ? stripslashes ( $_POST ['_special'] ) : "";
				
				$settings ['layout'] = isset ( $_POST ['layout'] ) ? $_POST ['layout'] : "";
				if($_POST['layout'] == 'both-sidebar') {
					$settings['disable-everywhere-sidebar-left'] = $_POST['disable-everywhere-sidebar-left'];
					$settings['disable-everywhere-sidebar-right'] = $_POST['disable-everywhere-sidebar-right'];
					$settings['widget-area-left'] =  array_unique(array_filter($_POST['mytheme']['widgetareas-left']));
					$settings['widget-area-right'] =  array_unique(array_filter($_POST['mytheme']['widgetareas-right']));
				} elseif($_POST['layout'] == 'with-left-sidebar') {
					$settings['disable-everywhere-sidebar-left'] = $_POST['disable-everywhere-sidebar-left'];
					$settings['widget-area-left'] =  array_unique(array_filter($_POST['mytheme']['widgetareas-left']));
				} elseif($_POST['layout'] == 'with-right-sidebar') {
					$settings['disable-everywhere-sidebar-right'] = $_POST['disable-everywhere-sidebar-right'];
					$settings['widget-area-right'] =  array_unique(array_filter($_POST['mytheme']['widgetareas-right']));
				} 
				
				$settings ['show-social-share'] = isset ( $_POST ['mytheme-social-share'] ) ? $_POST ['mytheme-social-share'] : "";
				$settings ['comment'] = isset ( $_POST ['mytheme-teacher-comment'] ) ? $_POST ['mytheme-teacher-comment'] : "";
				
				$settings ['teacher-social'] = isset($_POST['social']) ? array_filter($_POST['social']) : '';
				
				update_post_meta ( $post_id, "_teacher_settings", array_filter ( $settings ) );
				
			endif;
		}
		
		/**
		 * To load teacher pages in front end
		 *
		 * @param string $template        	
		 * @return string
		 */
		function dt_template_include($template) {
			if (is_singular( 'dt_teachers' )) {
				if (! file_exists ( get_stylesheet_directory () . '/single-dt_teachers.php' )) {
					$template = plugin_dir_path ( __FILE__ ) . 'templates/single-dt_teachers.php';
				}
			} elseif( is_post_type_archive( 'dt_teachers' ) ) {
				if (! file_exists ( get_stylesheet_directory () . '/archive-dt_teachers.php' )) {
					$template = plugin_dir_path ( __FILE__ ) . 'templates/archive-dt_teachers.php';
				}
			}
			return $template;
		}
	}
}
?>