<?php

function dt_quiz_questions($course_id, $lesson_id, $quiz_id = '', $user_id = '') {
	
	if($quiz_id == '')
		$quiz_id = get_the_ID();
	
	if($user_id == '')
		$user_id = get_current_user_id();
	
	$quiz_retakes = dttheme_wp_kses(get_post_meta ($quiz_id, "quiz-retakes", true));
	$quiz_retakes = ( isset($quiz_retakes) && $quiz_retakes != '' ) ? $quiz_retakes : 1;
	
	$dt_gradings = dt_get_user_gradings_array($course_id, $lesson_id, $quiz_id, $user_id);
									
	$dt_grade_post = get_posts( $dt_gradings );
	$grade_post_id = isset($dt_grade_post[0]) ? $dt_grade_post[0]->ID : 0;
	
	$user_attempts = get_post_meta ( $grade_post_id, 'dt-user-attempt', TRUE );
	$user_attempts = ( isset($user_attempts) && $user_attempts != '' ) ? $user_attempts : 0;
	
	$allow_retakes = get_post_meta ( $grade_post_id, 'allow-retakes', TRUE );
	
	if(isset($dt_grade_post[0]) && (!isset($allow_retakes) || $allow_retakes == '' || $allow_retakes == false)) {
	
		echo '<div class="dt-sc-info-box">'.__('You don\'t have permission to retake this quiz! Please contact teacher for further clarifications.', 'dt_themes').'</div>';
		
	} else if($user_attempts >= $quiz_retakes) {
		
		echo '<div class="dt-sc-info-box">'.__('You have crossed the number of retakes allowed for this quiz, so you can\'t retake this quiz.', 'dt_themes').'</div>';
		
	} else {
		
		$out = '';
		
		$quiz_duration = dttheme_wp_kses(get_post_meta ( $quiz_id, "quiz-duration",true));
		$quiz_duration = (isset($quiz_duration) && $quiz_duration > 0) ? $quiz_duration : 0;
				
		$out .= '<div class="column dt-sc-three-fourth first">';
		$out .= '<form method="post" class="frmQuiz" name="frmQuiz" action="'.get_permalink($quiz_id).'">';
		
		$out .= '<div class="dt-quiz-container">';
						
		$out .= '<div id="dt-question-list">';
		
		$quiz_question = get_post_meta ( $quiz_id, "quiz-question",true);
		$quiz_question_grade = get_post_meta ( $quiz_id, "quiz-question-grade",true); 
		$quiz_randomize_questions = get_post_meta ( $quiz_id, "quiz-randomize-questions",true); 
		if(isset($quiz_randomize_questions) && $quiz_randomize_questions != '') {
			shuffle($quiz_question);
		}
			
		$i = 0;
		foreach($quiz_question as $question_id) {
			
			$question_args = array( 'post_type' => 'dt_questions', 'p' => $question_id );
			$question = get_posts( $question_args );
			
			$out .= '<div class="dt-question">';
				$out .= '<div class="dt-title">';
					$out .= '<h4>'.($i+1).'. '.$question[0]->post_content.'</h4>';
				$out .= '</div>';	
			
				$out .= '<div class="dt-question-options">';
				
				$question_type = get_post_meta ( $question_id, "question-type",true);
				
				if($question_type == 'multiple-choice') {
					
					$multichoice_answers = dttheme_wp_kses(get_post_meta ( $question_id, 'multichoice-answers', TRUE ));
					
					if(isset($multichoice_answers) && is_array($multichoice_answers)) {
						$out .= '<ul>'; $j = 1;
						foreach($multichoice_answers as $answer) {
							$out .= '<li>';
							$out .= '<input id="dt-question-'.$question_id.'-option-'.$j.'" type="radio" name="dt-question-'.$question_id.'" value="'.$answer.'" />  <label>'.$answer.'</label>';
							$out .= '</li>';
							$j++;
						}
						$out .= '</ul>';
					}
				
				} else if($question_type == 'multiple-correct') {
					
					$multicorrect_answers = dttheme_wp_kses(get_post_meta ( $question_id, 'multicorrect-answers', TRUE ));
					
					if(isset($multicorrect_answers) && is_array($multicorrect_answers)) {
						$out .= '<ul>'; $j = 1;
						foreach($multicorrect_answers as $answer) {
							$out .= '<li>';
							$out .= '<input id="dt-question-'.$question_id.'-option-'.$j.'" type="checkbox" name="dt-question-'.$question_id.'[]" value="'.$answer.'" />  <label>'.$answer.'</label>';
							$out .= '</li>';
							$j++;
						}
						$out .= '</ul>';
					}
				
				} else if($question_type == 'boolean') {
					
					$out .= '<div class="dt-boolean">';
					$out .= '<input id="dt-question-'.$question_id.'-option-1" type="radio" name="dt-question-'.$question_id.'" value="true" />  <label>'.__('True', 'dt_themes').'</label>';
					$out .= '<input id="dt-question-'.$question_id.'-option-1" type="radio" name="dt-question-'.$question_id.'" value="false" />  <label>'.__('False', 'dt_themes').'</label>';
					$out .= '</div>';			
					
				} else if($question_type == 'gap-fill') {
	
					$text_before_gap = dttheme_wp_kses(get_post_meta ( $question_id, 'text-before-gap', TRUE ));
					$text_before_gap = !empty($text_before_gap) ? $text_before_gap : '';
					$text_after_gap = dttheme_wp_kses(get_post_meta ( $question_id, 'text-after-gap', TRUE ));
					$text_after_gap = !empty($text_after_gap) ? $text_after_gap : '';
					
					$out .= '<div class="dt-gapfill">';
					$out .= $text_before_gap.' <input id="dt-question-'.$question_id.'" type="text" name="dt-question-'.$question_id.'" value="" class="dt-gap" /> '.$text_after_gap;
					$out .= '</div>';	
				
				} else if($question_type == 'single-line') {
								
					$out .= '<input id="dt-question-'.$question_id.'" type="text" name="dt-question-'.$question_id.'" value="" />';			
	
				} else if($question_type == 'multi-line') {
								
					$out .= '<textarea id="dt-question-'.$question_id.'" name="dt-question-'.$question_id.'"></textarea>';
					
				}
				
				$out .= '</div>';	
				$out .= '<div class="dt-mark"><span>'.$quiz_question_grade[$i].'</span>'.__('Mark(s)', 'dt_themes').'</div>';
				
			$out .= '</div>';
			$out .= '<div class="dt-sc-hr-invisible-small"></div>';
			
			$i++;
			
		}
		
		$out .= '</div>';
		
		$out .= '</div>';
		
		$out .= '<input type="hidden" name="dt_question_type" id="dt_question_type" value="'.$question_type.'" />';
		$out .= '<a class="dt-sc-button small filled" name="complete_quiz" id="dt-complete-quiz">'.__('Complete Quiz','dt_themes').'</a>';
		
		$out .= '</form>';
		$out .= '</div>';
		
		$out .= '<div class="column dt-sc-one-fourth">';
		
		$out .= '<div class="dt-sc-quiz-sidebar">';
		
		$out .= '<div class="dt-sc-timer-container">';
		
		if($quiz_duration > 0) {
			$quiz_duration_secs = ($quiz_duration*60);
			$out .= '<h4><span class="fa fa-clock-o"></span>'.__('Time Remaining', 'dt_themes').'</h4>';
			$out .= '<div class="dt-quiz-timer dt-start" data-time="'.$quiz_duration_secs.'">
						<div class="dt-timer" data-timer="'.$quiz_duration_secs.'"></div>
						<div class="dt-countdown">'.$quiz_duration.'</div>  
						<span class="dt-mins">'.__('MINS', 'dt_themes').'</span>
						<span class="dt-secs">'.__('SECS', 'dt_themes').'</span>
					</div>';
		}
		
		$attempt = $user_attempts+1;
		if($user_id != '' && $quiz_retakes != '')
			$out .='<div class="dt-sc-attempt">'.__('Attempt : ', 'dt_themes').$attempt.'</div>';
			
		$out .= '</div>';	
		
		$quiz_pass_percentage = get_post_meta ( $quiz_id, "quiz-pass-percentage",true);
		if(isset($quiz_pass_percentage) && $quiz_pass_percentage != '') {
			$out .= '<div class="dt-sc-warning-box">';
			$out .= sprintf( __('You require %s to pass this quiz!', 'dt_themes'), $quiz_pass_percentage.'%' );
			$out .= '</div>';	
		}
		
		$out .= '</div>';		
		
		$out .= '</div>';						
		
		echo $out;
	
	}
	
}

