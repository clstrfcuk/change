<?php
if (! class_exists ( 'DTQuizesPostType' )) {
	class DTQuizesPostType {
		
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
					'dt_add_quiz_meta_box' 
			) );
			
			add_filter ( "manage_edit-dt_quizes_columns", array (
					$this,
					"dt_quizes_edit_columns" 
			) );
			
			add_action ( "manage_pages_custom_column", array (
					$this,
					"dt_quizes_columns_display" 
			), 10, 2 );
		}
		
		/**
		 */
		function createPostType() {
			
			if(dttheme_option('dt_course','single-quiz-slug') != '') $quiz_slug = trim(stripslashes(dttheme_option('dt_course','single-quiz-slug')));
			else $quiz_slug = 'quizes';
			
			$labels = array (
					'name' => __ ( 'Quizes', 'dt_themes' ),
					'all_items' => __ ( 'All Quizes', 'dt_themes' ),
					'singular_name' => __ ( 'Quiz', 'dt_themes' ),
					'add_new' => __ ( 'Add New', 'dt_themes' ),
					'add_new_item' => __ ( 'Add New Quiz', 'dt_themes' ),
					'edit_item' => __ ( 'Edit Quiz', 'dt_themes' ),
					'new_item' => __ ( 'New Quiz', 'dt_themes' ),
					'view_item' => __ ( 'View Quiz', 'dt_themes' ),
					'search_items' => __ ( 'Search Quizes', 'dt_themes' ),
					'not_found' => __ ( 'No Quizes found', 'dt_themes' ),
					'not_found_in_trash' => __ ( 'No Quizes found in Trash', 'dt_themes' ),
					'parent_item_colon' => __ ( 'Parent Quiz:', 'dt_themes' ),
					'menu_name' => __ ( 'Quizes', 'dt_themes' ) 
			);
			
			$args = array (
					'labels' => $labels,
					'hierarchical' => true,
					'description' => 'This is custom post type quizes',
					'supports' => array (
							'title',
							'editor',
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
					'rewrite' => array( 'slug' => $quiz_slug, 'hierarchical' => true, 'with_front' => false ),
					'capability_type' => 'post' 
			);
			
			register_post_type ( 'dt_quizes', $args );
				
		}
		
		/**
		 */
		function dt_add_quiz_meta_box() {
			add_meta_box ( "dt-quiz-default-metabox", __ ( 'Quiz Options', 'dt_themes' ), array (
					$this,
					'dt_default_metabox' 
			), 'dt_quizes', "normal", "default" );
		}
		
		/**
		 */
		function dt_default_metabox() {
			include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/dt_quiz_default_metabox.php';
		}

		
		/**
		 *
		 * @param unknown $columns
		 * @return multitype:
		 */
		function dt_quizes_edit_columns($columns) {
			$newcolumns = array (
				"cb" => "<input type=\"checkbox\" />",
				"title" => "Title",
				"dt_lesson" => "Lesson",
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
		function dt_quizes_columns_display($columns, $id) {
			global $post;
			
			switch ($columns) {
				
				case "dt_lesson":
				
					$lesson_args = array('post_type' => 'dt_lessons', 'meta_key' => 'lesson-quiz', 'meta_value' => $post->ID, 'hierarchical' => 0 );
					$lessons = get_pages( $lesson_args );
					
					if(isset($lessons[0])) {
						echo $lessons[0]->post_title;
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
				
				update_post_meta ( $post_id, "_quiz_settings", array_filter ( $settings ) );
				
				if( isset( $_POST ['quiz-subtitle'] ) && $_POST ['quiz-subtitle'] != '' ) update_post_meta ( $post_id, "quiz-subtitle", stripslashes ( $_POST ['quiz-subtitle'] ) );
				else delete_post_meta ( $post_id, "quiz-subtitle" );

				if( isset( $_POST ['quiz-duration'] ) && $_POST ['quiz-duration'] != '' ) update_post_meta ( $post_id, "quiz-duration", stripslashes ( $_POST ['quiz-duration'] ) );
				else delete_post_meta ( $post_id, "quiz-duration" );

				if( isset( $_POST ['quiz-retakes'] ) && $_POST ['quiz-retakes'] != '' ) update_post_meta ( $post_id, "quiz-retakes", stripslashes ( $_POST ['quiz-retakes'] ) );
				else delete_post_meta ( $post_id, "quiz-retakes" );

				if( isset( $_POST ['quiz-postmsg'] ) && $_POST ['quiz-postmsg'] != '' ) update_post_meta ( $post_id, "quiz-postmsg", stripslashes ( $_POST ['quiz-postmsg'] ) );
				else delete_post_meta ( $post_id, "quiz-postmsg" );

				if( isset( $_POST ['quiz-randomize-questions'] ) && $_POST ['quiz-randomize-questions'] != '' ) update_post_meta ( $post_id, "quiz-randomize-questions", stripslashes ( $_POST ['quiz-randomize-questions'] ) );
				else delete_post_meta ( $post_id, "quiz-randomize-questions" );

				if( isset( $_POST ['quiz-auto-evaluation'] ) && $_POST ['quiz-auto-evaluation'] != '' ) update_post_meta ( $post_id, "quiz-auto-evaluation", stripslashes ( $_POST ['quiz-auto-evaluation'] ) );
				else delete_post_meta ( $post_id, "quiz-auto-evaluation" );

				if( isset( $_POST ['quiz-passmark-percentage'] ) && $_POST ['quiz-passmark-percentage'] != '' ) update_post_meta ( $post_id, "quiz-passmark-percentage", stripslashes ( $_POST ['quiz-passmark-percentage'] ) );
				else delete_post_meta ( $post_id, "quiz-passmark-percentage" );

				if( isset( $_POST ['dt-quiz-question'] ) && $_POST ['dt-quiz-question'] != '' ) update_post_meta ( $post_id, "quiz-question", array_filter ( $_POST ['dt-quiz-question'] ) );
				else delete_post_meta ( $post_id, "quiz-question" );

				if( isset( $_POST ['dt-quiz-question-grade'] ) && $_POST ['dt-quiz-question-grade'] != '' ) update_post_meta ( $post_id, "quiz-question-grade", array_filter ( $_POST ['dt-quiz-question-grade'] ) );
				else delete_post_meta ( $post_id, "quiz-question-grade" );

				if( isset( $_POST ['dt-quiz-total-grade'] ) && $_POST ['dt-quiz-total-grade'] != '' ) update_post_meta ( $post_id, "quiz-total-grade", stripslashes ( $_POST ['dt-quiz-total-grade'] ) );
				else delete_post_meta ( $post_id, "quiz-total-grade" );			
				
				if( isset( $_POST ['quiz-pass-percentage'] ) && $_POST ['quiz-pass-percentage'] != '' ) update_post_meta ( $post_id, "quiz-pass-percentage", stripslashes ( $_POST ['quiz-pass-percentage'] ) );
				else delete_post_meta ( $post_id, "quiz-pass-percentage" );				
				
			endif;
		}
		
		/**
		 * To load quiz pages in front end
		 *
		 * @param string $template        	
		 * @return string
		 */
		function dt_template_include($template) {
			if (is_singular( 'dt_quizes' )) {
				if (! file_exists ( get_stylesheet_directory () . '/single-dt_quizes.php' )) {
					$template = plugin_dir_path ( __FILE__ ) . 'templates/single-dt_quizes.php';
				}
			}
			return $template;
		}
	}
}
?>