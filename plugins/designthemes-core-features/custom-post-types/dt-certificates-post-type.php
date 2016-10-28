<?php
if (! class_exists ( 'DTCertificatesPostType' )) {
	class DTCertificatesPostType {
		
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
			
			add_action ( 'wp_head', array (
					$this,
					'dt_wp_head' 
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
					'dt_add_certificate_meta_box' 
			) );
			
		}
		
		/**
		 */
		function createPostType() {
			$labels = array (
					'name' => __ ( 'Certificates', 'dt_themes' ),
					'all_items' => __ ( 'All Certificates', 'dt_themes' ),
					'singular_name' => __ ( 'Certificate', 'dt_themes' ),
					'add_new' => __ ( 'Add New', 'dt_themes' ),
					'add_new_item' => __ ( 'Add New Certificate', 'dt_themes' ),
					'edit_item' => __ ( 'Edit Certificate', 'dt_themes' ),
					'new_item' => __ ( 'New Certificate', 'dt_themes' ),
					'view_item' => __ ( 'View Certificate', 'dt_themes' ),
					'search_items' => __ ( 'Search Certificates', 'dt_themes' ),
					'not_found' => __ ( 'No Certificates found', 'dt_themes' ),
					'not_found_in_trash' => __ ( 'No Certificates found in Trash', 'dt_themes' ),
					'parent_item_colon' => __ ( 'Parent Certificate:', 'dt_themes' ),
					'menu_name' => __ ( 'Certificates', 'dt_themes' ) 
			);
			
			$args = array (
					'labels' => $labels,
					'hierarchical' => true,
					'description' => 'This is custom post type certificates',
					'supports' => array (
							'title',
							'editor',
							'author',
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
					'rewrite' => array( 'slug' => 'certificates', 'hierarchical' => true, 'with_front' => false ),
					'capability_type' => 'post' 
			);
			
			register_post_type ( 'dt_certificates', $args );
				
		}
		
		/**
		 */
		function dt_add_certificate_meta_box() {
			add_meta_box ( "dt-certificate-default-metabox", __ ( 'Certificate Options', 'dt_themes' ), array (
					$this,
					'dt_default_metabox' 
			), 'dt_certificates', "normal", "default" );
		}
		
		/**
		 */
		function dt_default_metabox() {
			include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/dt_certificate_default_metabox.php';
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
		
			if(isset($_POST['background-image'])) :
			
				if( isset( $_POST ['background-image'] ) && $_POST ['background-image'] != '' ) update_post_meta ( $post_id, "background-image", stripslashes ( $_POST ['background-image'] ) );
				else delete_post_meta ( $post_id, "background-image" );

				if( isset( $_POST ['custom-class'] ) && $_POST ['custom-class'] != '' ) update_post_meta ( $post_id, "custom-class", stripslashes ( $_POST ['custom-class'] ) );
				else delete_post_meta ( $post_id, "custom-class" );

				if( isset( $_POST ['custom-css'] ) && $_POST ['custom-css'] != '' ) update_post_meta ( $post_id, "custom-css", stripslashes ( $_POST ['custom-css'] ) );
				else delete_post_meta ( $post_id, "custom-css" );

				if( isset( $_POST ['enable-print'] ) && $_POST ['enable-print'] != '' ) update_post_meta ( $post_id, "enable-print", stripslashes ( $_POST ['enable-print'] ) );
				else delete_post_meta ( $post_id, "enable-print" );

			endif;
				
				
		}
		
		function dt_wp_head() {
			
			if(get_post() != '') {
				
				$output = get_post_meta( get_the_ID(), 'custom-css', true );;
		
				if (!empty($output)) :
					$output = "\r".'<style type="text/css">'."\r".$output."\r".'</style>'."\r";
					echo $output;
				endif;
				
			}
			
		}
		
		/**
		 * To load certificate pages in front end
		 *
		 * @param string $template        	
		 * @return string
		 */
		function dt_template_include($template) {
			if (is_singular( 'dt_certificates' )) {
				if (! file_exists ( get_stylesheet_directory () . '/single-dt_certificates.php' )) {
					$template = plugin_dir_path ( __FILE__ ) . 'templates/single-dt_certificates.php';
				}
			}
			return $template;
		}
	}
}
?>