function dt_validate_quiz($course_id, $lesson_id, $quiz_id = '', $user_id = '') {

	if($quiz_id == '')
		$quiz_id = get_the_ID();
	
	if($user_id == '')
		$user_id = get_current_user_id();
		
	$dt_gradings = dt_get_user_gradings_array($course_id, $lesson_id, $quiz_id, $user_id);
	
	$dt_grade_post = get_posts( $dt_gradings );
	
	$quiz_retakes = dttheme_wp_kses(get_post_meta ($quiz_id, "quiz-retakes", true));
	$quiz_retakes = ( isset($quiz_retakes) && $quiz_retakes >= 0 ) ? $quiz_retakes : 1;
	
	if(!isset($dt_grade_post[0])) {
	
		$title = '';
		if(isset($user_id) && $user_id >= 0) {
			$user_info = get_userdata($user_id);
			$title .= $user_info->display_name;
		}
		
		if(isset($lesson_id) && $lesson_id >= 0) {
			$lesson_args = array( 'post_type' => 'dt_lessons', 'p' => $lesson_id );
			$lesson = get_posts( $lesson_args );
			$title .= ' - '.$lesson[0]->post_title;
		}
		
		$course_args = array( 'post_type' => 'dt_courses', 'p' => $course_id );
		$course = get_posts( $course_args );
		
		$grade_post = array(
			'post_title' => $title,
			'post_status' => 'publish',
			'post_type' => 'dt_gradings',
			'post_author' => $course[0]->post_author,
		);
		
		$grade_post_id = wp_insert_post( $grade_post );
		
		update_post_meta ( $grade_post_id, 'allow-retakes',  true );
		update_post_meta ( $grade_post_id, 'dt-quiz-id',  $quiz_id );
		update_post_meta ( $grade_post_id, 'dt-course-id',  $course_id );
		update_post_meta ( $grade_post_id, 'dt-lesson-id',  $lesson_id );
		update_post_meta ( $grade_post_id, 'dt-user-id',  $user_id );
		update_post_meta ( $grade_post_id, 'grade-type',  'quiz' );
		
		$user_attempts = 1;
	
	} else {
		$grade_post_id = $dt_grade_post[0]->ID;
	}
	
		
	if(isset($dt_grade_post[0])) {
		$user_attempts = get_post_meta ( $grade_post_id, 'dt-user-attempt', TRUE );
		$user_attempts = ( isset($user_attempts) && $user_attempts != '' ) ? $user_attempts : 1;
		
		$prev_gradings = get_post_meta ( $grade_post_id, 'dt-prev-gradings', TRUE );
		$prev_gradings = ( isset($prev_gradings) && !empty($prev_gradings) ) ? array_filter($prev_gradings) : array();
		
		$marks_obtained = get_post_meta( $grade_post_id, 'marks-obtained', TRUE );
		$marks_obtained = ( isset($marks_obtained) && $marks_obtained > 0 ) ? $marks_obtained : 0;
		
		$marks_obtained_percent = get_post_meta ( $grade_post_id, "marks-obtained-percent",true); 
		$marks_obtained_percent = (isset($marks_obtained_percent) && $marks_obtained_percent > 0) ? $marks_obtained_percent : 0;
		
		$prev_gradings[$user_attempts-1]['attempts'] = $user_attempts;
		$prev_gradings[$user_attempts-1]['mark'] = $marks_obtained;
		$prev_gradings[$user_attempts-1]['percentage'] = $marks_obtained_percent;
		
		$user_attempts = $user_attempts+1;
		
	} else {
		$prev_gradings = array();
	}
		
	if($user_attempts <= $quiz_retakes) {
	
		update_post_meta ( $grade_post_id, 'dt-prev-gradings',  array_filter($prev_gradings) );
		update_post_meta ( $grade_post_id, 'dt-user-attempt',  $user_attempts );
		
		$quiz_auto_evaluation = get_post_meta ( $quiz_id, "quiz-auto-evaluation",true);
		$quiz_question_grade = get_post_meta ( $quiz_id, "quiz-question-grade",true);
		$quiz_total_grade = get_post_meta ( $quiz_id, "quiz-total-grade",true); 
		$passmark_percentage = get_post_meta ($quiz_id, "quiz-pass-percentage", true);
		
		$i = 0;
		$user_grade = 0;
		$quiz_question = get_post_meta ( $quiz_id, "quiz-question",true);
		foreach($quiz_question as $question_id) {
			
			$question_type = get_post_meta ( $question_id, "question-type",true);
			
			$user_answer = isset($_POST['dt-question-'.$question_id]) ? $_POST['dt-question-'.$question_id] : '';
			
			if($question_type == 'multi-line') {
				$user_answer = trim(nl2br($user_answer));
			} else if($question_type == 'single-line') {
				$user_answer = trim($user_answer);
			}
			
			update_post_meta ( $grade_post_id, 'dt-question-'.$question_id, $user_answer );
			
			if(isset($quiz_auto_evaluation) && $quiz_auto_evaluation != '') {
				if(dt_validate_user_answer($question_id, $question_type, $user_answer)) {
					update_post_meta ( $grade_post_id, 'question-id-'.$question_id.'-grade', true );
					$user_grade = $user_grade + $quiz_question_grade[$i];
				} else {
					delete_post_meta ( $grade_post_id, 'question-id-'.$question_id.'-grade' );	
				}
			} else {
				delete_post_meta ( $grade_post_id, 'question-id-'.$question_id.'-grade' );	
			}
			
			$i++;
					
		}
		
		if(isset($quiz_auto_evaluation) && $quiz_auto_evaluation != '') {
			$user_percentage = round((($user_grade/$quiz_total_grade)*100), 2);
			update_post_meta ( $grade_post_id, "marks-obtained", stripslashes ( $user_grade ) );
			update_post_meta ( $grade_post_id, "marks-obtained-percent", stripslashes ( $user_percentage ) );
			update_post_meta ( $grade_post_id, "graded", true );
		} else {
			delete_post_meta ( $grade_post_id, 'marks-obtained' );
			delete_post_meta ( $grade_post_id, 'marks-obtained-percent' );
			delete_post_meta ( $grade_post_id, 'graded' );
		}
		
	}
	
		
	$quiz_postmsg = dttheme_wp_kses(get_post_meta ($quiz_id, "quiz-postmsg", true));
	if(isset($quiz_postmsg) && $quiz_postmsg != '')
		echo '<div class="dt-sc-post-quiz-msg">'.$quiz_postmsg.'</div>';
		
	echo '<div class="dt-sc-hr-invisible"></div>';
	
	$quiz_duration = dttheme_wp_kses(get_post_meta ( $quiz_id, "quiz-duration",true));
	$quiz_duration = (isset($quiz_duration) && $quiz_duration > 0) ? $quiz_duration : 0;
	
	if(isset($quiz_auto_evaluation) && $quiz_auto_evaluation != '') {
		if($passmark_percentage > 0) {
			if($user_percentage >= $passmark_percentage) {
				echo '<div class="dt-sc-success-box">';
				echo sprintf( __('Congratulations! you have passed the quiz by achieving %s', 'dt_themes'), $user_percentage.'%' );
				echo '</div>';
			} else {
				echo '<div class="dt-sc-error-box">';
				echo sprintf( __('You require %1$s to pass this quiz. Your grade is %2$s', 'dt_themes'), $passmark_percentage.'%', $user_percentage.'%'  );
				echo '</div>';
			}
		} else {
			echo '<div class="dt-sc-info-box">';
			echo sprintf( __('You acquired %s in this quiz.', 'dt_themes'), $user_percentage.'%' );
			echo '</div>';
		}
	} else {
		if($user_attempts < $quiz_retakes) {
			echo '<div class="dt-sc-info-box">'.__('Your quiz have been submitted successfully and it will be graded soon!', 'dt_themes').'</div>';
		} else 	if($user_attempts >= $quiz_retakes) {
			echo '<div class="dt-sc-info-box">'.__('Your quiz have been submitted successfully and it will be graded soon!<br /> You have crossed the number of retakes allowed for this quiz, so you can\'t retake this quiz.', 'dt_themes').'</div>';
		}
	}
	
	echo '<a href="'.get_permalink($course_id).'" class="dt-sc-button small back-to-course">'.__('Back to ', 'dt_themes').get_the_title($course_id).'</a>';
	
}

function dt_get_user_gradings_array($course_id, $lesson_id, $quiz_id, $user_id) {
	
	$dt_gradings = array(
					'post_type'=>'dt_gradings',
					'meta_query'=>array()
				);
	
	$dt_gradings['meta_query'][] = array(
										'key'     => 'dt-quiz-id',
										'value'   => $quiz_id,
										'compare' => '=',
										'type'    => 'numeric'
									);

	$dt_gradings['meta_query'][] = array(
										'key'     => 'dt-course-id',
										'value'   => $course_id,
										'compare' => '=',
										'type'    => 'numeric'
									);
									
	$dt_gradings['meta_query'][] = array(
										'key'     => 'dt-lesson-id',
										'value'   => $lesson_id,
										'compare' => '=',
										'type'    => 'numeric'
									);

	$dt_gradings['meta_query'][] = array(
										'key'     => 'dt-user-id',
										'value'   => $user_id,
										'compare' => '=',
										'type'    => 'numeric'
									);
									
	if($quiz_id == -1) {
		$dt_gradings['meta_query'][] = array(
											'key'     => 'grade-type',
											'value'   => 'manual',
											'compare' => '=',
										);
	} else {
		$dt_gradings['meta_query'][] = array(
											'key'     => 'grade-type',
											'value'   => 'quiz',
											'compare' => '=',
										);
	}
									
	return $dt_gradings;

}

