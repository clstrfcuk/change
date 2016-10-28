<?php
global $post;
$post_id = $post->ID;

$question_type = get_post_meta ( $post->ID, 'question-type', TRUE );
$multichoice_answers = get_post_meta ( $post->ID, 'multichoice-answers', TRUE );
$multichoice_correct_answer = get_post_meta ( $post->ID, 'multichoice-correct-answer', TRUE );
$multicorrect_answers = get_post_meta ( $post->ID, 'multicorrect-answers', TRUE );
$multicorrect_correct_answer = get_post_meta ( $post->ID, 'multicorrect-correct-answer', TRUE );
$multicorrect_correct_answer = is_array($multicorrect_correct_answer) ? $multicorrect_correct_answer : array();
$boolean_answer = get_post_meta ( $post->ID, 'boolean-answer', TRUE );
$text_before_gap = get_post_meta ( $post->ID, 'text-before-gap', TRUE );
$gap = get_post_meta ( $post->ID, 'gap', TRUE );
$text_after_gap = get_post_meta ( $post->ID, 'text-after-gap', TRUE );
$singleline_answer = get_post_meta ( $post->ID, 'singleline-answer', TRUE );
$multiline_answer = get_post_meta ( $post->ID, 'multiline-answer', TRUE );
$answer_explanation = get_post_meta ( $post->ID, 'answer-explanation', TRUE );

$hide_multichoice = $hide_multicorrect = $hide_boolean = $hide_gapfill = $hide_singleline = $hide_multiline = 'hidden';

if($question_type == 'multiple-choice') $hide_multichoice = '';
else if($question_type == 'multiple-correct') $hide_multicorrect = '';
else if($question_type == 'boolean') $hide_boolean = '';
else if($question_type == 'gap-fill') $hide_gapfill = '';
else if($question_type == 'single-line') $hide_singleline = '';
else if($question_type == 'multi-line')$hide_multiline = '';
else if($question_type == '') $hide_multichoice = '';

?>

<!-- Question Type -->
<div class="custom-box">

    <div class="column one-sixth">
       <label><?php _e('Question Type', 'dt_themes'); ?></label>
    </div>
    <div class="column five-sixth last">
        <select id="dt-question-type" name="dt_question_type" class="large">
            <option value="multiple-choice" <?php selected( 'multiple-choice', $question_type, true ); ?>><?php _e('Multiple Choice', 'dt_themes'); ?></option>
            <option value="multiple-correct" <?php selected( 'multiple-correct', $question_type, true ); ?>><?php _e('Multiple Correct', 'dt_themes'); ?></option>
            <option value="boolean" <?php selected( 'boolean', $question_type, true ); ?>><?php _e('True / False', 'dt_themes'); ?></option>
            <option value="gap-fill" <?php selected( 'gap-fill', $question_type, true ); ?>><?php _e('Gap Fill', 'dt_themes'); ?></option>
            <option value="single-line" <?php selected( 'single-line', $question_type, true ); ?>><?php _e('Single Line', 'dt_themes'); ?></option>
            <option value="multi-line" <?php selected( 'multi-line', $question_type, true ); ?>><?php _e('Multi Line', 'dt_themes'); ?></option>
        </select>
        <p class="note"> <?php _e('Choose type of question here.','dt_themes');?> </p>
    </div>
    
</div>
<!-- Question Type End -->

