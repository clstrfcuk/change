<?php
if (! class_exists ( 'DTLessonsPostType' )) {
	class DTLessonsPostType {
		
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
					'dt_add_lesson_meta_box' 
			) );
			
			add_filter ( "manage_edit-dt_lessons_columns", array (
					$this,
					"dt_lessons_edit_columns" 
			) );
			
			add_action ( "manage_pages_custom_column", array (
					$this,
					"dt_lessons_columns_display" 
			), 10, 2 );
			
			add_filter( 'manage_edit-dt_lessons_sortable_columns', array (
					$this,
					'dt_lesson_sortable_columns' 
			));
			
		}
		
		function dt_lesson_sortable_columns( $columns ) {
			
			$custom = array(
				'dt_course' => 'dt_lesson_course',
				'dt_teacher' => 'lesson-teacher',
			);
			return wp_parse_args($custom, $columns);
				
		}	
					
		/**
		 */
		function createPostType() {
			
			if(dttheme_option('dt_course','single-lesson-slug') != '') $lesson_slug = trim(stripslashes(dttheme_option('dt_course','single-lesson-slug')));
			else $lesson_slug = 'lessons';
			
			if(dttheme_option('dt_course','lesson-category-slug') != '') $lesson_cat_slug = trim(stripslashes(dttheme_option('dt_course','lesson-category-slug')));
			else $lesson_cat_slug = 'lesson-complexity';

			$labels = array (
					'name' => __ ( 'Lessons', 'dt_themes' ),
					'all_items' => __ ( 'All Lessons', 'dt_themes' ),
					'singular_name' => __ ( 'Lesson', 'dt_themes' ),
					'add_new' => __ ( 'Add New', 'dt_themes' ),
					'add_new_item' => __ ( 'Add New Lesson', 'dt_themes' ),
					'edit_item' => __ ( 'Edit Lesson', 'dt_themes' ),
					'new_item' => __ ( 'New Lesson', 'dt_themes' ),
					'view_item' => __ ( 'View Lesson', 'dt_themes' ),
					'search_items' => __ ( 'Search Lessons', 'dt_themes' ),
					'not_found' => __ ( 'No Lessons found', 'dt_themes' ),
					'not_found_in_trash' => __ ( 'No Lessons found in Trash', 'dt_themes' ),
					'parent_item_colon' => __ ( 'Parent Lesson:', 'dt_themes' ),
					'menu_name' => __ ( 'Lessons', 'dt_themes' ) 
			);
			
			$args = array (
					'labels' => $labels,
					'hierarchical' => true,
					'description' => 'This is custom post type lessons',
					'supports' => array (
							'title',
							'editor',
							'excerpt',
							'author',
							'page-attributes',
							'comments',
							'thumbnail'
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
					'rewrite' => array( 'slug' => $lesson_slug, 'hierarchical' => true, 'with_front' => false ),
					'capability_type' => 'post' 
			);
			
			register_post_type ( 'dt_lessons', $args );
				
			register_taxonomy ( "lesson_complexity", array (
						"dt_lessons" 
				), array (
						"hierarchical" => true,
						"labels" => array(
							"name" 					=> __("Lesson Complexities",'dt_themes' ),
							"singular_name" 		=> __("Lesson Complexity",'dt_themes' ),
							'search_items'			=> __( 'Search Lesson Complexities', 'dt_themes' ),
							'popular_items'			=> __( 'Popular Lesson Complexities', 'dt_themes' ),
							'all_items'				=> __( 'All Lesson Complexities', 'dt_themes' ),
							'parent_item'			=> __( 'Parent Lesson Complexity', 'dt_themes' ),
							'parent_item_colon'		=> __( 'Parent Lesson Complexity', 'dt_themes' ),
							'edit_item'				=> __( 'Edit Lesson Complexity', 'dt_themes' ),
							'update_item'			=> __( 'Update Lesson Complexity', 'dt_themes' ),
							'add_new_item'			=> __( 'Add New Lesson Complexity', 'dt_themes' ),
							'new_item_name'			=> __( 'New Lesson Complexity', 'dt_themes' ),
							'add_or_remove_items'	=> __( 'Add or remove', 'dt_themes' ),
							'choose_from_most_used'	=> __( 'Choose from most used', 'dt_themes' ),
							'menu_name'				=> __( 'Lesson Complexities','dt_themes' ),
						),
						"show_admin_column" => true,
						"rewrite" => array( 'slug' => $lesson_cat_slug, 'hierarchical' => true, 'with_front' => false ),
						"query_var" => true 
				) 
			);
						
		}
		
		/**
		 */
		function dt_add_lesson_meta_box() {
			add_meta_box ( "dt-lesson-default-metabox", __ ( 'Lesson Options', 'dt_themes' ), array (
					$this,
					'dt_default_metabox' 
			), 'dt_lessons', "normal", "default" );
		}
		
		/**
		 */
		function dt_default_metabox() {
			include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/dt_lesson_default_metabox.php';
		}

		
		/**
		 *
		 * @param unknown $columns
		 * @return multitype:
		 */
		function dt_lessons_edit_columns($columns) {
			$newcolumns = array (
				"cb" => "<input type=\"checkbox\" />",
				"title" => "Title",
				"dt_course" => "Course",
				"dt_teacher" => "Teacher",
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
		function dt_lessons_columns_display($columns, $id) {
			global $post;
			
			switch ($columns) {
				
				case "dt_course":
					$dt_lesson_course = get_post_meta ( $id, "dt_lesson_course",true);
					if(isset($dt_lesson_course) && $dt_lesson_course != '') {
						$post_data = get_post($dt_lesson_course);
						echo $post_data->post_title;
					}
				break;
				
				case "dt_teacher":
					$lesson_teacher = get_post_meta ( $id, "lesson-teacher",true);
					if($lesson_teacher != '') {
						$post_data = get_post($lesson_teacher);
						echo $post_data->post_title.' - '.$lesson_teacher;
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
	
				$settings ['lesson-duration'] = isset ( $_POST ['lesson-duration'] ) ? stripslashes ( $_POST ['lesson-duration'] ) : "";
				$settings ['private-lesson'] = isset(  $_POST['private-lesson'] ) ? $_POST['private-lesson']: "";
								
				update_post_meta ( $post_id, "_lesson_settings", array_filter ( $settings ) );
				
				if( isset( $_POST ['lesson-teacher'] ) && $_POST ['lesson-teacher'] != '' ){
					update_post_meta ( $post_id, "lesson-teacher", stripslashes ( $_POST ['lesson-teacher'] ) );
					
				} else {
					delete_post_meta ( $post_id, "lesson-teacher" );
				}
				
				if( isset( $_POST ['dt-lesson-course'] ) && $_POST ['dt-lesson-course'] != '' ){
					update_post_meta ( $post_id, "dt_lesson_course", stripslashes ( $_POST ['dt-lesson-course'] ) );
				} else {
					delete_post_meta ( $post_id, "dt_lesson_course" );
				}
				
				if( isset( $_POST ['lesson-video'] ) && $_POST ['lesson-video'] != ''){
					update_post_meta ( $post_id, "lesson-video", stripslashes ( $_POST ['lesson-video'] ) );
				} else {
					delete_post_meta ( $post_id, "lesson-video" );
				}
				
				if( isset( $_POST ['dt-lesson-quiz'] ) && $_POST ['dt-lesson-quiz'] != '' ){
					update_post_meta ( $post_id, "lesson-quiz", stripslashes ( $_POST ['dt-lesson-quiz'] ) );
				} else {
					delete_post_meta ( $post_id, "lesson-quiz" );
				}
				
			endif;
		}
		
		/**
		 * To load lesson pages in front end
		 *
		 * @param string $template        	
		 * @return string
		 */
		function dt_template_include($template) {
			if (is_singular( 'dt_lessons' )) {
				if (! file_exists ( get_stylesheet_directory () . '/single-dt_lessons.php' )) {
					$template = plugin_dir_path ( __FILE__ ) . 'templates/single-dt_lessons.php';
				}
			} elseif (is_tax ( 'lesson_complexity' )) {
				if (! file_exists ( get_stylesheet_directory () . '/taxonomy-lesson_complexity.php' )) {
					$template = plugin_dir_path ( __FILE__ ) . 'templates/taxonomy-lesson_complexity.php';
				}
			} elseif( is_post_type_archive('dt_lessons') ) {
				if (! file_exists ( get_stylesheet_directory () . '/archive-dt_lessons.php' )) {
					$template = plugin_dir_path ( __FILE__ ) . 'templates/archive-dt_lessons.php';
				}
			}
			return $template;
		}
	}
}
?>