function dt_can_user_retake_quiz($course_id, $lesson_id, $quiz_id, $user_id) {
	
	$dt_gradings = array(
					'post_type'=>'dt_gradings',
					'meta_query'=>array()
				);
	
	$dt_gradings['meta_query'][] = array(
										'key'     => 'dt-quiz-id',
										'value'   => $quiz_id,
										'compare' => '=',
										'type'    => 'numeric'
									);

	$dt_gradings['meta_query'][] = array(
										'key'     => 'dt-course-id',
										'value'   => $course_id,
										'compare' => '=',
										'type'    => 'numeric'
									);
									
	$dt_gradings['meta_query'][] = array(
										'key'     => 'dt-lesson-id',
										'value'   => $lesson_id,
										'compare' => '=',
										'type'    => 'numeric'
									);

	$dt_gradings['meta_query'][] = array(
										'key'     => 'dt-user-id',
										'value'   => $user_id,
										'compare' => '=',
										'type'    => 'numeric'
									);
						
									
	$dt_grade_post = get_posts( $dt_gradings );
	$grade_post_id = isset($dt_grade_post[0]) ? $dt_grade_post[0]->ID : 0;
	
	$user_attempts = get_post_meta ( $grade_post_id, 'dt-user-attempt', TRUE );
	$user_attempts = ( isset($user_attempts) && $user_attempts != '' ) ? $user_attempts : 0;
	
	$allow_retakes = get_post_meta ( $grade_post_id, 'allow-retakes', TRUE );
	
	$quiz_retakes = dttheme_wp_kses(get_post_meta ($quiz_id, "quiz-retakes", true));
	$quiz_retakes = ( isset($quiz_retakes) && $quiz_retakes >= 0 ) ? $quiz_retakes : 1;
	
	if($allow_retakes == true && $user_attempts < $quiz_retakes)
		return true;
	else
		return false;
	
}

function dt_get_users_course_status($course_id, $user_id = '') {
	
	if($user_id == '')
		$user_id = get_current_user_id();
	
	$lesson_args = array('post_type' => 'dt_lessons', 'posts_per_page' => -1, 'meta_key' => 'dt_lesson_course', 'meta_value' => $course_id );
	$lessons_array = get_pages( $lesson_args );
	$total_lessons = count($lessons_array);
	
	$i = 0;
	if(isset($lessons_array) && !empty($lessons_array)) {		
	
		foreach($lessons_array as $lesson) {
			
			$lesson_id = $lesson->ID;
			$quiz_id = get_post_meta ($lesson_id, "lesson-quiz", true);
			if(!isset($quiz_id) || $quiz_id == '') $quiz_id = -1;
			
			$dt_gradings = dt_get_user_gradings_array($course_id, $lesson_id, $quiz_id, $user_id);
			$dt_grade_post = get_posts( $dt_gradings );
			
			if(isset($dt_grade_post[0])) {
				$dt_grade_post_id = $dt_grade_post[0]->ID;
				$user_status = get_post_meta ( $dt_grade_post_id, "graded",true);
				if(isset($user_status) && $user_status != '')
					$i++;
			}
			
		}
	
	}
	
	$assignment_args = array('post_type' => 'dt_assignments', 'posts_per_page' => -1, 'meta_query'=>array());	
	$assignment_args['meta_query'][] = array( 'key' => 'assignment-course-evaluation', 'value' => '', 'compare' => '!=' );	
	$assignment_args['meta_query'][] = array( 'key' => 'dt-assignment-course', 'value' => $course_id, 'compare' => '=', 'type' => 'numeric' );
							
	$assignment_array = get_posts( $assignment_args );
	$total_assignments = count($assignment_array);
	
	$j = 0;
	foreach($assignment_array as $assignment) {
		$assignment_id = $assignment->ID;
		
		$dtgradings = array( 'post_type' => 'dt_gradings', 'meta_query'=>array() );
		$dtgradings['meta_query'][] = array( 'key' => 'dt-user-id', 'value' => $user_id, 'compare' => '=', 'type' => 'numeric' );
		$dtgradings['meta_query'][] = array( 'key' => 'dt-assignment-id', 'value' => $assignment_id, 'compare' => '=', 'type' => 'numeric' );
		$dtgradings['meta_query'][] = array( 'key' => 'grade-type', 'value' => 'assignment', 'compare' => '=' );
		$dtgradings['meta_query'][] = array( 'key' => 'graded', 'value' => '', 'compare' => '!=' );
		$dtgradings_post = get_posts( $dtgradings );
		
		if(isset($dtgradings_post) && !empty($dtgradings_post))
			$j++;
		
	}
	
	$total_tasks = $total_lessons+$total_assignments;
	$total_tasks_completed = $i+$j;
	
	if($total_tasks != 0 && ($total_tasks == $total_tasks_completed))
		return true;
	else
		return false;	

}


function dt_validate_user_answer($question_id, $question_type, $user_answer) {

	if($question_type == 'multiple-choice') {
		
		$correct_answer = get_post_meta ( $question_id, 'multichoice-correct-answer', TRUE );
		
	} else if($question_type == 'multiple-correct') {
		
		$correct_answer = get_post_meta ( $question_id, 'multicorrect-correct-answer', TRUE );
		
	} else if($question_type == 'boolean') {
		
		$correct_answer = get_post_meta ( $question_id, 'boolean-answer', TRUE );
		
	} else if($question_type == 'gap-fill') {
	
		$correct_answer = get_post_meta ( $question_id, 'gap', TRUE );
	
	} else if($question_type == 'single-line') {
					
		$correct_answer = get_post_meta ( $question_id, 'singleline-answer', TRUE );
	
	} else if($question_type == 'multi-line') {
					
		$correct_answer = get_post_meta ( $question_id, 'multiline-answer', TRUE );
		$correct_answer = str_replace(array("\r", "\n", "\r\n", "<br>", "<br />", " ", "'", "\\"), "", $correct_answer);
		
		$user_answer = str_replace(array("\r", "\n", "\r\n", "<br>", "<br />", " ", "'", "\\"), "", $user_answer);
		
	}
	
	$user_answer = str_replace(array("\\"), "", $user_answer);
	
	if($question_type != 'multiple-correct') {
		$correct_answer = strtolower(trim($correct_answer));
		$user_answer = strtolower(trim($user_answer));
	}
	
	if($correct_answer == $user_answer)
		return true;
	else
		return false;
		

}

function dt_check_quiz_assigned_status($quiz_id, $post_id) {
	
	$lesson_args = array('post_type' => 'dt_lessons', 'meta_key' => 'lesson-quiz', 'meta_value' => $quiz_id );
	$lessons = get_pages( $lesson_args );
	
	if(isset($lessons[0]) && $lessons[0]->ID != $post_id)
		return false;
	else
		return true;
				
}

add_action( 'wp_ajax_dt_set_commission', 'dt_set_commission' );
add_action( 'wp_ajax_nopriv_dt_set_commission', 'dt_set_commission' );

function dt_set_commission($teacher_id = '') {
	
	
	if($teacher_id == '')
		$teacher_id = $_REQUEST['teacher_id'];

	$payment_settings = get_option('dt_settings');
	
	$payment_settings = isset($payment_settings['set-commission']) ? $payment_settings['set-commission'] : array();
	
	$out = '';
	
	$course_args = array( 'post_type' => 'dt_courses', 'posts_per_page' => -1, 'author' => $teacher_id );
	$courses = get_posts( $course_args );
	
	$out .= '<table border="0" cellpadding="0" cellspacing="20">
				<thead>
				  <tr>
					<th scope="col">'.__('#', 'dt_themes').'</th>
					<th scope="col">'.__('Course', 'dt_themes').'</th>
					<th scope="col">'.__('Price', 'dt_themes').'</th>
					<th scope="col">'.__('Commission (%)', 'dt_themes').'</th>
				  </tr>
				</thead>
				<tbody>';
	
	$i = 1;
	foreach($courses as $course) {
		$starting_price = dttheme_wp_kses(get_post_meta($course->ID, 'starting-price', true));
		if(dttheme_option('dt_course','currency-position') == 'after-price') 
			$price = $starting_price.dttheme_wp_kses(dttheme_option('dt_course','currency')); 
		else
			$price = dttheme_wp_kses(dttheme_option('dt_course','currency')).$starting_price; 
		
		if(isset($payment_settings['commission'][$teacher_id]['course'][$course->ID]) && $payment_settings['commission'][$teacher_id]['course'][$course->ID] != '') {
			$out .= '<tr>
						<td>'.$i.'</td>
						<td>'.$course->post_title.'</td>
						<td>'.$price.'</td>
						<td><input type="number" name="course['.$course->ID.']" value="'.$payment_settings['commission'][$teacher_id]['course'][$course->ID].'" /></td>
					</tr>';
		} else {
			$out .= '<tr>
						<td>'.$i.'</td>
						<td>'.$course->post_title.'</td>
						<td>'.$price.'</td>
						<td><input type="number" name="course['.$course->ID.']" value="" /></td>
					</tr>';
		}
		
		$i++;
	}
	
	$out .= '</tbody></table>';
	
	$out .= wp_nonce_field('dt_payment_settings','_wpnonce'); 
	$out .= '<input type="submit" name="dt_save" value="'.__('Save Settings' ,'dt_themes').'" class="dt-statistics-button" />';
	
	echo $out;
	die();
}

add_action( 'wp_ajax_dt_pay_commission', 'dt_pay_commission' );
add_action( 'wp_ajax_nopriv_dt_pay_commission', 'dt_pay_commission' );

