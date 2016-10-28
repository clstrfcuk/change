<?php
global $post;
$post_id = $post->ID;
$quiz_settings = get_post_meta ( $post->ID, '_quiz_settings', TRUE );
$quiz_settings = is_array ( $quiz_settings ) ? $quiz_settings : array (); 

?>

<!-- Layout -->
<div id="page-layout" class="custom-box ">
	<div class="column one-sixth">
		<label><?php _e('Layout','dt_themes');?> </label>
	</div>
	<div class="column five-sixth last">
		<ul class="bpanel-layout-set"><?php
			$layouts = array('content-full-width'=>'without-sidebar','with-left-sidebar'=>'left-sidebar','with-right-sidebar'=>	'right-sidebar', 'both-sidebar'=>'both-sidebar');
			
			$v =  array_key_exists("layout",$quiz_settings) ?  $quiz_settings['layout'] : 'content-full-width';
			foreach($layouts as $key => $value):
				$class = ($key == $v) ? " class='selected' " : "";
				echo "<li><a href='#' rel='{$key}' {$class}><img src='".IAMD_FW_URL."theme_options/images/columns/{$value}.png' alt='' /></a></li>";
			endforeach; ?>
		</ul>
		<?php $v = array_key_exists("layout",$quiz_settings) ? $quiz_settings['layout'] : 'content-full-width';?>
		<input id="mytheme-course-layout" name="layout" type="hidden" value="<?php echo $v;?>" />
		<p class="note"> <?php _e("You can choose between a left, right or full width.",'dt_themes');?> </p>
	</div>
</div>
<!-- Layout End-->

