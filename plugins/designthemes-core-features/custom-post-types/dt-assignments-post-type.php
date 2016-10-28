<?php
if (! class_exists ( 'DTAssignmentsPostType' )) {
	class DTAssignmentsPostType {
		
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
					'dt_add_assignment_meta_box' 
			) );
			
		}
		
		/**
		 */
		function createPostType() {
			
			if(dttheme_option('dt_course','single-assignment-slug') != '') $assignment_slug = trim(stripslashes(dttheme_option('dt_course','single-assignment-slug')));
			else $assignment_slug = 'assignments';
			
			$labels = array (
					'name' => __ ( 'Assignments', 'dt_themes' ),
					'all_items' => __ ( 'All Assignments', 'dt_themes' ),
					'singular_name' => __ ( 'Assignment', 'dt_themes' ),
					'add_new' => __ ( 'Add New', 'dt_themes' ),
					'add_new_item' => __ ( 'Add New Assignment', 'dt_themes' ),
					'edit_item' => __ ( 'Edit Assignment', 'dt_themes' ),
					'new_item' => __ ( 'New Assignment', 'dt_themes' ),
					'view_item' => __ ( 'View Assignment', 'dt_themes' ),
					'search_items' => __ ( 'Search Assignments', 'dt_themes' ),
					'not_found' => __ ( 'No Assignments found', 'dt_themes' ),
					'not_found_in_trash' => __ ( 'No Assignments found in Trash', 'dt_themes' ),
					'parent_item_colon' => __ ( 'Parent Assignment:', 'dt_themes' ),
					'menu_name' => __ ( 'Assignments', 'dt_themes' ) 
			);
			
			$args = array (
					'labels' => $labels,
					'hierarchical' => true,
					'description' => 'This is custom post type assignments',
					'supports' => array (
							'title',
							'editor',
							'author',
							'comments',
					),
					
					'public' => true,
					'show_ui' => true,
					'show_in_menu' => 'dt_lms',
					
					'show_in_nav_menus' => false,
					'publicly_queryable' => true,
					'exclude_from_search' => false,
					'has_archive' => true,
					'query_var' => true,
					'can_export' => true,
					'rewrite' => array( 'slug' => $assignment_slug, 'hierarchical' => true, 'with_front' => false ),
					'capability_type' => 'post' 
			);
			
			register_post_type ( 'dt_assignments', $args );
				
		}
		
		/**
		 */
		function dt_add_assignment_meta_box() {
			add_meta_box ( "dt-assignment-default-metabox", __ ( 'Assignment Options', 'dt_themes' ), array (
					$this,
					'dt_default_metabox' 
			), 'dt_assignments', "normal", "default" );
		}
		
		/**
		 */
		function dt_default_metabox() {
			include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/dt_assignment_default_metabox.php';
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
		
			if(isset($_POST['assignment-subtitle'])) :
			
				if( isset( $_POST ['assignment-subtitle'] ) && $_POST ['assignment-subtitle'] != '' ) update_post_meta ( $post_id, "assignment-subtitle", stripslashes ( $_POST ['assignment-subtitle'] ) );
				else delete_post_meta ( $post_id, "assignment-subtitle" );

				if( isset( $_POST ['assignment-maximum-mark'] ) && $_POST ['assignment-maximum-mark'] != '' ) update_post_meta ( $post_id, "assignment-maximum-mark", stripslashes ( $_POST ['assignment-maximum-mark'] ) );
				else delete_post_meta ( $post_id, "assignment-maximum-mark" );

				if( isset( $_POST ['assignment-enable-textarea'] ) && $_POST ['assignment-enable-textarea'] != '' ) update_post_meta ( $post_id, "assignment-enable-textarea", stripslashes ( $_POST ['assignment-enable-textarea'] ) );
				else delete_post_meta ( $post_id, "assignment-enable-textarea" );

				if( isset( $_POST ['assignment-enable-attachment'] ) && $_POST ['assignment-enable-attachment'] != '' ) update_post_meta ( $post_id, "assignment-enable-attachment", stripslashes ( $_POST ['assignment-enable-attachment'] ) );
				else delete_post_meta ( $post_id, "assignment-enable-attachment" );
				
				if( isset( $_POST ['assignment-attachment-type'] ) && $_POST ['assignment-attachment-type'] != '' ) update_post_meta ( $post_id, "assignment-attachment-type",  $_POST ['assignment-attachment-type'] );
				else delete_post_meta ( $post_id, "assignment-attachment-type" );
				
				if($_POST ['assignment-attachment-size'] > dt_get_upload_size()) $attachment_size = 0; else $attachment_size = $_POST ['assignment-attachment-size'];
				
				if( isset( $_POST ['assignment-attachment-size'] ) && $_POST ['assignment-attachment-size'] != '' ) update_post_meta ( $post_id, "assignment-attachment-size", $attachment_size );
				else delete_post_meta ( $post_id, "assignment-attachment-size" );
				
				if( isset( $_POST ['assignment-course-evaluation'] ) && $_POST ['assignment-course-evaluation'] != '' ) update_post_meta ( $post_id, "assignment-course-evaluation",  $_POST ['assignment-course-evaluation'] );
				else delete_post_meta ( $post_id, "assignment-course-evaluation" );
				
				if( isset( $_POST ['dt-assignment-course'] ) && $_POST ['dt-assignment-course'] != '' ) update_post_meta ( $post_id, "dt-assignment-course",  $_POST ['dt-assignment-course'] );
				else delete_post_meta ( $post_id, "dt-assignment-course" );
				
			endif;
		}
		
		/**
		 * To load assignment pages in front end
		 *
		 * @param string $template        	
		 * @return string
		 */
		function dt_template_include($template) {
			if (is_singular( 'dt_assignments' )) {
				if (! file_exists ( get_stylesheet_directory () . '/single-dt_assignments.php' )) {
					$template = plugin_dir_path ( __FILE__ ) . 'templates/single-dt_assignments.php';
				}
			}
			return $template;
		}
	}
}
?>