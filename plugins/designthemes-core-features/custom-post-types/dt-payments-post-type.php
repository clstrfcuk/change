<?php
if (! class_exists ( 'DTPaymentsPostType' )) {
	class DTPaymentsPostType {
		
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
			
			add_action ( 'admin_head', array (
					$this,
					'dt_hide_addnew_button'
			) );

		}
		
		
		/**
		 * A function hook that the WordPress core launches at 'init' points
		 */
		function dt_init() {
			$this->createPostType ();
		}
		
		/**
		 * A function hook that the WordPress core launches at 'admin_init' points
		 */
		function dt_admin_init() {
			wp_enqueue_script ( 'jquery-ui-sortable' );
						
			remove_filter( 'manage_posts_custom_column', 'likeThisDisplayPostLikes');
			
			add_action ( 'add_meta_boxes', array (
					$this,
					'dt_add_payment_meta_box' 
			) );
		}
		
		/**
		 * A function to hide add new button for this post type
		 */
		function dt_hide_addnew_button() {
			if('dt_payments' == get_post_type() || is_post_type_archive( 'dt_payments' ))
				echo '<style type="text/css">
						.add-new-h2{display:none;}
					</style>';
		}
		
		/**
		 */
		function createPostType() {
			$labels = array (
					'name' => __ ( 'Payments', 'dt_themes' ),
					'all_items' => __ ( 'All Payments', 'dt_themes' ),
					'singular_name' => __ ( 'Payment', 'dt_themes' ),
					'add_new' => __ ( 'Add New', 'dt_themes' ),
					'add_new_item' => __ ( 'Add New Payment', 'dt_themes' ),
					'edit_item' => __ ( 'Edit Payment', 'dt_themes' ),
					'new_item' => __ ( 'New Payment', 'dt_themes' ),
					'view_item' => __ ( 'View Payment', 'dt_themes' ),
					'search_items' => __ ( 'Search Payments', 'dt_themes' ),
					'not_found' => __ ( 'No Payments found', 'dt_themes' ),
					'not_found_in_trash' => __ ( 'No Payments found in Trash', 'dt_themes' ),
					'parent_item_colon' => __ ( 'Parent Payment:', 'dt_themes' ),
					'menu_name' => __ ( 'Payments', 'dt_themes' ) 
			);
			
			$args = array (
					'labels' => $labels,
					'hierarchical' => true,
					'description' => 'This is custom post type payments',
					'supports' => array ('title', 'author'),
					
					'public' => true,
					'show_ui' => true,
					'show_in_menu' => 'dt_lms',
					
					'show_in_nav_menus' => false,
					'publicly_queryable' => true,
					'exclude_from_search' => false,
					'has_archive' => true,
					'query_var' => true,
					'can_export' => true,
					'rewrite' => array( 'slug' => 'payments', 'hierarchical' => true, 'with_front' => false ),
					'capability_type' => 'post',
			);
			
			register_post_type ( 'dt_payments', $args );
				
		}
		
		/**
		 */
		function dt_add_payment_meta_box() {
			add_meta_box ( "dt-payment-default-metabox", __ ( 'Payment Options', 'dt_themes' ), array (
					$this,
					'dt_default_metabox' 
			), 'dt_payments', "normal", "default" );
		}
		
		/**
		 */
		function dt_default_metabox() {
			include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/dt_payment_default_metabox.php';
		}

	}
}
?>