<?php 
$sb_layout = array_key_exists("layout",$quiz_settings) ? $quiz_settings['layout'] : 'content-full-width';
$sidebar_both = $sidebar_left = $sidebar_right = '';
if($sb_layout == 'content-full-width') {
	$sidebar_both = 'style="display:none;"'; 
} elseif($sb_layout == 'with-left-sidebar') {
	$sidebar_right = 'style="display:none;"'; 
} elseif($sb_layout == 'with-right-sidebar') {
	$sidebar_left = 'style="display:none;"'; 
} 
?>
<div id="widget-area-options" <?php echo $sidebar_both;?>>

    <div id="left-sidebar-container" class="page-left-sidebar" <?php echo $sidebar_left; ?>>
        <!-- 2. Every Where Sidebar Left Start -->
        <div id="page-commom-sidebar" class="sidebar-section custom-box">
            <div class="column one-sixth"><label><?php _e('Disable Every Where Sidebar Left','dt_themes');?></label></div>
            <div class="column five-sixth last"><?php 
                $switchclass = array_key_exists("disable-everywhere-sidebar-left",$quiz_settings) ? 'checkbox-switch-on' :'checkbox-switch-off';
                $checked = array_key_exists("disable-everywhere-sidebar-left",$quiz_settings) ? ' checked="checked"' : '';?>
                
                <div data-for="mytheme-disable-everywhere-sidebar-left" class="checkbox-switch <?php echo $switchclass;?>"></div>
                <input id="mytheme-disable-everywhere-sidebar-left" class="hidden" type="checkbox" name="disable-everywhere-sidebar-left" value="true"  <?php echo $checked;?>/>
                <p class="note"> <?php _e('Yes! to hide "Every Where Sidebar" on this page.','dt_themes');?> </p>
             </div>
        </div><!-- Every Where Sidebar Left End-->
    
        <!-- 3. Choose Widget Areas Start -->
        <div id="page-sidebars" class="sidebar-section custom-box page-widgetareas">
            <div class="column one-sixth"><label><?php _e('Choose Widget Area - Left Sidebar','dt_themes');?></label></div>
            <div class="column five-sixth last"><?php
                if( array_key_exists('widget-area-left', $quiz_settings)):
                    $widgetareas =  array_unique($quiz_settings["widget-area-left"]);
                    $widgetareas = array_filter($widgetareas);
                    foreach( $widgetareas as $widgetarea ){
                        echo '<div class="multidropdown">';
                        echo dttheme_custom_widgetarea_list("widgetareas-left",$widgetarea,"multidropdown","sidebars");
                        echo '</div>';
                    }
                    echo '<div class="multidropdown">';
                        echo dttheme_custom_widgetarea_list("widgetareas-left","","multidropdown","sidebars");
                    echo '</div>';                                
                else:
                    echo '<div class="multidropdown">';
                       echo dttheme_custom_widgetarea_list("widgetareas-left","","multidropdown","sidebars");
                    echo '</div>';                                
                endif;?>
            </div>
        </div><!-- Choose Widget Areas End -->
    </div>
    
    <div id="right-sidebar-container" class="page-right-sidebar" <?php echo $sidebar_right; ?>>
        <!-- 3. Every Where Sidebar Right Start -->
        <div id="page-commom-sidebar" class="sidebar-section custom-box page-right-sidebar">
            <div class="column one-sixth"><label><?php _e('Disable Every Where Sidebar Right','dt_themes');?></label></div>
            <div class="column five-sixth last"><?php 
                $switchclass = array_key_exists("disable-everywhere-sidebar-right",$quiz_settings) ? 'checkbox-switch-on' :'checkbox-switch-off';
                $checked = array_key_exists("disable-everywhere-sidebar-right",$quiz_settings) ? ' checked="checked"' : '';?>
                
                <div data-for="mytheme-disable-everywhere-sidebar-right" class="checkbox-switch <?php echo $switchclass;?>"></div>
                <input id="mytheme-disable-everywhere-sidebar-right" class="hidden" type="checkbox" name="disable-everywhere-sidebar-right" value="true"  <?php echo $checked;?>/>
                <p class="note"> <?php _e('Yes! to hide "Every Where Sidebar" on this page.','dt_themes');?> </p>
             </div>
        </div><!-- Every Where Sidebar Right End-->
        
        <!-- 3. Choose Widget Areas Start -->
        <div id="page-sidebars" class="sidebar-section custom-box page-widgetareas">
            <div class="column one-sixth"><label><?php _e('Choose Widget Area - Right Sidebar','dt_themes');?></label></div>
            <div class="column five-sixth last"><?php
                if( array_key_exists('widget-area-right', $quiz_settings)):
                    $widgetareas =  array_unique($quiz_settings["widget-area-right"]);
                    $widgetareas = array_filter($widgetareas);
                    foreach( $widgetareas as $widgetarea ){
                        echo '<div class="multidropdown">';
                        echo dttheme_custom_widgetarea_list("widgetareas-right",$widgetarea,"multidropdown","sidebars");
                        echo '</div>';
                    }
                    echo '<div class="multidropdown">';
                        echo dttheme_custom_widgetarea_list("widgetareas-right","","multidropdown","sidebars");
                    echo '</div>';                                
                else:
                    echo '<div class="multidropdown">';
                       echo dttheme_custom_widgetarea_list("widgetareas-right","","multidropdown","sidebars");
                    echo '</div>';                                
                endif;?>
            </div>
        </div><!-- Choose Widget Areas End -->
    </div>

</div>

<!-- Subtitle & Duration -->
<div class="custom-box">

	<div class="column one-half">
    
        <div class="column one-third">
           <label><?php _e('Subtitle','dt_themes');?></label>
        </div>
        <div class="column two-third last">
			<?php $quiz_subtitle = get_post_meta ( $post_id, "quiz-subtitle",true); ?>
            <input id="quiz-subtitle" name="quiz-subtitle" class="large" type="text" value="<?php echo $quiz_subtitle;?>" style="width:100%;" />
            <p class="note"> <?php _e("Subtitle for this Quiz.",'dt_themes');?> </p>
        </div>

	</div>
	<div class="column one-half last">
    
        <div class="column one-third">
            <label><?php _e('Duration','dt_themes');?></label>
        </div>
        <div class="column two-third last">
			<?php $quiz_duration = get_post_meta ( $post_id, "quiz-duration",true); ?>
            <input id="quiz-duration" name="quiz-duration" class="large" type="number" value="<?php echo $quiz_duration;?>" />
            <p class="note"> <?php _e("Quiz duration in minutes which enables timer.",'dt_themes');?> </p>
        </div>

	</div>
    