function dt_pay_commission($teacher_id = '') {

	if($teacher_id == '')
		$teacher_id = $_REQUEST['teacher_id'];
		
	$payment_settings = get_option('dt_settings');
	
	$paycommission_settings = isset($payment_settings['pay-commission']) ? $payment_settings['pay-commission'] : array();
	$setcommission_settings = isset($payment_settings['set-commission']) ? $payment_settings['set-commission'] : array();
	
	$all_ccaps = dt_get_all_capabilities_id();
	
	$out = '';
	$out .= '<table border="0" cellpadding="0" cellspacing="20">
				<thead>
				  <tr>
					<th scope="col">'.__('#', 'dt_themes').'</th>
					<th scope="col">'.__('Course', 'dt_themes').'</th>
					<th scope="col">'.__('Purchases', 'dt_themes').'</th>
					<th scope="col">'.__('Price', 'dt_themes').'</th>
					<th scope="col">'.__('Commission (%)', 'dt_themes').'</th>
					<th scope="col">'.sprintf(__('To Pay (%s)', 'dt_themes'), dttheme_wp_kses(dttheme_option('dt_course','currency'))).'</th>
					<th scope="col">'.__('Mark As Paid', 'dt_themes').'</th>
				  </tr>
				</thead>
				<tbody>';
	

	$course_args = array( 'post_type' => 'dt_courses', 'posts_per_page' => -1, 'author' => $teacher_id );
	$courses = get_posts( $course_args );
	
	$pay_commission = array();
	
	$i = 1;
	foreach($courses as $course) {
		$course_id = $course->ID;
		$teacher_id = $course->post_author;
		$x = $i-1;
		
		$starting_price = dttheme_wp_kses(get_post_meta($course_id, 'starting-price', true));
		if(dttheme_option('dt_course','currency-position') == 'after-price') 
			$price = $starting_price.dttheme_wp_kses(dttheme_option('dt_course','currency')); 
		else
			$price = dttheme_wp_kses(dttheme_option('dt_course','currency')).$starting_price; 
		
		$purchases = dt_count_value($all_ccaps, $course_id);	
		$commission = isset($setcommission_settings['commission'][$teacher_id]['course'][$course_id]) ? $setcommission_settings['commission'][$teacher_id]['course'][$course_id] : 0;
		
		$topay = (($purchases*$starting_price)*$commission)/100;
		
		$students_id = dt_get_course_capabilities_id($course_id);
		 
		$pay_commission[$x]['course_id'] = $course_id;
		$pay_commission[$x]['teacher_id'] = $teacher_id;
		$pay_commission[$x]['students_id'] = $students_id;
		$pay_commission[$x]['starting_price'] = $starting_price;
		$pay_commission[$x]['purchases'] = $purchases;
		$pay_commission[$x]['commission'] = $commission;
		$pay_commission[$x]['topay'] = $topay;
		
		$out .= '<tr>
					<td>'.$i.'</td>
					<td>'.$course->post_title.'</td>
					<td>'.$purchases.'</td>
					<td>'.$price.'</td>
					<td>'.$commission.'</td>
					<td>'.$topay.'</td>
					<td>';
						if($topay != 0) {
							
							$out .= '<div data-for="item-'.$x.'" class="dt-paycom-checkbox-switch checkbox-switch-off"></div>';
							$out .= '<input id="item-'.$x.'" class="hidden" type="checkbox" name="item['.$x.']" value="true" />';
							
							$out .= '<input type="hidden" name="hid_topay" id="hid-topay-'.$x.'" value="'.$topay.'" />';
						}
		   $out .= '</td>
				</tr>';
		
		$i++;
	}
	
	$out .= '</tbody></table>';
	
	$out .= '<input type="hidden" name="item_data_all" value="'.dt_encode_array($pay_commission).'" />';
	
	$out .= '<div class="dt-amount-container">';
	$out .= '<label><strong>'.sprintf(__('Total (%s)', 'dt_themes'), dttheme_wp_kses(dttheme_option('dt_course','currency'))).'</strong></label><input type="text" name="total_amount" id="total-amount" value="0" readonly="readonly" />';
	$out .= '</div>';
	
	$out .= wp_nonce_field('dt_payment_settings','_wpnonce'); 
	$out .= '<input type="submit" name="dt_save" value="'.__('Save Settings' ,'dt_themes').'" class="dt-statistics-button" />';
	
	echo $out;
	die();
}

function dt_encode_array($array){
	$array = serialize($array);
	$array = htmlentities($array);
	return $array;
}

function dt_decode_array($array){
	$array = stripslashes($array);
	$array = unserialize($array);
	return $array;
}


function dt_count_value($array, $value){
	$counter = 0;
	foreach($array as $thisvalue)
	{
		if($thisvalue === $value){
			$counter++; 
		}
	}
	return $counter;
}

function dt_get_all_capabilities_id() {

	$students_list = $students_cap = $new_ccaps = array();
	$students = array_merge(get_users(array('role' => 's2member_level1')), get_users(array('role' => 's2member_level2')), get_users(array('role' => 's2member_level3')), get_users(array('role' => 's2member_level4')));
	foreach($students as $student) {
		$students_list[] = $student->data->ID;
		
		$student_level = get_user_field ("s2member_access_role", $student->data->ID);
		if($student_level == 's2member_level2' || $student_level == 's2member_level3' || $student_level == 's2member_level4') { $current_student_cap = dt_get_all_paid_courses(); }
		else { $current_student_cap = get_user_field ("s2member_access_ccaps", $student->data->ID); $current_student_cap = dt_remove_cid($current_student_cap); }
		
		$commission_paid_caps = get_user_meta($student->data->ID, 'commission_ccaps', true);
		$commission_paid_caps = array_filter(explode(',', $commission_paid_caps));
		
		$result_caps = array_diff($current_student_cap, $commission_paid_caps);
		
		$students_cap[] = $result_caps;
	}
	
	foreach($students_cap as $key => $cap) {
		$new_ccaps = array_merge($new_ccaps, $cap);
	}

	return $new_ccaps;
	
}

function dt_remove_cid($new_ccaps) {
	$all_ccaps = array();
	foreach($new_ccaps as $cap) {
		$all_ccaps[] = (int)str_replace('cid_', '', $cap);
	}
	return $all_ccaps;
}

function dt_get_course_capabilities_id($course_id) {

	$course_id = 'cid_'.$course_id;
	
	$students_list = $students_cap = $new_ccaps = $all_ccaps = array();
	$students = get_users( array('role' => 's2member_level1') );
	foreach($students as $student) {
		$students_cap = get_user_field ("s2member_access_ccaps", $student->data->ID);
		if(in_array($course_id, $students_cap)) {
			$students_list[] = $student->data->ID;
		}
	}
	
	$s2members = array('s2member_level2', 's2member_level3', 's2member_level4');
	foreach($s2members as $s2member) {
		$s2users = get_users(array('role' => $s2member));
		foreach($s2users as $s2user) $students_list[] = $s2user->data->ID;
	}
	
	return $students_list;
	
}

