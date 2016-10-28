<?php
if (! class_exists ( 'DTQuestionsPostType' )) {
	class DTQuestionsPostType {
		
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
					'dt_add_question_meta_box' 
			) );
			
		}
		
		/**
		 */
		function createPostType() {
			
			if(dttheme_option('dt_course','single-question-slug') != '') $question_slug = trim(stripslashes(dttheme_option('dt_course','single-question-slug')));
			else $question_slug = 'questions';
			
			$labels = array (
					'name' => __ ( 'Questions', 'dt_themes' ),
					'all_items' => __ ( 'All Questions', 'dt_themes' ),
					'singular_name' => __ ( 'Question', 'dt_themes' ),
					'add_new' => __ ( 'Add New', 'dt_themes' ),
					'add_new_item' => __ ( 'Add New Question', 'dt_themes' ),
					'edit_item' => __ ( 'Edit Question', 'dt_themes' ),
					'new_item' => __ ( 'New Question', 'dt_themes' ),
					'view_item' => __ ( 'View Question', 'dt_themes' ),
					'search_items' => __ ( 'Search Questions', 'dt_themes' ),
					'not_found' => __ ( 'No Questions found', 'dt_themes' ),
					'not_found_in_trash' => __ ( 'No Questions found in Trash', 'dt_themes' ),
					'parent_item_colon' => __ ( 'Parent Question:', 'dt_themes' ),
					'menu_name' => __ ( 'Questions', 'dt_themes' ) 
			);
			
			$args = array (
					'labels' => $labels,
					'hierarchical' => true,
					'description' => 'This is custom post type questions',
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
					'rewrite' => array( 'slug' => $question_slug, 'hierarchical' => true, 'with_front' => false ),
					'capability_type' => 'post' 
			);
			
			register_post_type ( 'dt_questions', $args );
				
		}
		
		/**
		 */
		function dt_add_question_meta_box() {
			add_meta_box ( "dt-question-default-metabox", __ ( 'Question Options', 'dt_themes' ), array (
					$this,
					'dt_default_metabox' 
			), 'dt_questions', "normal", "default" );
		}
		
		/**
		 */
		function dt_default_metabox() {
			include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/dt_question_default_metabox.php';
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
		
			if(isset($_POST['dt_question_type'])) :
			
				if( isset( $_POST ['dt_question_type'] ) && $_POST ['dt_question_type'] != '' ) update_post_meta ( $post_id, "question-type", stripslashes ( $_POST ['dt_question_type'] ) );
				else delete_post_meta ( $post_id, "question-type" );

				if($_POST ['dt_question_type'] == 'multiple-choice') {
					
					if( isset( $_POST ['dt_multichoice_answers'] ) && !empty($_POST ['dt_multichoice_answers']) ) update_post_meta ( $post_id, "multichoice-answers", array_filter($_POST ['dt_multichoice_answers']) );
					else delete_post_meta ( $post_id, "multichoice-answers" );
					
					$dt_multichoice_answers = $_POST ['dt_multichoice_answers'];
					$dt_multichoice_correct_answers = $dt_multichoice_answers[$_POST ['dt-multichoice-correct-answer']];
					
					if( isset( $dt_multichoice_correct_answers ) && !empty($dt_multichoice_correct_answers) ) update_post_meta ( $post_id, "multichoice-correct-answer", $dt_multichoice_correct_answers );
					else delete_post_meta ( $post_id, "multichoice-correct-answer" );
					
					delete_post_meta ( $post_id, "multicorrect-answers" );
					delete_post_meta ( $post_id, "multicorrect-correct-answer" );
					delete_post_meta ( $post_id, "boolean-answer" );
					delete_post_meta ( $post_id, "text-before-gap" );
					delete_post_meta ( $post_id, "gap" );
					delete_post_meta ( $post_id, "text-after-gap" );
					delete_post_meta ( $post_id, "singleline-answer" );
					delete_post_meta ( $post_id, "multiline-answer" );
				
				} else if($_POST ['dt_question_type'] == 'multiple-correct') {
					
					if( isset( $_POST ['dt_multicorrect_answers'] ) && !empty($_POST ['dt_multicorrect_answers']) ) update_post_meta ( $post_id, "multicorrect-answers", array_filter($_POST ['dt_multicorrect_answers']) );
					else delete_post_meta ( $post_id, "multicorrect-answers" );
					
					$multicorrect_answer = $_POST ['dt-multicorrect-correct-answer'];
					$dt_multicorrect_correct_answers = array();
					$dt_multicorrect_answers = $_POST ['dt_multicorrect_answers'];
					
					if(isset($dt_multicorrect_answers) && !empty($dt_multicorrect_answers)) {
						foreach($dt_multicorrect_answers as $key => $answer)
						{
							if(in_array($key, $multicorrect_answer)) {
								$dt_multicorrect_correct_answers[] = $answer;
							}
						}
					}
					
					if( isset( $dt_multicorrect_correct_answers ) && !empty($dt_multicorrect_correct_answers) ) update_post_meta ( $post_id, "multicorrect-correct-answer", array_filter($dt_multicorrect_correct_answers) );
					else delete_post_meta ( $post_id, "multicorrect-correct-answer" );
					
					delete_post_meta ( $post_id, "multichoice-answers" );
					delete_post_meta ( $post_id, "multichoice-correct-answer" );
					delete_post_meta ( $post_id, "boolean-answer" );
					delete_post_meta ( $post_id, "text-before-gap" );
					delete_post_meta ( $post_id, "gap" );
					delete_post_meta ( $post_id, "text-after-gap" );
					delete_post_meta ( $post_id, "singleline-answer" );
					delete_post_meta ( $post_id, "multiline-answer" );
				
				} else if($_POST ['dt_question_type'] == 'boolean') {
								
					if( isset( $_POST ['dt-boolean-answer'] ) && !empty($_POST ['dt-boolean-answer']) ) update_post_meta ( $post_id, "boolean-answer", $_POST ['dt-boolean-answer'] );
					else delete_post_meta ( $post_id, "boolean-answer" );
					
					delete_post_meta ( $post_id, "multicorrect-answers" );
					delete_post_meta ( $post_id, "multicorrect-correct-answer" );
					delete_post_meta ( $post_id, "multichoice-answers" );
					delete_post_meta ( $post_id, "multichoice-correct-answer" );
					delete_post_meta ( $post_id, "text-before-gap" );
					delete_post_meta ( $post_id, "gap" );
					delete_post_meta ( $post_id, "text-after-gap" );
					delete_post_meta ( $post_id, "singleline-answer" );
					delete_post_meta ( $post_id, "multiline-answer" );
					
				} else if($_POST ['dt_question_type'] == 'gap-fill') {
								
					if( isset( $_POST ['dt_text_before_gap'] ) && !empty($_POST ['dt_text_before_gap']) ) update_post_meta ( $post_id, "text-before-gap", stripslashes($_POST ['dt_text_before_gap']) );
					else delete_post_meta ( $post_id, "text-before-gap" );
					
					if( isset( $_POST ['dt_gap'] ) && !empty($_POST ['dt_gap']) ) update_post_meta ( $post_id, "gap", stripslashes($_POST ['dt_gap']) );
					else delete_post_meta ( $post_id, "gap" );

					if( isset( $_POST ['dt_text_after_gap'] ) && !empty($_POST ['dt_text_after_gap']) ) update_post_meta ( $post_id, "text-after-gap", stripslashes($_POST ['dt_text_after_gap']) );
					else delete_post_meta ( $post_id, "text-after-gap" );
					
					delete_post_meta ( $post_id, "multicorrect-answers" );
					delete_post_meta ( $post_id, "multicorrect-correct-answer" );
					delete_post_meta ( $post_id, "multichoice-answers" );
					delete_post_meta ( $post_id, "multichoice-correct-answer" );
					delete_post_meta ( $post_id, "boolean-answer" );
					delete_post_meta ( $post_id, "singleline-answer" );
					delete_post_meta ( $post_id, "multiline-answer" );
				
				} else if($_POST ['dt_question_type'] == 'single-line') {
								
					if( isset( $_POST ['dt_singleline_answer'] ) && !empty($_POST ['dt_singleline_answer']) ) update_post_meta ( $post_id, "singleline-answer", stripslashes($_POST ['dt_singleline_answer']) );
					else delete_post_meta ( $post_id, "singleline-answer" );
					
					delete_post_meta ( $post_id, "multicorrect-answers" );
					delete_post_meta ( $post_id, "multicorrect-correct-answer" );
					delete_post_meta ( $post_id, "multichoice-answers" );
					delete_post_meta ( $post_id, "multichoice-correct-answer" );
					delete_post_meta ( $post_id, "boolean-answer" );
					delete_post_meta ( $post_id, "text-before-gap" );
					delete_post_meta ( $post_id, "gap" );
					delete_post_meta ( $post_id, "text-after-gap" );
					delete_post_meta ( $post_id, "multiline-answer" );

				} else if($_POST ['dt_question_type'] == 'multi-line') {
								
					if( isset( $_POST ['dt_multiline_answer'] ) && !empty($_POST ['dt_multiline_answer']) ) update_post_meta ( $post_id, "multiline-answer",  nl2br(stripslashes($_POST ['dt_multiline_answer'])) );
					else delete_post_meta ( $post_id, "multiline-answer" );
					
					delete_post_meta ( $post_id, "multicorrect-answers" );
					delete_post_meta ( $post_id, "multicorrect-correct-answer" );
					delete_post_meta ( $post_id, "multichoice-answers" );
					delete_post_meta ( $post_id, "multichoice-correct-answer" );
					delete_post_meta ( $post_id, "boolean-answer" );
					delete_post_meta ( $post_id, "text-before-gap" );
					delete_post_meta ( $post_id, "gap" );
					delete_post_meta ( $post_id, "text-after-gap" );
					delete_post_meta ( $post_id, "singleline-answer" );
					
				}
				
				if( isset( $_POST ['dt_answer_explanation'] ) && !empty($_POST ['dt_answer_explanation']) ) update_post_meta ( $post_id, "answer-explanation", nl2br(stripslashes($_POST ['dt_answer_explanation'])) );
				else delete_post_meta ( $post_id, "answer-explanation" );
				
			endif;
				
				
		}
		
		/**
		 * To load question pages in front end
		 *
		 * @param string $template        	
		 * @return string
		 */
		function dt_template_include($template) {
			if (is_singular( 'dt_questions' )) {
				if (! file_exists ( get_stylesheet_directory () . '/single-dt_questions.php' )) {
					$template = plugin_dir_path ( __FILE__ ) . 'templates/single-dt_questions.php';
				}
			}
			return $template;
		}
	}
}
?>