</div>
<!-- Subtitle & Duration End -->

<!-- Number Of Retakes & Post Quiz Message -->
<div class="custom-box">

	<div class="column one-half">
    
        <div class="column one-third">
           <label><?php _e('Number Of Retakes','dt_themes');?></label>
        </div>
        <div class="column two-third last">
			<?php $quiz_retakes = get_post_meta ( $post_id, "quiz-retakes",true); ?>
            <input id="quiz-retakes" name="quiz-retakes" class="large" type="number" value="<?php echo $quiz_retakes;?>" />
            <p class="note"> <?php _e("Number of retakes allowed for student.",'dt_themes');?> </p>
        </div>

	</div>
	<div class="column one-half last">
    
        <div class="column one-third">
            <label><?php _e('Post Quiz Message','dt_themes');?></label>
        </div>
        <div class="column two-third last">
			<?php $quiz_postmsg = get_post_meta ( $post_id, "quiz-postmsg",true); ?>
            <textarea id="quiz-postmsg" name="quiz-postmsg" style="width:100%;"><?php echo $quiz_postmsg;?></textarea>
            <p class="note"> <?php _e("Message to display once quiz submitted.",'dt_themes');?> </p>
        </div>

	</div>
    
</div>
<!-- Number Of Retakes & Post Quiz Message End -->