function dt_list_questions_with_answers($course_id, $lesson_id) {

	$quiz_id = get_the_ID();
	$user_id = get_current_user_id();
	
	$quiz_question = get_post_meta ( $quiz_id, "quiz-question",true);
	$quiz_question_grade = get_post_meta ( $quiz_id, "quiz-question-grade",true); 

	$dt_gradings = dt_get_user_gradings_array($course_id, $lesson_id, $quiz_id, $user_id);
									
	$dt_grade_post = get_posts( $dt_gradings );
	$grade_post_id = isset($dt_grade_post[0]) ? $dt_grade_post[0]->ID : 0;

		
	$i = 0;
	$out = '';
	$out .= '<div id="dt-question-list">';
	foreach($quiz_question as $question_id) {
		
		$question_args = array( 'post_type' => 'dt_questions', 'p' => $question_id );
		$question = get_posts( $question_args );
		
		$question_grade = get_post_meta ( $grade_post_id, 'question-id-'.$question_id.'-grade',true);
		
		if($question_grade == true) $grade_cls = 'dt-correct'; else $grade_cls = 'dt-wrong';
		
			$out .= '<div class="dt-question '.$grade_cls.'">';
				$out .= '<div class="dt-title">';
					$out .= '<h4>'.($i+1).' '.$question[0]->post_content.'</h4>';
				$out .= '</div>';	
			
			
				$question_name = 'dt-question-'.$question_id;
				$user_answer = get_post_meta ( $grade_post_id, $question_name, TRUE );
			
				$out .= '<div class="dt-question-options">';
						
					$question_type = get_post_meta ( $question_id, "question-type",true);
					if($question_type == 'multiple-choice') {
						
						$multichoice_answers = dttheme_wp_kses(get_post_meta ( $question_id, 'multichoice-answers', TRUE ));
						
						if(isset($multichoice_answers) && is_array($multichoice_answers)) {
							$out .= '<ul>'; $j = 1;
							foreach($multichoice_answers as $answer) {
								if($user_answer == $answer) $chk_attr = 'checked="checked"'; else $chk_attr = '';
								$out .= '<li>';
								$out .= '<input id="dt-question-'.$question_id.'-option-'.$j.'" type="radio" name="dt-question-'.$question_id.'" value="'.$answer.'" disabled="disabled" '.$chk_attr.' />  <label>'.$answer.'</label>';
								$out .= '</li>';
								$j++;
							}
							$out .= '</ul>';
						}
					
					} else if($question_type == 'multiple-correct') {
						
						$multicorrect_answers = dttheme_wp_kses(get_post_meta ( $question_id, 'multicorrect-answers', TRUE ));
						
						if(isset($multicorrect_answers) && is_array($multicorrect_answers)) {
							$out .= '<ul>'; $j = 1;
							foreach($multicorrect_answers as $answer) {
								if(is_array($user_answer) && in_array($answer,$user_answer)) $chk_attr = 'checked="checked"'; else $chk_attr = '';
								$out .= '<li>';
								$out .= '<input id="dt-question-'.$question_id.'-option-'.$j.'" type="checkbox" name="dt-question-'.$question_id.'[]" value="'.$answer.'" disabled="disabled" '.$chk_attr.' />  <label>'.$answer.'</label>';
								$out .= '</li>';
								$j++;
							}
							$out .= '</ul>';
						}
					
					} else if($question_type == 'boolean') {
						
						if($user_answer == 'true') {
							$true_attr = 'checked="checked"'; 
							$false_attr = ''; 
						} else {
							$true_attr = ''; 
							$false_attr = 'checked="checked"'; 
						}
						
						$out .= '<div class="dt-boolean">';
						$out .= '<input id="dt-question-'.$question_id.'-option-1" type="radio" name="dt-question-'.$question_id.'" value="true" disabled="disabled" '.$true_attr.' />  <label>'.__('True', 'dt_themes').'</label>';
						$out .= '<input id="dt-question-'.$question_id.'-option-1" type="radio" name="dt-question-'.$question_id.'" value="false" disabled="disabled" '.$false_attr.' />  <label>'.__('False', 'dt_themes').'</label>';
						$out .= '</div>';	 			
						
					} else if($question_type == 'gap-fill') {
		
						$text_before_gap = dttheme_wp_kses(get_post_meta ( $question_id, 'text-before-gap', TRUE ));
						$text_before_gap = !empty($text_before_gap) ? $text_before_gap : '';
						$text_after_gap = dttheme_wp_kses(get_post_meta ( $question_id, 'text-after-gap', TRUE ));
						$text_after_gap = !empty($text_after_gap) ? $text_after_gap : '';
						
						$out .= '<div class="dt-gapfill">';
						$out .= $text_before_gap.' <input id="dt-question-'.$question_id.'" type="text" name="dt-question-'.$question_id.'" value="'.$user_answer.'" class="dt-gap" disabled="disabled" /> '.$text_after_gap;
						$out .= '</div>';	 
						
					} else if($question_type == 'single-line') {
									
						$out .= '<input id="dt-question-'.$question_id.'" type="text" name="dt-question-'.$question_id.'" value="'.$user_answer.'" disabled="disabled" />';			
		
					} else if($question_type == 'multi-line') {
									
						$out .= '<textarea id="dt-question-'.$question_id.'" name="dt-question-'.$question_id.'" disabled="disabled">'.str_replace(array("<br>", "<br />"), "", $user_answer).'</textarea>';
						
					}
					
					$answer_explanation = dttheme_wp_kses(get_post_meta ( $question_id, 'answer-explanation', TRUE ));
					if(isset($answer_explanation) && $answer_explanation != '') {
						$out .= '<div class="dt-answer-explanation">';
						$out .= '<h5>'.__('Answer Explanation : ', 'dt_themes').'</h5>';	
						$out .= '<div>'.$answer_explanation.'</div>';	
						$out .= '</div>';
					}
				
				$out .= '</div>';	
				
				$out .= '<div class="dt-mark"><span>'.$quiz_question_grade[$i].'</span>'.__('Mark(s)', 'dt_themes').'</div>';
				
			$out .= '</div>';
		$out .= '<div class="dt-sc-hr-invisible-small"></div>';
		
		$i++;
		
	}
	
	$out .= '</div>';	

	echo $out;
	
}

function dt_get_course_percentage($course_id, $user_id = '') {
	
	$total_percent = 0;
	if($user_id == '')
		$user_id = get_current_user_id();

	$lesson_args = array('post_type' => 'dt_lessons', 'posts_per_page' => -1, 'meta_key' => 'dt_lesson_course', 'meta_value' => $course_id );								
	$lessons_array = get_posts( $lesson_args );
	
	$lessons_count = count($lessons_array);
	
	$cnt = 0;
	$teachers = array();
	foreach($lessons_array as $lesson) {
		$lesson_id = $lesson->ID;
		$quiz_id = get_post_meta($lesson_id, 'lesson-quiz', true);
		if(!isset($quiz_id) || $quiz_id == '') $quiz_id = -1;
		
		$dt_gradings = dt_get_user_gradings_array($course_id, $lesson_id, $quiz_id, $user_id);
		$dt_grade_post = get_posts( $dt_gradings );
		
		if(isset($dt_grade_post[0])) {
		
			$dt_grade_post_id = $dt_grade_post[0]->ID;
			$percent = get_post_meta($dt_grade_post_id, 'marks-obtained-percent', true);
			
			$mark_as_graded = get_post_meta ( $dt_grade_post_id, "graded",true);
			
			if(isset($mark_as_graded) && $mark_as_graded != '' && isset($percent)){
				$total_percent = $total_percent + $percent;
				$cnt++;
			}
			
		}
		
	}
	
	$total_assignment_percent = $assign_cnt = 0;
	$assignment_args = array('post_type' => 'dt_assignments', 'posts_per_page' => -1, 'meta_query'=>array());	
	$assignment_args['meta_query'][] = array( 'key' => 'assignment-course-evaluation', 'value' => '', 'compare' => '!=' );	
	$assignment_args['meta_query'][] = array( 'key' => 'dt-assignment-course', 'value' => $course_id, 'compare' => '=', 'type' => 'numeric' );
							
	$assignment_array = get_posts( $assignment_args );
	$total_assignments = count($assignment_array);
	
	
	foreach($assignment_array as $assignment) {
		$assignment_id = $assignment->ID;
		
		$dtgradings = array( 'post_type' => 'dt_gradings', 'meta_query'=>array() );
		$dtgradings['meta_query'][] = array( 'key' => 'dt-user-id', 'value' => $user_id, 'compare' => '=', 'type' => 'numeric' );
		$dtgradings['meta_query'][] = array( 'key' => 'dt-assignment-id', 'value' => $assignment_id, 'compare' => '=', 'type' => 'numeric' );
		$dtgradings['meta_query'][] = array( 'key' => 'grade-type', 'value' => 'assignment', 'compare' => '=' );
		$dtgradings['meta_query'][] = array( 'key' => 'graded', 'value' => '', 'compare' => '!=' );
		$dtgradings_post = get_posts( $dtgradings );
		
		if(isset($dtgradings_post[0]) && !empty($dtgradings_post[0])) {
			$dt_grade_post_id = $dtgradings_post[0]->ID;
			$percent = get_post_meta($dt_grade_post_id, 'marks-obtained-percent', true);
			$total_assignment_percent = $total_assignment_percent + $percent;
			$assign_cnt++;
		}
		
	}
	
	$total_tasks = $lessons_count + $total_assignments;
	$total_tasks_completed = $cnt + $assign_cnt;
	
	$total_percentage = $total_percent + $total_assignment_percent;
	
	if($total_tasks != 0)
		$course_percent = ($total_percentage/$total_tasks);
	
	if($total_tasks == 0 || $total_tasks != $total_tasks_completed) $course_percent = 0;
	
	return round($course_percent, 2);
	
}