<!-- Anwsers -->
<div class="custom-box">

    <div class="column one-sixth">
       <label><?php _e('Answers', 'dt_themes'); ?></label>
    </div>
    <div class="column five-sixth last">
    
        <div class="dt-answers dt-multiple-choice-answers <?php echo $hide_multichoice; ?>">
        
            <div id="dt-multichoice-answers-container">
            	<?php 
				if(!empty($multichoice_answers)) {
					$i = 0;
					foreach($multichoice_answers as $answer) { 
						if($answer == $multichoice_correct_answer) $chk_str = 'checked="checked"'; else $chk_str = '';
					?>
						<div id="dt-answer-holder">
							<input type="text" id="dt_multichoice_answers" name="dt_multichoice_answers[]" value="<?php echo $answer; ?>" class="large">
							<input id="dt-multichoice-correct-answer" type="radio" name="dt-multichoice-correct-answer" value="<?php echo $i; ?>" <?php echo $chk_str; ?>>
							<span class="dt-remove-multichoice-answer">X</span>
						</div>
					<?php 
					$i++;
					} 
					$multichoice_cnt = $i-1;
				} else {
					for($i = 0; $i <= 3; $i++) {
						if($i == 0) $chk_str = 'checked="checked"'; else $chk_str = '';
					?>
						<div id="dt-answer-holder">
							<input type="text" id="dt_multichoice_answers" name="dt_multichoice_answers[]" value="" class="large">
							<input id="dt-multichoice-correct-answer" type="radio" name="dt-multichoice-correct-answer" value="<?php echo $i; ?>" <?php echo $chk_str; ?>>
							<span class="dt-remove-multichoice-answer">X</span>
						</div>
					<?php 
					}
					$multichoice_cnt = 3;
				}
				?>
            </div>
                       
            <a href="#" class="dt-add-multichoice-answer custom-button-style"><?php _e('Add answer', 'dt_themes'); ?></a>
            
            <div class="dt-multichoice-answer-clone hidden">
                <div id="dt-answer-holder">
                    <input type="text" id="dt_multichoice_answers" name="dt_multichoice_answers[]" value="" class="large">
                     <input id="dt-multichoice-correct-answer" type="radio" name="dt-multichoice-correct-answer" value="<?php echo $multichoice_cnt; ?>">
                    <span class="dt-remove-multichoice-answer">X</span>
                </div>     
                <input type="text" name="dt_multichoice_answers_cnt" id="dt_multichoice_answers_cnt" value="<?php echo $multichoice_cnt; ?>" />      
            </div>
            
           	<p class="note"> <?php _e('Type your answers here and select the correct answer.','dt_themes');?> </p> 
            
        </div>
        
        <div class="dt-answers dt-multiple-correct-answers  <?php echo $hide_multicorrect; ?>">
        
            <div id="dt-multicorrect-answers-container">
            	<?php 
				if(!empty($multicorrect_answers)) {
					$i = 0;
					foreach($multicorrect_answers as $answer) { 
						if(in_array($answer, $multicorrect_correct_answer)) $chk_str = 'checked="checked"'; else $chk_str = '';
					?>
                        <div id="dt-answer-holder">
                            <input type="text" id="dt_multicorrect_answers" name="dt_multicorrect_answers[]" value="<?php echo $answer; ?>" class="large">
                            <input id="dt-multicorrect-correct-answer" type="checkbox" name="dt-multicorrect-correct-answer[]" value="<?php echo $i; ?>" <?php echo $chk_str; ?>>
                            <span class="dt-remove-multicorrect-answer">X</span>
                        </div>
					<?php 
					$i++;
					} 
					$multicorrect_cnt = $i-1;
				} else {
					for($i = 0; $i <= 3; $i++) {
					?>
                        <div id="dt-answer-holder">
                            <input type="text" id="dt_multicorrect_answers" name="dt_multicorrect_answers[]" value="" class="large">
                            <input id="dt-multicorrect-correct-answer" type="checkbox" name="dt-multicorrect-correct-answer[]" value="<?php echo $i; ?>">
                            <span class="dt-remove-multicorrect-answer">X</span>
                        </div>
					<?php 
					}
					$multicorrect_cnt = 3;
				}
				?>
            </div>
                       
            <a href="#" class="dt-add-multicorrect-answer custom-button-style"><?php _e('Add answer', 'dt_themes'); ?></a>
            
            <div class="dt-multicorrect-answer-clone hidden">
                <div id="dt-answer-holder">
                    <input type="text" id="dt_multicorrect_answers" name="dt_multicorrect_answers[]" value="" class="large">
                     <input id="dt-multicorrect-correct-answer" type="checkbox" name="dt-multicorrect-correct-answer[]" value="<?php echo $multicorrect_cnt; ?>">
                    <span class="dt-remove-multicorrect-answer">X</span>
                </div>     
                <input type="text" name="dt_multicorrect_answers_cnt" id="dt_multicorrect_answers_cnt" value="<?php echo $multicorrect_cnt; ?>" />      
            </div>
            
            <p class="note"> <?php _e('Type your answers here and select the correct answers.','dt_themes');?> </p>
            
        </div>
        
        <div class="dt-answers dt-boolean-answers  <?php echo $hide_boolean; ?>">
        
            <label for="lbl_boolean">
                <input id="dt-boolean-answer-true" type="radio" name="dt-boolean-answer" value="true" <?php if($boolean_answer == 'true' || empty($boolean_answer)) echo 'checked="checked"'; ?>> <?php _e('True', 'dt_themes'); ?>
            </label>
            <label for="lbl_boolean">
                <input id="dt-boolean-answer-false" type="radio" name="dt-boolean-answer" value="false" <?php if($boolean_answer == 'false') echo 'checked="checked"'; ?>> <?php _e('False', 'dt_themes'); ?>
            </label>
            
        </div>

        <div class="dt-answers dt-gap-fill-answers  <?php echo $hide_gapfill; ?>">
            
            <div class="column one-sixth">
               <label><?php _e('Text Before Gap', 'dt_themes'); ?></label>
            </div>
            <div class="column five-sixth last">
                <input type="text" id="dt_text_before_gap" name="dt_text_before_gap" value="<?php echo $text_before_gap; ?>" class="large">
            </div>

            <div class="column one-sixth">
               <label><?php _e('Gap', 'dt_themes'); ?></label>
            </div>
            <div class="column five-sixth last">
                <input type="text" id="dt_gap" name="dt_gap" value="<?php echo $gap; ?>" class="large">
            </div>

            <div class="column one-sixth">
               <label><?php _e('Text After Gap', 'dt_themes'); ?></label>
            </div>
            <div class="column five-sixth last">
                <input type="text" id="dt_text_after_gap" name="dt_text_after_gap" value="<?php echo $text_after_gap; ?>" class="large">
            </div>

        </div>

        <div class="dt-answers dt-single-line-answers  <?php echo $hide_singleline; ?>">
           
           	<input type="text" id="dt_singleline_answer" name="dt_singleline_answer" value="<?php echo $singleline_answer; ?>" class="large">
            
        </div>

        <div class="dt-answers dt-multi-line-answers  <?php echo $hide_multiline; ?>">
            
            <textarea id="dt_multiline_answer" name="dt_multiline_answer" class="large" rows="8" cols="8"><?php echo $multiline_answer; ?></textarea>
            
        </div>
        
    </div>
    
</div>
<!-- Anwsers End -->

<!-- Anwser Explanation -->
<div class="custom-box">

    <div class="column one-sixth">
       <label><?php _e('Answer Explanation', 'dt_themes'); ?></label>
    </div>
    <div class="column five-sixth last">
        <textarea id="dt_answer_explanation" name="dt_answer_explanation" class="large" rows="8" cols="8"><?php echo $answer_explanation; ?></textarea>
        <p class="note"> <?php _e('You can provide explanantion for the answer here.','dt_themes');?> </p>
    </div>
    
</div>
<!-- Anwser Explanation End -->
