<?php
if (! class_exists ( 'DTCoursesPostType' )) {
	class DTCoursesPostType {
		
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
					'dt_add_course_meta_box' 
			) );
			
			add_filter ( "manage_edit-dt_courses_columns", array (
					$this,
					"dt_courses_edit_columns" 
			) );
			
			add_action ( "manage_posts_custom_column", array (
					$this,
					"dt_courses_columns_display" 
			), 10, 2 );
		}
		
		/**
		 */
		function createPostType() {
			
			if(dttheme_option('dt_course','single-course-slug') != '') $course_slug = trim(stripslashes(dttheme_option('dt_course','single-course-slug')));
			else $course_slug = 'courses';
			
			if(dttheme_option('dt_course','course-category-slug') != '') $course_cat_slug = trim(stripslashes(dttheme_option('dt_course','course-category-slug')));
			else $course_cat_slug = 'coursecategory';

			$labels = array (
					'name' => __ ( 'Courses', 'dt_themes' ),
					'all_items' => __ ( 'All Courses', 'dt_themes' ),
					'singular_name' => __ ( 'Course', 'dt_themes' ),
					'add_new' => __ ( 'Add New', 'dt_themes' ),
					'add_new_item' => __ ( 'Add New Course', 'dt_themes' ),
					'edit_item' => __ ( 'Edit Course', 'dt_themes' ),
					'new_item' => __ ( 'New Course', 'dt_themes' ),
					'view_item' => __ ( 'View Course', 'dt_themes' ),
					'search_items' => __ ( 'Search Courses', 'dt_themes' ),
					'not_found' => __ ( 'No Courses found', 'dt_themes' ),
					'not_found_in_trash' => __ ( 'No Courses found in Trash', 'dt_themes' ),
					'parent_item_colon' => __ ( 'Parent Course:', 'dt_themes' ),
					'menu_name' => __ ( 'Courses', 'dt_themes' ) 
			);
			
			$args = array (
					'labels' => $labels,
					'hierarchical' => false,
					'description' => 'This is custom post type courses',
					'supports' => array (
							'title',
							'editor',
							'excerpt',
							'author',
							'comments',
							'page-attributes',
							'thumbnail'
					),
					
					'public' => true,
					'show_ui' => true,
					'show_in_menu' => 'dt_lms',
					
					'show_in_nav_menus' => true,
					'publicly_queryable' => true,
					'exclude_from_search' => false,
					'has_archive' => true,
					'query_var' => true,
					'can_export' => true,
					'rewrite' => array( 'slug' => $course_slug, 'hierarchical' => true, 'with_front' => false ),
					'capability_type' => 'post' 
			);
			
			register_post_type ( 'dt_courses', $args );
			
			register_taxonomy ( "course_category", array (
						"dt_courses" 
				), array (
						"hierarchical" => true,
						"labels" => array(
							"name" 					=> __("Course Categories",'dt_themes' ),
							"singular_name" 		=> __("Course Category",'dt_themes' ),
							'search_items'			=> __( 'Search Course Categories', 'dt_themes' ),
							'popular_items'			=> __( 'Popular Course Categories', 'dt_themes' ),
							'all_items'				=> __( 'All Course Categories', 'dt_themes' ),
							'parent_item'			=> __( 'Parent Course Category', 'dt_themes' ),
							'parent_item_colon'		=> __( 'Parent Course Category', 'dt_themes' ),
							'edit_item'				=> __( 'Edit Course Category', 'dt_themes' ),
							'update_item'			=> __( 'Update Course Category', 'dt_themes' ),
							'add_new_item'			=> __( 'Add New Course Category', 'dt_themes' ),
							'new_item_name'			=> __( 'New Course Category', 'dt_themes' ),
							'add_or_remove_items'	=> __( 'Add or remove', 'dt_themes' ),
							'choose_from_most_used'	=> __( 'Choose from most used', 'dt_themes' ),
							'menu_name'				=> __( 'Course Categories','dt_themes' ),
						),
						"show_admin_column" => true,
						"rewrite" => array( 'slug' => $course_cat_slug, 'hierarchical' => true, 'with_front' => false ),
						"query_var" => true 
				) 
			);
						
		}
		
		/**
		 */
		function dt_add_course_meta_box() {
			add_meta_box ( "dt-course-default-metabox", __ ( 'Courses Options', 'dt_themes' ), array (
					$this,
					'dt_default_metabox' 
			), 'dt_courses', "normal", "default" );
		}
		
		/**
		 */
		function dt_default_metabox() {
			include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/dt_course_default_metabox.php';
		}
		
		/**
		 *
		 * @param unknown $columns
		 * @return multitype:
		 */
		function dt_courses_edit_columns($columns) {
			$newcolumns = array (
				"cb" => "<input type=\"checkbox\" />",
				"dt_course_thumb" => "Image",
				"title" => "Title",
				"taxonomy-course_category" => "Course Category",
				"lessons-count" => "Lessons",
				"date" => "Date"
			);
			$columns = array_merge ( $newcolumns, $columns );
			return $columns;
		}
		
		/**
		 *
		 * @param unknown $columns
		 * @param unknown $id        	
		 */
		function dt_courses_columns_display($columns, $id) {
			global $post;
			
			switch ($columns) {
				
				case "dt_course_thumb":
				    $image = wp_get_attachment_image(get_post_thumbnail_id($id), array(75,75));
					if(!empty($image))
					  	echo $image;
					else
						echo '<img src="http://placehold.it/75x75" alt="'.$id.'" />';
				break;

				case "lessons-count":
		
					$count = 0;
					$lesson_args = array( 'post_type' => 'dt_lessons', 'posts_per_page' => -1, 'meta_key' => 'dt_lesson_course', 'meta_value' => $id );
					$lessons_array = get_pages( $lesson_args );
					
					if(isset($lessons_array)) {
					
						$count = count($lessons_array);
						echo $count;
					
					}
					
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
				
				$settings ['referrrence_url'] = isset ( $_POST ['referrrence_url'] ) ? stripslashes ( $_POST ['referrrence_url'] ) : "";
				$settings ['show-social-share'] = isset ( $_POST ['mytheme-social-share'] ) ? $_POST ['mytheme-social-share'] : "";
				$settings ['disable-staffs'] = isset ( $_POST ['mytheme-disable-staffs'] ) ? $_POST ['mytheme-disable-staffs'] : "";
				$settings ['show-related-course'] = isset ( $_POST ['mytheme-related-course'] ) ? $_POST ['mytheme-related-course'] : "";
								
				update_post_meta ( $post_id, "_course_settings", array_filter ( $settings ) );
				
				if( isset( $_POST ['course-video'] ) && $_POST ['course-video'] != ''){
					update_post_meta ( $post_id, "course-video", stripslashes ( $_POST ['course-video'] ) );
				} else {
					delete_post_meta ( $post_id, "course-video" );
				}
				if( isset( $_POST ['featured-course'] ) && $_POST ['featured-course'] != ''){
					update_post_meta ( $post_id, "featured-course", stripslashes ( $_POST ['featured-course'] ) );
				} else {
					delete_post_meta ( $post_id, "featured-course" );
				}
				if( isset( $_POST ['starting-price'] ) && $_POST ['starting-price'] != ''){
					update_post_meta ( $post_id, "starting-price", stripslashes ( $_POST ['starting-price'] ) );
				} else {
					delete_post_meta ( $post_id, "starting-price" );
				}

				if( isset( $_POST ['enable-certificate'] ) && $_POST ['enable-certificate'] != ''){
					update_post_meta ( $post_id, "enable-certificate", stripslashes ( $_POST ['enable-certificate'] ) );
				} else {
					delete_post_meta ( $post_id, "enable-certificate" );
				}
				
				if( isset( $_POST ['certificate-percentage'] ) && $_POST ['certificate-percentage'] != ''){
					update_post_meta ( $post_id, "certificate-percentage", stripslashes ( $_POST ['certificate-percentage'] ) );
				} else {
					delete_post_meta ( $post_id, "certificate-percentage" );
				}
				
				if( isset( $_POST ['certificate-template'] ) && $_POST ['certificate-template'] != ''){
					update_post_meta ( $post_id, "certificate-template", stripslashes ( $_POST ['certificate-template'] ) );
				} else {
					delete_post_meta ( $post_id, "certificate-template" );
				}

				if( isset( $_POST ['enable-badge'] ) && $_POST ['enable-badge'] != ''){
					update_post_meta ( $post_id, "enable-badge", stripslashes ( $_POST ['enable-badge'] ) );
				} else {
					delete_post_meta ( $post_id, "enable-badge" );
				}
				
				if( isset( $_POST ['badge-percentage'] ) && $_POST ['badge-percentage'] != ''){
					update_post_meta ( $post_id, "badge-percentage", stripslashes ( $_POST ['badge-percentage'] ) );
				} else {
					delete_post_meta ( $post_id, "badge-percentage" );
				}
				
				if( isset( $_POST ['badge-title'] ) && $_POST ['badge-title'] != ''){
					update_post_meta ( $post_id, "badge-title", stripslashes ( $_POST ['badge-title'] ) );
				} else {
					delete_post_meta ( $post_id, "badge-title" );
				}
				
				if( isset( $_POST ['badge-image'] ) && $_POST ['badge-image'] != ''){
					update_post_meta ( $post_id, "badge-image", stripslashes ( $_POST ['badge-image'] ) );
				} else {
					delete_post_meta ( $post_id, "badge-image" );
				}
				
				if( isset( $_POST ['media-attachments'] ) && !empty($_POST ['media-attachments'])){
					update_post_meta ( $post_id, "media-attachments", $_POST ['media-attachments'] );
				} else {
					delete_post_meta ( $post_id, "media-attachments" );
				}

			endif;
		}
		
		/**
		 * To load course pages in front end
		 *
		 * @param string $template        	
		 * @return string
		 */
		function dt_template_include($template) {
			if (is_singular( 'dt_courses' )) {
				if (! file_exists ( get_stylesheet_directory () . '/single-dt_courses.php' )) {
					$template = plugin_dir_path ( __FILE__ ) . 'templates/single-dt_courses.php';
				}
			} elseif (is_tax ( 'course_category' )) {
				if (! file_exists ( get_stylesheet_directory () . '/taxonomy-course_category.php' )) {
					$template = plugin_dir_path ( __FILE__ ) . 'templates/taxonomy-course_category.php';
				}
			} elseif( is_post_type_archive('dt_courses') ) {
				if (! file_exists ( get_stylesheet_directory () . '/archive-dt_courses.php' )) {
					$template = plugin_dir_path ( __FILE__ ) . 'templates/archive-dt_courses.php';
				}
			}
			return $template;
		}
	}
}
?>