<!-- Randomize Questions & Auto Evaluation -->
<div class="custom-box">

	<div class="column one-half">
    
        <div class="column one-third">
           <label><?php _e('Randomize Questions','dt_themes');?></label>
        </div>
        <div class="column two-third last">
			<?php
			$quiz_randomize_questions = get_post_meta ( $post_id, "quiz-randomize-questions",true);
            $switchclass = ($quiz_randomize_questions != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($quiz_randomize_questions != '') ? ' checked="checked"' : '';
            ?>
            <div data-for="quiz-randomize-questions" class="dt-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="quiz-randomize-questions" class="hidden" type="checkbox" name="quiz-randomize-questions" value="true" <?php echo $checked;?> />
            <p class="note"> <?php _e('Would you like to randomize the questions order automatically everytime ?','dt_themes');?> </p>
        </div>

	</div>
	<div class="column one-half last">
    
        <div class="column one-third">
            <label><?php _e('Enable Auto Evaluation','dt_themes');?></label>
        </div>
        <div class="column two-third last">
			<?php
			$quiz_auto_evaluation = get_post_meta ( $post_id, "quiz-auto-evaluation",true);
            $switchclass = ($quiz_auto_evaluation != '') ? 'checkbox-switch-on' : 'checkbox-switch-off';
            $checked = ($quiz_auto_evaluation != '') ? ' checked="checked"' : '';
            ?>
            <div data-for="quiz-auto-evaluation" class="dt-checkbox-switch <?php echo $switchclass;?>"></div>
            <input id="quiz-auto-evaluation" class="hidden" type="checkbox" name="quiz-auto-evaluation" value="true" <?php echo $checked;?> />
            <p class="note"> <?php _e('Would you like to enable auto evaluate questions ?. Enabling this will grade quiz automatically and it will mark as completed.','dt_themes');?> </p>
        </div>

	</div>
    
</div>
<!-- Randomize Questions & Auto Evaluation End -->

<!-- Add Questions -->
<div class="custom-box">

	<div class="column one-sixth dt-add-quiz">
    
       <label><?php _e('Add Questions','dt_themes');?></label>

	</div>
	<div class="column five-sixth last">
    
        <?php
        $questions_args = array( 'post_type' => 'dt_questions', 'numberposts' => -1, 'orderby' => 'date', 'order' => 'DESC', 'suppress_filters' => FALSE );
        $questions_array = get_posts( $questions_args );
		?>		
    
    	<div id="dt-quiz-questions-container">
        
        	<?php 
			$quiz_question = get_post_meta ( $post_id, "quiz-question",true); 
			$quiz_question_grade = get_post_meta ( $post_id, "quiz-question-grade",true); 
			$quiz_total_grade = get_post_meta ( $post_id, "quiz-total-grade",true); 
			
			$j = 0;
			if(isset($quiz_question) && is_array($quiz_question)) {
				foreach($quiz_question as $sel_question) {
				?>
					<div id="dt-question-box">
						<?php
						$out = '';
						$out .= '<select id="dt-quiz-question" name="dt-quiz-question[]" data-placeholder="'.__('Choose a Question...', 'dt_themes').'" class="dt-chosen-select" style="width:40%;">' . "\n";
						$out .= '<option value=""></option>';
						if ( count( $questions_array ) > 0 ) {
							foreach ($questions_array as $question){
								$out .= '<option value="' . esc_attr( $question->ID ) . '"' . selected( $question->ID, $sel_question, false ) . '>' . esc_html( $question->post_title ) . '</option>' . "\n";
							}
						}
						$out .= '</select>' . "\n";
						echo $out;
						?>
						<input type="number" id="dt-quiz-question-grade" name="dt-quiz-question-grade[]" value="<?php echo $quiz_question_grade[$j]; ?>" />
						<span class="dt-remove-question">X</span>
					</div>
				<?php
				$j++;
				}
			}
			?>
            
        </div>
		
        <a href="#" class="dt-add-questions custom-button-style"><?php _e('Add Questions', 'dt_themes'); ?></a>
        
        <p class="note"> <?php _e('You can question along with its mark here.','dt_themes');?> </p>
        <div class="hr_invisible"></div>
        
        <div id="dt-total-marks-container"><?php _e('Total Marks : ', 'dt_themes'); ?><span><?php echo $quiz_total_grade; ?></span> <input type="hidden" id="dt-quiz-total-grade" name="dt-quiz-total-grade" value="<?php echo $quiz_total_grade; ?>" /></div>
        
    	<div id="dt-questions-to-clone" class="hidden">
        
			<?php
            $out = '';
            $out .= '<select data-placeholder="'.__('Choose a Question...', 'dt_themes').'" style="width:40%;">' . "\n";
            $out .= '<option value=""></option>';
            if ( count( $questions_array ) > 0 ) {
                foreach ($questions_array as $question){
                    $out .= '<option value="' . esc_attr( $question->ID ) . '">' . esc_html( $question->post_title ) . '</option>' . "\n";
                }
            }
            $out .= '</select>' . "\n";
            echo $out;
            ?>
            <input type="number" value="" />
            <span class="dt-remove-question">X</span>
        
        </div>
    
	</div>
    
</div>
<!-- Add Questions End -->

<!-- Pass Percentage -->
<div class="custom-box">

	<div class="column one-half">
    
        <div class="column one-third">
           <label><?php _e('Pass Percentage','dt_themes');?></label>
        </div>
        <div class="column two-third last">
			<?php $quiz_pass_percentage = get_post_meta ( $post_id, "quiz-pass-percentage",true); ?>
            <input id="quiz-pass-percentage" name="quiz-pass-percentage" class="large" type="number" value="<?php echo $quiz_pass_percentage;?>" /><?php _e("%",'dt_themes');?>
            <p class="note"> <?php _e("Pass precentage for this quiz.",'dt_themes');?> </p>
        </div>

	</div>
	<div class="column one-half last">
    
       &nbsp;

	</div>
</div>
<!-- Pass Percentage End -->

<div class="custom-box">
     <p class="note"> <?php _e("<strong>Note:</strong> Once quiz assigned for one lesson, then it cannot be assigned for another.",'dt_themes');?> </p>
</div>