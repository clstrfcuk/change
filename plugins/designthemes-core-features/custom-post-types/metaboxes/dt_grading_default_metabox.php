<?php
global $post;
$post_id = $post->ID;

$quiz_id = get_post_meta ( $post->ID, 'dt-quiz-id', TRUE );
$course_id = get_post_meta ( $post->ID, 'dt-course-id', TRUE );
$lesson_id = get_post_meta ( $post->ID, 'dt-lesson-id', TRUE );
$user_id = get_post_meta ( $post->ID, 'dt-user-id', TRUE );

$user_attempts = get_post_meta ( $post->ID, 'dt-user-attempt', TRUE );
$prev_gradings = get_post_meta ( $post->ID, 'dt-prev-gradings', TRUE );

$grade_type = get_post_meta ( $post->ID, 'grade-type', TRUE );
$assignment_id = get_post_meta ( $post->ID, 'dt-assignment-id', TRUE );

$user_info = get_userdata($user_id);

if($grade_type == 'assignment') {
	?>
    
    <div class="custom-box">
        <div class="column one-fifth">
            <label><?php _e('User Name', 'dt_themes'); ?></label>
        </div>
        <div class="column four-fifth last">
            <label><?php echo $user_info->display_name; ?></label>
        </div>
    </div>
     
    <div class="custom-box">       
        <div class="column one-fifth">
            <label><?php _e('Assignment Title', 'dt_themes'); ?></label>
        </div>
        <div class="column four-fifth last">
            <?php
            $assignment_args = array( 'post_type' => 'dt_assignments', 'p' => $assignment_id );
            $assignment = get_posts( $assignment_args );
            ?>
            <label><?php echo $assignment[0]->post_title; ?></label>
        </div>
    </div>
    
    <div class="custom-box">
        <div class="column one-fifth">
           <label><?php _e('Notes', 'dt_themes'); ?></label>
        </div>
        <div class="column four-fifth last">
            <?php
            $dt_assignment_notes = get_post_meta ( $post_id, "dt-assignment-notes", true); 
			if(isset($dt_assignment_notes) && $dt_assignment_notes != '') {
            ?>
                <label><?php echo nl2br($dt_assignment_notes); ?></label>
            <?php } ?>
        </div>
    </div>

    <div class="custom-box">
        <div class="column one-fifth">
           <label><?php _e('Attachment', 'dt_themes'); ?></label>
        </div>
        <div class="column four-fifth last dt-assignment-attachment">
            <?php
			
            $dt_attachment_id = get_post_meta ( $post_id, "dt-attachment-id", true);
			
			if(isset($dt_attachment_id) && $dt_attachment_id != '') {
			
				$dt_attachment_name = get_post_meta ( $post_id, "dt-attachment-name", true);
				
				echo '<img src="'.plugin_dir_url ( __FILE__ ) . 'images/attachment.png'.'" />';
				echo '<div class="dt-attachments-link">';
				echo $dt_attachment_name;
				echo '<a href="'.wp_get_attachment_url( $dt_attachment_id ).'">'.__('View Attachment', 'dt_themes').'</a>';
				echo '</div>';
				
			} else {
				echo __('No attachments found!', 'dt_themes');
			}
           
		    ?>
        </div>
    </div>
    
     <div class="custom-box">
        <div class="column one-fifth">
           <label><?php _e('Marks Obtained', 'dt_themes'); ?></label>
        </div>
        <div class="column four-fifth last">
            <?php
            $marks_obtained = get_post_meta ( $post_id, "marks-obtained", true); 
            ?>
            <input id="dt-marks-obtained" name="dt-marks-obtained" class="large" type="number" value="<?php echo $marks_obtained; ?>" style="width:10%;" />
        </div>
    </div>

    <div class="custom-box">
        <div class="column one-fifth">
           <label><?php _e('Maximum Marks', 'dt_themes'); ?></label>
        </div>
        <div class="column four-fifth last">
            <?php
            $assignment_maximum_mark = get_post_meta ( $assignment_id, "assignment-maximum-mark", true); 
            ?>
            <label><?php echo $assignment_maximum_mark; ?></label>
            <input type="hidden" name="dt-assignment-maximum-mark" id="dt-assignment-maximum-mark" value="<?php echo $assignment_maximum_mark; ?>"  />
        </div>
    </div>

     <div class="custom-box">
        <div class="column one-fifth">
           <label><?php _e('Percentage (%)', 'dt_themes'); ?></label>
        </div>
        <div class="column four-fifth last">
            <?php
            $marks_obtained_percent = get_post_meta ( $post_id, "marks-obtained-percent", true); 
            ?>
            <div id="dt-marks-obtained-percentage-html"><label><?php echo $marks_obtained_percent; ?></label></div>
            <input type="hidden" name="dt-marks-obtained-percent" id="dt-marks-obtained-percent" value="<?php echo $marks_obtained_percent; ?>"  />
        </div>
    </div>
   
    <div class="custom-box">
        <div class="column one-fifth">
           <label><?php _e('Graded', 'dt_themes'); ?></label>
        </div>
        <div class="column four-fifth last">
            <?php
            $graded = get_post_meta ( $post_id, "graded",true);
            $switchclass = ($graded != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($graded != '') ? ' checked="checked"' : '';
            ?>
            <div data-for="graded" class="dt-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="graded" class="hidden" type="checkbox" name="graded" value="true" <?php echo $checked;?> />
            <p class="note"> <?php _e('Once you enable this option, then this user can\'t resubmit this assignemnt and it will be marked as completed!','dt_themes');?> </p>
        </div>
    </div>
    
    <?php
} else {
	
?>

    <div class="custom-box">
        <div class="column one-half">
        	<table border="0" cellpadding="0" cellspacing="0">
				<?php if($grade_type != 'manual') { ?>
                    <tr>
                        <td> <label class="dt-grading-table-title"><?php _e('Quiz', 'dt_themes'); ?></label> </td>
                      <td> 
                            <?php
                            $quiz_args = array( 'post_type' => 'dt_quizes', 'p' => $quiz_id );
                            $quiz = get_posts( $quiz_args );
                            ?>
                            <label><?php echo $quiz[0]->post_title; ?></label>
                      </td>
                   </tr>
				<?php } ?>
            	<tr>
                	<td> <label class="dt-grading-table-title"><?php _e('User Name', 'dt_themes'); ?></label> </td>
                  <td> <label><?php echo $user_info->display_name; ?></label> </td>
               </tr>
            	<tr>
                	<td> <label class="dt-grading-table-title"><?php _e('Course', 'dt_themes'); ?></label> </td>
                  <td> 
						<?php
                        $course_args = array( 'post_type' => 'dt_courses', 'p' => $course_id );
                        $course = get_posts( $course_args );
                        ?>
                        <label><?php echo $course[0]->post_title; ?></label>
                  </td>
               </tr>
            	<tr>
                	<td> <label class="dt-grading-table-title"><?php _e('Lesson', 'dt_themes'); ?></label> </td>
                  <td> 
						<?php
                        $lesson_args = array( 'post_type' => 'dt_lessons', 'p' => $lesson_id );
                        $lesson = get_posts( $lesson_args );
                        ?>
                        <label><?php echo $lesson[0]->post_title; ?></label>
                  </td>
               </tr>
				<?php
                if(isset($user_attempts) && $user_attempts > 0) {
                ?>
                    <tr>
                        <td> <label class="dt-grading-table-title"><?php _e('Attempt', 'dt_themes'); ?></label> </td>
                      <td> <label><?php echo $user_attempts; ?></label> </td>
                   </tr>
                <?php
                }
                ?>
                <?php if($grade_type == 'manual') { ?>
                    <tr>
                        <td> <label class="dt-grading-table-title"><?php _e('Marks Obtained', 'dt_themes'); ?></label> </td>
                      <td> 
                            <?php
                            $dt_marks_obtained = get_post_meta ( $post_id, "marks-obtained",true); 
                            $dt_marks_obtained = (isset($dt_marks_obtained) && $dt_marks_obtained > 0) ? $dt_marks_obtained : 0;
                            $dt_marks_obtained_percent = get_post_meta ( $post_id, "marks-obtained-percent",true); 
                            $dt_marks_obtained_percent = (isset($dt_marks_obtained_percent) && $dt_marks_obtained_percent > 0) ? $dt_marks_obtained_percent : 0;
                            
                            if($dt_marks_obtained == 0) {
                                $dt_marks_obtained_str = __('0 (0%)', 'dt_themes');
                            } else {
                                $dt_marks_obtained_str = $dt_marks_obtained.' ('.$dt_marks_obtained_percent.'%)';
                            }
                            ?>
                           <div id="dt-marks-obtained-html"><label><?php echo $dt_marks_obtained_str; ?></label></div>
                           <input type="hidden" name="dt-marks-obtained" id="dt-marks-obtained" value="<?php echo $dt_marks_obtained; ?>"  />
                           <input type="hidden" name="dt-marks-obtained-percent" id="dt-marks-obtained-percent" value="<?php echo $dt_marks_obtained_percent; ?>"  />
                      </td>
                   </tr>
               <?php } ?>
           </table>
        </div>
        <div class="column one-half last">
			<?php if($grade_type != 'manual') { ?>
                <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td> <label class="dt-grading-table-title"><?php _e('Passmark Percentage', 'dt_themes'); ?></label> </td>
                      <td> 
							<?php
                            $quiz_pass_percentage = get_post_meta ( $quiz_id, "quiz-pass-percentage",true); 
                            ?>
                            <label><?php echo $quiz_pass_percentage.__(" %",'dt_themes'); ?></label>
                            <input type="hidden" name="dt-pass-percentage" id="dt-pass-percentage" value="<?php echo $quiz_pass_percentage; ?>"  />
                      </td>
                   </tr>
                    <tr>
                        <td> <label class="dt-grading-table-title"><?php _e('Marks Obtained', 'dt_themes'); ?></label> </td>
                      <td> 
                            <?php
                            $dt_marks_obtained = get_post_meta ( $post_id, "marks-obtained",true); 
                            $dt_marks_obtained = (isset($dt_marks_obtained) && $dt_marks_obtained > 0) ? $dt_marks_obtained : 0;
                            $dt_marks_obtained_percent = get_post_meta ( $post_id, "marks-obtained-percent",true); 
                            $dt_marks_obtained_percent = (isset($dt_marks_obtained_percent) && $dt_marks_obtained_percent > 0) ? $dt_marks_obtained_percent : 0;
                            
                            if($dt_marks_obtained == 0) {
                                $dt_marks_obtained_str = __('0 (0%)', 'dt_themes');
                            } else {
                                $dt_marks_obtained_str = $dt_marks_obtained.' ('.$dt_marks_obtained_percent.'%)';
                            }
                            ?>
                           <div id="dt-marks-obtained-html"><label><?php echo $dt_marks_obtained_str; ?></label></div>
                           <input type="hidden" name="dt-marks-obtained" id="dt-marks-obtained" value="<?php echo $dt_marks_obtained; ?>"  />
                           <input type="hidden" name="dt-marks-obtained-percent" id="dt-marks-obtained-percent" value="<?php echo $dt_marks_obtained_percent; ?>"  />
                      </td>
                   </tr>
                    <tr>
                        <td> <label class="dt-grading-table-title"><?php _e('Total Marks', 'dt_themes'); ?></label> </td>
                      <td> 
							<?php
                            $quiz_total_grade = get_post_meta ( $quiz_id, "quiz-total-grade",true); 
                            ?>
                            <label><?php echo $quiz_total_grade; ?></label>
                            <input type="hidden" name="dt-total-marks" id="dt-total-marks" value="<?php echo $quiz_total_grade; ?>"  />
                      </td>
                   </tr>
                    <tr>
                        <td> <label class="dt-grading-table-title"><?php _e('Number Of Retakes', 'dt_themes'); ?></label> </td>
                      <td> 
							<?php
                            $quiz_retakes = dttheme_wp_kses(get_post_meta ( $quiz_id, "quiz-retakes",true)); 
                            ?>
                            <label><?php echo $quiz_retakes; ?></label>
                      </td>
                   </tr>
					<?php
                    if($quiz_retakes != '') {
                    ?>
                        <tr>
                            <td> <label class="dt-grading-table-title"><?php _e('Allow Retakes', 'dt_themes'); ?></label> </td>
                          <td class="dt-allow-reatakes">
								<?php
                                $allow_retakes = get_post_meta ( $post_id, "allow-retakes",true);
                                $switchclass = ($allow_retakes != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
                                $checked = ($allow_retakes != '') ? ' checked="checked"' : '';
                                ?>
                                <div data-for="allow-retakes" class="dt-checkbox-switch <?php echo $switchclass;?>"></div>
                                <input id="allow-retakes" class="hidden" type="checkbox" name="allow-retakes" value="true" <?php echo $checked;?> />
                                <p class="note"> <?php _e('You can allow user to retake this quiz.','dt_themes');?> </p>
                           </td>
                       </tr>
                   <?php } ?>
               </table>
           <?php } ?>
        </div>
    </div>

<?php if($grade_type != 'manual') { ?> 
    <div class="custom-box">
        <div class="column one-half">
            <div class="column one-fifth">
               <label><?php _e('Graded', 'dt_themes'); ?></label>
            </div>
            <div class="column four-fifth last">
                <?php
                $graded = get_post_meta ( $post_id, "graded",true);
                $switchclass = ($graded != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
                $checked = ($graded != '') ? ' checked="checked"' : '';
                ?>
                <div data-for="graded" class="dt-checkbox-switch <?php echo $switchclass;?>"></div>
                <input id="graded" class="hidden" type="checkbox" name="graded" value="true" <?php echo $checked;?> />
                <p class="note"> <?php _e('Once you enable this option, this leeson will be marked as completed and user won\'t be able to retake quiz!','dt_themes');?> </p>
            </div>
        </div>
        <div class="column one-half last">    
            <a class="custom-button-style" id="dt-reset-grade" href="#"><?php echo __('Reset', 'dt_themes'); ?></a>
            <a class="custom-button-style" id="dt-auto-grade" href="#"><?php echo __('Auto Grade', 'dt_themes'); ?></a>
        </div>
    </div>
<?php } else { ?>
	<div class="custom-box">
    	<?php echo __('User manaully marked as completed.', 'dt_themes'); ?>
	</div>
<?php } ?>

<div class="custom-box">


	<?php
	
	if(isset($quiz_id) && $quiz_id != '' && $quiz_id != -1) {
		
		$quiz_question = get_post_meta ( $quiz_id, "quiz-question",true);
		$quiz_question_grade = get_post_meta ( $quiz_id, "quiz-question-grade",true);  
		
		echo '<table id="dt-grading-table" border="0" cellpadding="0" cellspacing="0">
				  <tr id="dt-first-row">
					<th scope="col">'.__('#', 'dt_themes').'</th>
					<th scope="col">'.__('Question', 'dt_themes').'</th>
					<th scope="col" class="aligncenter">'.__('Answer Options', 'dt_themes').'</th>
					<th scope="col" class="aligncenter">'.__('Correct Answer', 'dt_themes').'</th>
					<th scope="col" class="aligncenter">'.__('User Answer', 'dt_themes').'</th>
					<th scope="col" class="aligncenter">'.__('Grade', 'dt_themes').'</th>
					<th scope="col" class="aligncenter">'.__('Option', 'dt_themes').'</th>
				  </tr>';
	  
		$i = 1;
		foreach($quiz_question as $question_id) {
			
			$question_args = array( 'post_type' => 'dt_questions', 'p' => $question_id );
			$question = get_posts( $question_args );
			
			$question_type = get_post_meta ( $question_id, "question-type",true);
			
			if($question_type == 'multiple-choice') {
				
				$answers = dttheme_wp_kses(get_post_meta ( $question_id, 'multichoice-answers', TRUE ));
				$answers = implode('<br />',$answers);
				$correct_answer = dttheme_wp_kses(get_post_meta ( $question_id, 'multichoice-correct-answer', TRUE ));
				
			} else if($question_type == 'multiple-correct') {
				
				$answers = dttheme_wp_kses(get_post_meta ( $question_id, 'multicorrect-answers', TRUE ));
				$answers = implode('<br />',$answers);
				$correct_answer = dttheme_wp_kses(get_post_meta ( $question_id, 'multicorrect-correct-answer', TRUE ));
				$correct_answer = implode('<br />',$correct_answer);
				
			} else if($question_type == 'boolean') {
				
				$answers = __('True', 'dt_themes').'<br />'.__('False', 'dt_themes');
				$correct_answer = dttheme_wp_kses(get_post_meta ( $question_id, 'boolean-answer', TRUE ));
							
				
			} else if($question_type == 'gap-fill') {
	
				$text_before_gap = dttheme_wp_kses(get_post_meta ( $question_id, 'text-before-gap', TRUE ));
				$text_before_gap = !empty($text_before_gap) ? $text_before_gap : '';
				$text_gap = dttheme_wp_kses(get_post_meta ( $question_id, 'gap', TRUE ));
				$text_gap = !empty($text_gap) ? $text_gap : '';
				$text_after_gap = dttheme_wp_kses(get_post_meta ( $question_id, 'text-after-gap', TRUE ));
				$text_after_gap = !empty($text_after_gap) ? $text_after_gap : '';
				
				$answers = $text_before_gap.' <strong>'.$text_gap.'</strong> '.$text_after_gap;
				$correct_answer = $text_gap;
			
			} else if($question_type == 'single-line') {
							
				$answers = '';
				$correct_answer = dttheme_wp_kses(get_post_meta ( $question_id, 'singleline-answer', TRUE ));
	
			} else if($question_type == 'multi-line') {
							
				$answers = '';
				$correct_answer = dttheme_wp_kses(get_post_meta ( $question_id, 'multiline-answer', TRUE ));
				
			}
			
			$question_name = 'dt-question-'.$question_id;
			
			$user_answer = get_post_meta ( $post->ID, $question_name, TRUE );
			if(is_array($user_answer)) {
				$user_answer = implode('<br />',$user_answer);	
			}
			
			$question_grade = get_post_meta ( $post_id, 'question-id-'.$question_id.'-grade',true);
			//echo $question_grade.'<br>';
			
			echo '<tr id="dt-row-'.$question_id.'">
					<td>'.$i.'</td>
					<td>'.$question[0]->post_content.'</td>
					<td class="aligncenter">'.$answers.'</td>
					<td class="aligncenter" id="dt-correct-answer">'.$correct_answer.'</td>
					<td class="aligncenter" id="dt-user-answer">'.$user_answer.'</td>';
						if(isset($question_grade) && $question_grade == true) {               
							echo '<td class="aligncenter" id="dt-grade-html" data-grade="'.$quiz_question_grade[$i-1].'">'.$quiz_question_grade[$i-1].' / '.$quiz_question_grade[$i-1].'</td>
								  <td class="aligncenter" id="dt-grade-field">
									<div data-for="dt-question-id-'.$question_id.'-grade" data-grade="'.$quiz_question_grade[$i-1].'" data-quesid="'.$question_id.'" class="answer-switch answer-switch-on">Right</div>
									<input class="hidden" id="dt-question-id-'.$question_id.'-grade" type="checkbox" name="dt-question-id-'.$question_id.'-grade" value="true" checked="checked" />
								  </td>';
						} else {
							echo '<td class="aligncenter" id="dt-grade-html" data-grade="'.$quiz_question_grade[$i-1].'">0 / '.$quiz_question_grade[$i-1].'</td>
								  <td class="aligncenter" id="dt-grade-field">
									<div data-for="dt-question-id-'.$question_id.'-grade" data-grade="'.$quiz_question_grade[$i-1].'" data-quesid="'.$question_id.'" class="answer-switch answer-switch-off">Wrong</div>
									<input class="hidden" id="dt-question-id-'.$question_id.'-grade" type="checkbox" name="dt-question-id-'.$question_id.'-grade" value="false" />
								  </td>';
						}
				echo '</td>
				  </tr>';
				  
			$i++;
	
		}
		
		echo '</table>';
	
	}
	?>	

</div>

<?php
}
?>

<?php if(isset($prev_gradings) && !empty($prev_gradings)) { ?>
<div class="custom-box">
    <h3><?php _e('Previous Attempts', 'dt_themes'); ?></h3>
    <?php
    echo '<table border="0" cellpadding="0" cellspacing="10" style="width:100%;">
              <tr id="dt-first-row">
                <th scope="col" class="aligncenter">'.__('Attempt', 'dt_themes').'</th>
                <th scope="col" class="aligncenter">'.__('Mark', 'dt_themes').'</th>
                <th scope="col" class="aligncenter">'.__('Percentage', 'dt_themes').'</th>
              </tr>';
    
        foreach($prev_gradings as $grading) {
            echo '<tr>
                    <td class="aligncenter">'.$grading['attempts'].'</td>
                    <td class="aligncenter">'.$grading['mark'].'</td>
                    <td class="aligncenter">'.$grading['percentage'].'%</td>
                </tr>';
        }
    
    
    echo '</table>';
    ?>
</div>
<?php } ?>
