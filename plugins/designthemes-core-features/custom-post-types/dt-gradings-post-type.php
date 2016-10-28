<?php
if (! class_exists ( 'DTGradingsPostType' )) {
	class DTGradingsPostType {
		
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
			add_action ( 'save_post', array (
					$this,
					'save_post_meta' 
			) );
			add_action ( 'before_delete_post', array (
					$this,
					'dt_before_delete_post_meta' 
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
					'dt_add_grading_meta_box' 
			) );
			
			add_filter ( "manage_edit-dt_gradings_columns", array (
					$this,
					"dt_gradings_edit_columns" 
			) );
			
			add_action ( "manage_pages_custom_column", array (
					$this,
					"dt_gradings_columns_display" 
			), 10, 2 );
			
		}
		
		/**
		 * A function to hide add new button for this post type
		 */
		function dt_hide_addnew_button() {
			if('dt_gradings' == get_post_type())
				echo '<style type="text/css">
						.add-new-h2{display:none;}
					</style>';
		}
		
		/**
		 */
		function createPostType() {
			$labels = array (
					'name' => __ ( 'Gradings', 'dt_themes' ),
					'all_items' => __ ( 'All Gradings', 'dt_themes' ),
					'singular_name' => __ ( 'Grading', 'dt_themes' ),
					'add_new' => __ ( 'Add New', 'dt_themes' ),
					'add_new_item' => __ ( 'Add New Grading', 'dt_themes' ),
					'edit_item' => __ ( 'Edit Grading', 'dt_themes' ),
					'new_item' => __ ( 'New Grading', 'dt_themes' ),
					'view_item' => __ ( 'View Grading', 'dt_themes' ),
					'search_items' => __ ( 'Search Gradings', 'dt_themes' ),
					'not_found' => __ ( 'No Gradings found', 'dt_themes' ),
					'not_found_in_trash' => __ ( 'No Gradings found in Trash', 'dt_themes' ),
					'parent_item_colon' => __ ( 'Parent Grading:', 'dt_themes' ),
					'menu_name' => __ ( 'Gradings', 'dt_themes' ) 
			);
			
			$args = array (
					'labels' => $labels,
					'hierarchical' => true,
					'description' => 'This is custom post type gradings',
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
					'rewrite' => array( 'slug' => 'gradings', 'hierarchical' => true, 'with_front' => false ),
					'capability_type' => 'post',
			);
			
			register_post_type ( 'dt_gradings', $args );
		}
		
		/**
		 */
		function dt_add_grading_meta_box() {
			add_meta_box ( "dt-grading-default-metabox", __ ( 'Grading Options', 'dt_themes' ), array (
					$this,
					'dt_default_metabox' 
			), 'dt_gradings', "normal", "default" );
		}
		
		/**
		 */
		function dt_default_metabox() {
			include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/dt_grading_default_metabox.php';
		}

		
		/**
		 *
		 * @param unknown $columns
		 * @return multitype:
		 */
		function dt_gradings_edit_columns($columns) {
			$newcolumns = array (
				"cb" => "<input type=\"checkbox\" />",
				"title" => "Title",
				"learner" => "Learner",
				"course" => "Course",
				"lesson" => "Lesson",
				"grade" => "Grade",
				"status" => "Status",
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
		function dt_gradings_columns_display($columns, $id) {
			global $post;
			
			switch ($columns) {
				
				case "learner":
						$user_id = get_post_meta ( $id, 'dt-user-id', TRUE );
						if(isset($user_id) && $user_id >= 0) {
							$user_info = get_userdata($user_id);
							echo $user_info->display_name;
						}
				break;

				case "course":
						$course_id = get_post_meta ( $id, "dt-course-id",true);
						if(isset($course_id) && $course_id >= 0 && $course_id  != '') {
							$course_args = array( 'post_type' => 'dt_courses', 'p' => $course_id );
							$course = get_posts( $course_args );
							echo $course[0]->post_title;
						}
				break;
				
				case "lesson":
						$lesson_id = get_post_meta ( $id, "dt-lesson-id",true);
						if(isset($lesson_id) && $lesson_id >= 0 && $lesson_id  != '') {
							$lesson_args = array( 'post_type' => 'dt_lessons', 'p' => $lesson_id );
							$lesson = get_posts( $lesson_args );
							echo $lesson[0]->post_title;
						}
				break;
				
				case "grade":
						$dt_marks_obtained = get_post_meta ( $id, "marks-obtained",true); 
						$dt_marks_obtained = (isset($dt_marks_obtained) && $dt_marks_obtained > 0) ? $dt_marks_obtained : 0;
						$dt_marks_obtained_percent = get_post_meta ( $id, "marks-obtained-percent",true); 
						$dt_marks_obtained_percent = (isset($dt_marks_obtained_percent) && $dt_marks_obtained_percent > 0) ? $dt_marks_obtained_percent : 0;
						if($dt_marks_obtained >= 0) {
							echo $dt_marks_obtained.' ('.$dt_marks_obtained_percent.'%)';
						}
				break;
				
				case "status":
						$mark_as_graded = get_post_meta ( $id, "graded",true);
						if(isset($mark_as_graded) && $mark_as_graded != '') {
							echo __('Graded', 'dt_themes');
						} else {
							echo __('Ungraded', 'dt_themes');
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
		
			if(isset($_POST['dt-marks-obtained']) && $_POST['dt-marks-obtained'] >= 0) :
			
				
				if( isset( $_POST ['dt-marks-obtained'] ) && $_POST ['dt-marks-obtained'] != '' ) update_post_meta ( $post_id, "marks-obtained", stripslashes ( $_POST ['dt-marks-obtained'] ) );
				else delete_post_meta ( $post_id, "marks-obtained" );

				if( isset( $_POST ['dt-marks-obtained-percent'] ) && $_POST ['dt-marks-obtained-percent'] != '' ) update_post_meta ( $post_id, "marks-obtained-percent", stripslashes ( $_POST ['dt-marks-obtained-percent'] ) );
				else delete_post_meta ( $post_id, "marks-obtained-percent" );
				
				$quiz_id = get_post_meta ( $post_id, 'dt-quiz-id', TRUE );
				$quiz_question = get_post_meta ( $quiz_id, "quiz-question",true);
				
				foreach($quiz_question as $question_id) {
				
					if(isset($_POST['dt-question-id-'.$question_id.'-grade']) && $_POST ['dt-question-id-'.$question_id.'-grade'] == true) update_post_meta ($post_id, 'question-id-'.$question_id.'-grade', stripslashes ($_POST['dt-question-id-'.$question_id.'-grade']));
					else delete_post_meta ( $post_id, 'question-id-'.$question_id.'-grade' );
				
				}
				
				if( isset( $_POST ['allow-retakes'] ) && $_POST ['allow-retakes'] != '' ) update_post_meta ( $post_id, "allow-retakes", stripslashes ( $_POST ['allow-retakes'] ) );
				else delete_post_meta ( $post_id, "allow-retakes" );
		
				if( isset( $_POST ['graded'] ) && $_POST ['graded'] != '' ) update_post_meta ( $post_id, "graded", stripslashes ( $_POST ['graded'] ) );
				else delete_post_meta ( $post_id, "graded" );
		
			endif;
				
				
		}
		
		function dt_before_delete_post_meta($post_id) {
				
			if (!current_user_can('delete_posts'))
		        return;

			$attachment_id = get_post_meta ( $post_id, "dt-attachment-id", true);
			wp_delete_attachment( $attachment_id, true );
				
		}
		
		
	}
}
?>