function dt_get_user_course_overview($post_per_page, $curr_page) {
	
	$user_id = get_current_user_id();
	$user_info = get_userdata($user_id);
	
	$ccaps = array();
	
	if(IAMD_USER_ROLE == 's2member_level1') {
		foreach ($user_info->allcaps as $cap => $cap_enabled) {
			if (preg_match ("/^access_s2member_ccap_cid_/", $cap))
				$ccaps[] = preg_replace ("/^access_s2member_ccap_cid_/", "", $cap);
		}
	} else if(IAMD_USER_ROLE == 's2member_level2'|| IAMD_USER_ROLE == 's2member_level3' || IAMD_USER_ROLE == 's2member_level4') {
		$ccaps = dt_get_all_paid_courses();
	}
	
	$dt_start = ($curr_page-1)*$post_per_page;
	
	$dt_end = $dt_start+$post_per_page;

	$courses_cnt = count($ccaps);
	
	$course_ids_arr = dt_get_user_graded_course();
	$free_course = array_diff($course_ids_arr, $ccaps);
	$ccaps = array_merge($ccaps, $free_course);
	
	if(isset($ccaps) && is_array($ccaps)) {
		
		echo '<table border="0" cellpadding="0" cellspacing="10" style="width:100%;">
				<thead>
				  <tr>
					<th scope="col">'.__('#', 'dt_themes').'</th>
					<th scope="col" class="dt-sc-align-left">'.__('Course', 'dt_themes').'</th>
					<th scope="col" class="dt-sc-align-left">'.__('Lesson', 'dt_themes').'</th>
					<th scope="col">'.__('Grade', 'dt_themes').'</th>
					<th scope="col">'.__('Status', 'dt_themes').'</th>
					<th scope="col">'.__('Option', 'dt_themes').'</th>
				  </tr>
				</thead>
				<tbody>';
				  
		$i = 0;		  
		foreach($ccaps as $course_id) {
			
			if($i >= $dt_start && $i < $dt_end) {
			
				$course_args = array( 'post_type' => 'dt_courses', 'p' => $course_id );
				$course = get_posts( $course_args );
		
				$course_status = dt_get_users_course_status($course_id, '');
				if($course_status) {
					$course_overall_status = '<div class="dt-sc-course-completed"> <span class="fa fa-check-circle"></span>'.__('Completed', 'dt_themes').'</div>';
					$course_percent = dt_get_course_percentage($course_id, '').'%';	
				} else {
					$course_overall_status = '<div class="dt-sc-course-pending"> <span class="fa fa-clock-o fa-rotate-90"></span>'.__('Pending', 'dt_themes').'</div>';
					$course_percent = '';
				}
				
				$starting_price = dttheme_wp_kses(get_post_meta($course_id, 'starting-price', true));
				if(in_array($course_id, $free_course) & $starting_price != '') $notyet_text = ' <span class="dt-sc-not-purchased">('.__('Not yet purchased', 'dt_themes').')</span>'; 
				elseif(in_array($course_id, $free_course) & $starting_price == '') $notyet_text = ' <span class="dt-sc-not-purchased">('.__('Free', 'dt_themes').')</span>'; 
				else $notyet_text = '';

						
				echo '<tr>
						<td>'.($i+1).'</td>
						<td class="dt-sc-course-name"><a href="'.get_permalink($course_id).'">'.$course[0]->post_title.'</a>'.$notyet_text.'</td>
						<td>&nbsp;</td>
						<td>'.$course_percent.'</td>
						<td>'.$course_overall_status.'</td>
						<td>&nbsp;</td>
					</tr>';
					
				$lesson_args = array('sort_order' => 'ASC', 'sort_column' => 'menu_order', 'post_type' => 'dt_lessons', 'posts_per_page' => -1, 'meta_key' => 'dt_lesson_course', 'meta_value' => $course_id );								
				$lessons_array = get_posts( $lesson_args );
				
				foreach($lessons_array as $lesson) {
					
					$user_option = '';
					$lesson_id = $lesson->ID;
					$quiz_id = get_post_meta ($lesson_id, "lesson-quiz", true);
					if(!isset($quiz_id) || $quiz_id == '') $quiz_id = -1;
					$dt_gradings = dt_get_user_gradings_array($course_id, $lesson_id, $quiz_id, $user_id);
					$dt_grade_post = get_posts( $dt_gradings );
					
					if(isset($dt_grade_post[0])) {
						$dt_grade_post_id = $dt_grade_post[0]->ID;
						$graded = get_post_meta ($dt_grade_post_id, "graded", true);
						
						if(isset($graded) && $graded != '') {
							
							$user_status = '<div class="dt-sc-course-completed"> <span class="fa fa-check-circle"></span>'.__('Completed', 'dt_themes').'</div>';
							
							if($quiz_id != -1 && $quiz_id != '')
								$user_option = '<a href="'.get_permalink($quiz_id).'?dttype=viewquiz" class="dt-sc-button small">'.__('View Quiz', 'dt_themes').'</a>';
							
							$grade = get_post_meta ($dt_grade_post_id, "marks-obtained-percent", true);
							$grade = $grade.'%';
						
						} else {
							$grade = '';
							$user_status = '<div class="dt-sc-course-notgraded"> <span class="fa fa-trophy"></span>'.__('Not yet graded', 'dt_themes').'</div>';
							if(dt_can_user_retake_quiz($course_id, $lesson_id, $quiz_id, $user_id))
								$user_option = '<a href="'.get_permalink($quiz_id).'" class="dt-sc-button small">'.__('Retake Quiz', 'dt_themes').'</a>';
						}
					} else {
						$grade = '';
						$user_status = '<div class="dt-sc-course-pending"> <span class="fa fa-clock-o fa-rotate-90"></span>'.__('Pending', 'dt_themes').'</div>';
						if(isset($quiz_id) && $quiz_id > 0)
							$user_option = '<a href="'.get_permalink($quiz_id).'" class="dt-sc-button small">'.__('Take Quiz', 'dt_themes').'</a>';
					}
					
					echo '<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td class="dt-sc-lesson-name"><a href="'.get_permalink($lesson_id).'">'.$lesson->post_title.'</a></td>
							<td class="dt-sc-grade-percent">'.$grade.'</td>
							<td>'.$user_status.'</td>
							<td>'.$user_option.'</td>
						</tr>';
					
				}
				
				$assignment_args = array('post_type' => 'dt_assignments', 'posts_per_page' => -1, 'meta_query'=>array());	
				$assignment_args['meta_query'][] = array( 'key' => 'dt-assignment-course', 'value' => $course_id, 'compare' => '=', 'type' => 'numeric' );
										
				$assignment_array = get_posts( $assignment_args );
				
				foreach($assignment_array as $assignment) {
					$assignment_id = $assignment->ID;
					
					$dtgradings = array( 'post_type' => 'dt_gradings', 'meta_query'=>array() );
					$dtgradings['meta_query'][] = array( 'key' => 'dt-user-id', 'value' => $user_id, 'compare' => '=', 'type' => 'numeric' );
					$dtgradings['meta_query'][] = array( 'key' => 'dt-course-id', 'value' => $course_id, 'compare' => '=', 'type' => 'numeric' );
					$dtgradings['meta_query'][] = array( 'key' => 'dt-assignment-id', 'value' => $assignment_id, 'compare' => '=', 'type' => 'numeric' );
					$dtgradings['meta_query'][] = array( 'key' => 'grade-type', 'value' => 'assignment', 'compare' => '=' );
					$dtgradings_post = get_posts( $dtgradings );
					
					if(isset($dtgradings_post) && !empty($dtgradings_post)) {
						
						$dtgradings_id = $dtgradings_post[0]->ID;
						$marks_obtained_percent = get_post_meta ( $dtgradings_id, "marks-obtained-percent", true); 
						$graded = get_post_meta ($dtgradings_id, "graded", true);
						
						if(isset($graded) && $graded != '') { 
							$user_status = '<div class="dt-sc-course-completed"> <span class="fa fa-check-circle"></span>'.__('Completed', 'dt_themes').'</div>';
							$grade = $marks_obtained_percent.'%'; 
						}
						else { $user_status = '<div class="dt-sc-course-notgraded"> <span class="fa fa-check-circle"></span>'.__('Not yet graded', 'dt_themes').'</div>'; $grade = ''; }
							
					} else {
						
						$grade = '';
						$user_status = '<div class="dt-sc-course-pending"> <span class="fa fa-clock-o fa-rotate-90"></span>'.__('Pending', 'dt_themes').'</div>';
						
					}
					
					echo '<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td class="dt-sc-assignment-name"><a href="'.get_permalink($assignment_id).'">'.get_the_title($assignment_id).'</a></td>
							<td>'.$grade.'</td>
							<td>'.$user_status.'</td>
							<td><a href="'.get_permalink($assignment_id).'" class="dt-sc-button small">'.__('View Assignment', 'dt_themes').'</a></td>
						</tr>';
						
					
				}
					
			}
			
			$i++;
			
		}
		
		if($i == 0)
			echo '<tr><td colspan="6">'.__('You haven\'t purchased any course!', 'dt_themes').'</td></tr>'; 
		
		echo '</tbody></table>';
		
		echo dtthemes_ajax_pagination($post_per_page, $curr_page, $courses_cnt, 0);
		
	}
	
}

function dt_get_teacher_courses($post_per_page, $curr_page) {

	$user_id = get_current_user_id();

	$courses_arr = get_posts( array('posts_per_page' => -1, 'post_type' => 'dt_courses', 'author' => $user_id ) );
	$courses_cnt = count($courses_arr);

	echo '<table border="0" cellpadding="0" cellspacing="10" style="width:100%;">
			<thead>
			  <tr>
				<th scope="col">'.__('#', 'dt_themes').'</th>
				<th scope="col" class="dt-sc-align-left">'.__('Course', 'dt_themes').'</th>
				<th scope="col">'.__('Lesson', 'dt_themes').'</th>
				<th scope="col">'.__('Status', 'dt_themes').'</th>
			  </tr>
			</thead>
			<tbody>';
	
	
	$offset = (($curr_page-1)*$post_per_page);
	
	$course_args = array('offset'=>$offset, 'paged' => $curr_page ,'posts_per_page' => $post_per_page, 'post_type' => 'dt_courses', 'author' => $user_id );
	$courses = get_posts( $course_args );
	
	$i = 1;
	foreach($courses as $course) {
	
		$lesson_args = array('sort_order' => 'ASC', 'sort_column' => 'menu_order', 'post_type' => 'dt_lessons', 'posts_per_page' => -1, 'meta_key' => 'dt_lesson_course', 'meta_value' => $course->ID );								
		$lessons_array = get_posts( $lesson_args );
	
		 echo '<tr>
			<td>'.$i.'</td>
			<td class="dt-sc-course-name"><a href="'.get_permalink($course->ID).'">'.$course->post_title.'</a></td>
			<td>'.count($lessons_array).'</td>
			<td>'.$course->post_status.'</td>
		</tr>';
		
		$i++;
		
	}
	
	if($i == 1)
		echo '<tr><td colspan="4">'.__('You haven\'t submitted any course!', 'dt_themes').'</td></tr>'; 
	
	echo '</tbody></table>';
	
	echo dtthemes_ajax_pagination($post_per_page, $curr_page, $courses_cnt, 0);

}

function dt_allowed_filetypes() {

	$attachment_types = array(
		'jpg', 'gif', 'png', 'pdf', 'doc', 'docx', 'ppt', 'pptx', 'pps', 'ppsx', 'odt', 'xls', 'xlsx', 'mp3', 'm4a', 'ogg', 'wav', 'wma', 'mp4', 'm4v', 'mov', 'wmv', 'avi', 'mpg', 'ogv', '3gp', '3g2', 'flv', 'webm', 'apk', 'rar', 'zip'
	);

	return $attachment_types;
	
}

function dt_submit_assignemnt() {

	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');


	$user_id = get_current_user_id();
	$user_info = get_userdata($user_id);
	$title = $user_info->display_name;
	$title .= ' - '.get_the_title(); 
	
	$attachment_types = dt_allowed_filetypes();
	
	$assignment_attachment_type = get_post_meta ( get_the_ID(), "assignment-attachment-type",true);
	$assignment_attachment_size = get_post_meta ( get_the_ID(), "assignment-attachment-size",true);

	if(!empty($_FILES['dt-assignemnt-attachment']['name'])) {
		
		$fileName = $_FILES['dt-assignemnt-attachment']['name'];
		$fileInfo = pathinfo($fileName);
		$fileExtension = strtolower($fileInfo['extension']);
		
		$error_str = '';
		
		if(isset($assignment_attachment_type) && $assignment_attachment_type != '') {
			
			if(!in_array($fileExtension, $assignment_attachment_type)) {
				$error_str .=  '<strong>'.__('Allowed File Types : ', 'dt_themes').'</strong>';
				$error_str .= '<ul>';
				foreach($assignment_attachment_type as $assignment) {
					$error_str .= '<li class="'.$assignment.'">'.$assignment.'</li>';	
				}
				$error_str .= '</ul>';
			}
			
		}
		
		if(isset($assignment_attachment_size) && $assignment_attachment_size != '') {
			
			if($_FILES['dt-assignemnt-attachment']['size'] > ($assignment_attachment_size * 1048576)) {
				if($error_str != '') $error_str = ', ';
				$error_str .= '<strong>'.__('Maximum File Size : ', 'dt_themes').'</strong>'.$assignment_attachment_size.'MB';	
			}
			
		}
		
		if($error_str != '') {
			echo __('<strong>ERROR: </strong>','wplms-assignments').$error_str;
			return false;
		}
		
	}
	
	$assignment_course_evaluation = get_post_meta ( get_the_ID(), "assignment-course-evaluation",true);
	$dt_assignment_course = get_post_meta ( get_the_ID(), "dt-assignment-course",true);
	
	$dt_gradings = array( 'post_type'=>'dt_gradings', 'meta_query'=>array() );
	
	$dt_gradings['meta_query'][] = array( 'key' => 'dt-user-id', 'value' => $user_id, 'compare' => '=', 'type' => 'numeric' );
	$dt_gradings['meta_query'][] = array( 'key' => 'dt-assignment-id', 'value' => get_the_ID(), 'compare' => '=', 'type' => 'numeric' );
									
	$dt_grade_post = get_posts( $dt_gradings );
			
	if(empty($dt_grade_post)) {
		
		$grade_post = array(
			'post_title' => $title,
			'post_status' => 'publish',
			'post_type' => 'dt_gradings',
			'post_author' => get_the_author_meta('ID'),
		);
		
		$grade_post_id = wp_insert_post( $grade_post );
		
		update_post_meta ( $grade_post_id, 'grade-type',  'assignment' );
		update_post_meta ( $grade_post_id, 'dt-assignment-id',  get_the_ID() );
		update_post_meta ( $grade_post_id, 'dt-user-id',  $user_id );
		update_post_meta ( $grade_post_id, 'dt-course-id',  $dt_assignment_course );	
		
	} else {
		
		$grade_post_id = isset($dt_grade_post[0]) ? $dt_grade_post[0]->ID : 0;
		
	}
	
	$prev_attachment_id = get_post_meta ( $grade_post_id, "dt-attachment-id", true);
	
	wp_delete_attachment( $prev_attachment_id, true );
	
	if(!empty($_FILES['dt-assignemnt-attachment']['name'])) {
		
		$attachemnt_id = media_handle_upload('dt-assignemnt-attachment', $grade_post_id);
		update_post_meta ( $grade_post_id, 'dt-attachment-id',  $attachemnt_id );
		update_post_meta ( $grade_post_id, 'dt-attachment-name',  $_FILES['dt-assignemnt-attachment']['name'] );
	
	} else {
	
		delete_post_meta ( $grade_post_id, "dt-attachment-id" );
		delete_post_meta ( $grade_post_id, "dt-attachment-name" );
			
	}
	
	update_post_meta ( $grade_post_id, 'dt-assignment-notes',  $_POST['dt-assignemnt-textarea'] );
	
	return true;
	
}

function dt_get_upload_size() {
	
	$max_upload = (int)(ini_get('upload_max_filesize'));
	$max_post = (int)(ini_get('post_max_size'));
	$memory_limit = (int)(ini_get('memory_limit'));
	$upload_size = min($max_upload, $max_post, $memory_limit);
	return $upload_size;

}

function dt_get_user_assignments($post_per_page, $curr_page) {

	$user_id = get_current_user_id();
	
	$offset = (($curr_page-1)*$post_per_page);
	
	echo '<table border="0" cellpadding="0" cellspacing="10" style="width:100%;">
			<thead>
			  <tr>
				<th scope="col">'.__('#', 'dt_themes').'</th>
				<th scope="col">'.__('Assignment', 'dt_themes').'</th>
				<th scope="col">'.__('Mark', 'dt_themes').'</th>
			  </tr>
			</thead>
			<tbody>';

	$dtgradings = array( 'post_type' => 'dt_gradings', 'meta_query'=>array() );
	$dtgradings['meta_query'][] = array( 'key' => 'dt-user-id', 'value' => $user_id, 'compare' => '=', 'type' => 'numeric' );
	$dtgradings['meta_query'][] = array( 'key' => 'graded', 'value' => '', 'compare' => '!=' );
	$dtgradings['meta_query'][] = array( 'key' => 'grade-type', 'value' => 'assignment', 'compare' => '=' );
									
	$dtgradings_post = get_posts( $dtgradings );
	$grade_cnt = count($dtgradings_post);

	
	$dt_gradings = array(
					'post_type' => 'dt_gradings',
					'offset' => $offset, 
					'paged' => $curr_page,
					'posts_per_page' => $post_per_page,
					'meta_query'=>array()
				);
	
	$dt_gradings['meta_query'][] = array(
										'key'     => 'dt-user-id',
										'value'   => $user_id,
										'compare' => '=',
										'type'    => 'numeric'
									);

	$dt_gradings['meta_query'][] = array(
										'key'     => 'graded',
										'value'   => '',
										'compare' => '!=',
									);

	$dt_gradings['meta_query'][] = array(
										'key'     => 'grade-type',
										'value'   => 'assignment',
										'compare' => '=',
									);
									
	$dt_grade_post = get_posts( $dt_gradings );
	
	if(isset($dt_grade_post)) {
		$i = 1;
		foreach($dt_grade_post as $grade_post) {
			
			$marks_obtained = get_post_meta ( $grade_post->ID, "marks-obtained", true);
			$assignment_id = get_post_meta ( $grade_post->ID, "dt-assignment-id", true); 
			$assignment_maximum_mark = get_post_meta ( $assignment_id, "assignment-maximum-mark",true);
			
			 echo '<tr>
					<td>'.$i.'</td>
					<td><a href="'.get_permalink($assignment_id).'">'.$grade_post->post_title.'</a></td>
					<td>'.$marks_obtained.__(' out of ', 'dt_themes').$assignment_maximum_mark.'</td>
				</tr>';
			
			$i++;
		}
	}

	if($i == 1)
		echo '<tr><td colspan="3">'.__('No assignemnts found!', 'dt_themes').'</td></tr>'; 
	
	echo '</tbody></table>';
	
	echo dtthemes_ajax_pagination($post_per_page, $curr_page, $grade_cnt, 0);

}

function dt_get_teacher_assignments($post_per_page, $curr_page) {

	$user_id = get_current_user_id();
	
	$offset = (($curr_page-1)*$post_per_page);
	
	echo '<table border="0" cellpadding="0" cellspacing="10" style="width:100%;">
			<thead>
			  <tr>
				<th scope="col">'.__('#', 'dt_themes').'</th>
				<th scope="col">'.__('Assignment', 'dt_themes').'</th>
				<th scope="col">'.__('Related Course', 'dt_themes').'</th>
				<th scope="col">'.__('File Types', 'dt_themes').'</th>
				<th scope="col">'.__('File Size (MB)', 'dt_themes').'</th>
			  </tr>
			</thead>
			<tbody>';

	$dtassignments = array('posts_per_page' => -1, 'post_type' => 'dt_assignments', 'author' => $user_id );
									
	$dtassignments_post = get_posts( $dtassignments );
	$assignments_cnt = count($dtassignments_post);

	
	$dt_assignments = array(
					'post_type' => 'dt_assignments',
					'offset' => $offset, 
					'paged' => $curr_page,
					'posts_per_page' => $post_per_page,
					'author' => $user_id
				);
									
	$dt_assignments_post = get_posts( $dt_assignments );
	
	if(isset($dt_assignments_post)) {
		$i = 1;
		foreach($dt_assignments_post as $assignment_post) {
			
			$assignment_course_evaluation = get_post_meta ( $assignment_post->ID, "assignment-course-evaluation",true);
			$dt_assignment_course = get_post_meta ( $assignment_post->ID, "dt-assignment-course",true);
			
			$rel_course = '';
			if(isset($assignment_course_evaluation) && $assignment_course_evaluation != '') {
				$course_args = array( 'post_type' => 'dt_courses', 'p' => $dt_assignment_course );
				$course = get_posts( $course_args );
				$rel_course = $course[0]->post_title;
			}
			
			$assignment_attachment_type = get_post_meta ( $assignment_post->ID, "assignment-attachment-type",true);
			
			if(isset($assignment_attachment_type) && !empty($assignment_attachment_type)) $attacment_types = implode(', ',$assignment_attachment_type);
			else $attacment_types = '';
			
			$assignment_attachment_size = get_post_meta ( $assignment_post->ID, "assignment-attachment-size",true);
			
			if(isset($assignment_attachment_size) && !empty($assignment_attachment_size)) $attachment_size = $assignment_attachment_size;
			else $attachment_size = '';
			
			 echo '<tr>
					<td>'.$i.'</td>
					<td><a href="'.get_permalink($assignment_post->ID).'">'.$assignment_post->post_title.'</a></td>
					<td>'.$rel_course.'</td>
					<td>'.$attacment_types.'</td>
					<td>'.$attachment_size.'</td>
				</tr>';
			
			$i++;
		}
	}

	if($i == 1)
		echo '<tr><td colspan="5">'.__('No assignemnts found!', 'dt_themes').'</td></tr>'; 
	
	echo '</tbody></table>';
	
	echo dtthemes_ajax_pagination($post_per_page, $curr_page, $assignments_cnt, 0);

}

add_action( 'wp_ajax_dt_statistics_courses_pagination', 'dt_statistics_courses_pagination' );
add_action( 'wp_ajax_nopriv_dt_statistics_courses_pagination', 'dt_statistics_courses_pagination' );

function dt_statistics_courses_pagination() {
	
	$curr_page = $_REQUEST['curr_page'];
	dt_get_statistics_courses_list(10, $curr_page);
	die();

}

add_action( 'wp_ajax_dt_statistics_students_pagination', 'dt_statistics_students_pagination' );
add_action( 'wp_ajax_nopriv_dt_statistics_students_pagination', 'dt_statistics_students_pagination' );

function dt_statistics_students_pagination() {
	
	$curr_page = $_REQUEST['curr_page'];
	dt_get_statistics_students_list(10, $curr_page);
	die();

}

add_action( 'wp_ajax_dt_statistics_teachers_pagination', 'dt_statistics_teachers_pagination' );
add_action( 'wp_ajax_nopriv_dt_statistics_teachers_pagination', 'dt_statistics_teachers_pagination' );

function dt_statistics_teachers_pagination() {
	
	$curr_page = $_REQUEST['curr_page'];
	dt_get_statistics_teachers_list(10, $curr_page);
	die();

}

add_action( 'wp_ajax_dt_statistics_statistics_graph_ajax', 'dt_statistics_statistics_graph_ajax' );
add_action( 'wp_ajax_nopriv_dt_statistics_statistics_graph_ajax', 'dt_statistics_statistics_graph_ajax' );

function dt_statistics_statistics_graph_ajax() {
	
	$include_zero_sales = $_REQUEST['include_zero_sales'];
	$selectedItems = $_REQUEST['selectedItems'];
	$graph_type = $_REQUEST['graph_type'];
	
	dt_get_statistics_graph_data($graph_type, $include_zero_sales, $selectedItems);
	die();

}

function dt_get_attachment_id_from_url( $attachment_url = '' ) {
 
	global $wpdb;
	$attachment_id = false;
 
	if ($attachment_url == '')
		return false;
 
	$upload_dir_paths = wp_upload_dir();
 
	if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {
 
		$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );
		$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );
 
		$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );
 
	}
 
	return $attachment_id;
	
}

function dt_mark_lesson_complete($lesson_id, $course_id) {
	
	$user_id = get_current_user_id();
		
	$dt_gradings = dt_get_user_gradings_array($course_id, $lesson_id, -1, $user_id);
	$dt_grade_post = get_posts( $dt_gradings );
	
	if(!isset($dt_grade_post[0])) {

		$title = '';
		if(isset($user_id) && $user_id >= 0) {
			$user_info = get_userdata($user_id);
			$title .= $user_info->display_name;
		}
		
		if(isset($lesson_id) && $lesson_id >= 0) {
			$lesson_args = array( 'post_type' => 'dt_lessons', 'p' => $lesson_id );
			$lesson = get_posts( $lesson_args );
			$title .= ' - '.$lesson[0]->post_title;
		}
		
		$title .= ' ('.__('Manual Complete', 'dt_themes').')';
		
		$course_args = array( 'post_type' => 'dt_courses', 'p' => $course_id );
		$course = get_posts( $course_args );
		
		$grade_post = array(
			'post_title' => $title,
			'post_status' => 'publish',
			'post_type' => 'dt_gradings',
			'post_author' => $course[0]->post_author,
		);
		
		$grade_post_id = wp_insert_post( $grade_post );
		
		update_post_meta ( $grade_post_id, 'allow-retakes',  false );
		update_post_meta ( $grade_post_id, 'dt-course-id',  $course_id );
		update_post_meta ( $grade_post_id, 'dt-lesson-id',  $lesson_id );
		update_post_meta ( $grade_post_id, 'dt-quiz-id',  -1 );
		update_post_meta ( $grade_post_id, 'dt-user-id',  $user_id );
		update_post_meta ( $grade_post_id, 'grade-type',  'manual' );
		update_post_meta ( $grade_post_id, "marks-obtained", 100 );
		update_post_meta ( $grade_post_id, "marks-obtained-percent", '100' );
		update_post_meta ( $grade_post_id, "graded", true );
		
	}
	
}

function dt_get_user_graded_course() {
	
	$user_id = get_current_user_id();
	
	$dt_gradings = array(
					'post_type'=>'dt_gradings',
					'meta_query'=>array(),
				);
	
	$dt_gradings['meta_query'][] = array(
										'key'     => 'dt-user-id',
										'value'   => $user_id,
										'compare' => '=',
										'type'    => 'numeric'
									);
									
	$course_ids_arr = array();
	$dt_grade_post = get_posts( $dt_gradings );
	
	if(isset($dt_grade_post) && $dt_grade_post != '') {
		foreach($dt_grade_post as $grade_post) {
			$course_ids_arr[] = get_post_meta ( $grade_post->ID, 'dt-course-id', TRUE );
		}
	}
	
	$course_ids_arr = array_unique($course_ids_arr);
	
	return $course_ids_arr;

}


function dt_get_prev_next_lessons( $lesson_id = 0 ) {

	$dt_lessons = array();
	$dt_lessons['prev_lesson'] = 0;
	$dt_lessons['next_lesson'] = 0;
	if ( $lesson_id > 0 ) {

		$dt_lesson_course = get_post_meta ( $lesson_id, "dt_lesson_course",true);
		$lesson_args = array('sort_order' => 'ASC', 'sort_column' => 'menu_order', 'hierarchical' => 1, 'post_type' => 'dt_lessons', 'posts_per_page' => -1, 'meta_key' => 'dt_lesson_course', 'meta_value' => $dt_lesson_course );
		$lessons_array = get_pages( $lesson_args );
		
		if(isset($lessons_array) && !empty($lessons_array)) {		
			$found_index = false;
			foreach ($lessons_array as $lesson_item){
				if ( $found_index && $dt_lessons['next_lesson'] == 0 ) {
					$dt_lessons['next_lesson'] = $lesson_item->ID;
				}
				if ( $lesson_item->ID == $lesson_id ) {
					$found_index = true;
				}
				if ( !$found_index ) {
					$dt_lessons['prev_lesson'] = $lesson_item->ID;
				}
			}
		}

	}
	
	return $dt_lessons;
}

add_action( 'wp_ajax_dt_ajax_start_quiz', 'dt_ajax_start_quiz' );
add_action( 'wp_ajax_nopriv_dt_ajax_start_quiz', 'dt_ajax_start_quiz' );

function dt_ajax_start_quiz() {
	
	$course_id = $_REQUEST['course_id'];
	$lesson_id = $_REQUEST['lesson_id'];
	$quiz_id = $_REQUEST['quiz_id'];
	$user_id = $_REQUEST['user_id'];
	
	dt_quiz_questions($course_id, $lesson_id, $quiz_id, $user_id);
	die();

}

add_action( 'wp_ajax_dt_ajax_validate_quiz', 'dt_ajax_validate_quiz' );
add_action( 'wp_ajax_nopriv_dt_ajax_validate_quiz', 'dt_ajax_validate_quiz' );

function dt_ajax_validate_quiz() {
	
	$course_id = $_REQUEST['course_id'];
	$lesson_id = $_REQUEST['lesson_id'];
	$quiz_id = $_REQUEST['quiz_id'];
	$user_id = $_REQUEST['user_id'];
	
	dt_validate_quiz($course_id, $lesson_id, $quiz_id, $user_id); 
	die();

}

function dt_get_all_paid_courses() {
	
	$ccaps = array();
	$dt_ccaps_qry = array('post_type'=>'dt_courses', 'sort_order' => 'ASC', 'sort_column' => 'menu_order', 'posts_per_page' => -1, 'meta_query'=>array());
	$dt_ccaps_qry['meta_query'][] = array('key' => 'starting-price', 'value' => 0, 'compare' => '>', 'type' => 'numeric' );

	$dt_ccaps_post = get_posts( $dt_ccaps_qry );
	
	foreach($dt_ccaps_post as $cp_post) {
		$ccaps[] = $cp_post->ID;
	}
	
	return $ccaps;

}

?>