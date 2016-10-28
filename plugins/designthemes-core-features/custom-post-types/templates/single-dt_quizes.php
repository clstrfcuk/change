<?php 
get_header();

//GETTING META VALUES...
$quiz_settings = get_post_meta($post->ID, '_quiz_settings', true);
$quiz_settings = is_array( $quiz_settings ) ? $quiz_settings  : array();

if($GLOBALS['force_enable'] == true)
	$page_layout = $GLOBALS['page_layout'];
else
	$page_layout = !empty($quiz_settings['layout']) ? $quiz_settings['layout'] : 'content-full-width';

$show_sidebar = $show_left_sidebar = $show_right_sidebar =  false;
$sidebar_class = "";

switch ( $page_layout ) {
	case 'with-left-sidebar':
		$page_layout = "page-with-sidebar with-left-sidebar";
		$show_sidebar = $show_left_sidebar = true;
		$sidebar_class = "secondary-has-left-sidebar";
	break;

	case 'with-right-sidebar':
		$page_layout = "page-with-sidebar with-right-sidebar";
		$show_sidebar = $show_right_sidebar	= true;
		$sidebar_class = "secondary-has-right-sidebar";
	break;

	case 'both-sidebar':
		$page_layout = "page-with-sidebar page-with-both-sidebar";
		$show_sidebar = $show_right_sidebar	= $show_left_sidebar = true;
		$sidebar_class = "secondary-has-both-sidebar";
	break;

	case 'content-full-width':
	default:
		$page_layout = "content-full-width";
	break;
}

$ts = get_post_meta($post->ID, '_teacher_settings', true);

if ( $show_sidebar ):
	if ( $show_left_sidebar ): ?>
		<!-- Secondary Left -->
		<section id="secondary-left" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'left' );?></section><?php
	endif;
endif;
?>

<!-- ** Primary Section ** -->
<section id="primary" class="<?php echo $page_layout;?>">

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    
    	<?php
		
		$lesson_args = array('post_type' => 'dt_lessons', 'meta_key' => 'lesson-quiz', 'meta_value' => $post->ID, 'hierarchical' => 0 );
		$lessons = get_pages( $lesson_args );
		
		if(isset($lessons[0])) {
			
			$dt_lesson_course = get_post_meta($lessons[0]->ID, "dt_lesson_course",true);
			$starting_price = get_post_meta($dt_lesson_course, 'starting-price', true);
			$s2_level = "access_s2member_ccap_cid_{$dt_lesson_course}";
			
			$lesson_settings = get_post_meta($lessons[0]->ID, '_lesson_settings', true);
			$private_lesson = !empty($lesson_settings['private-lesson']) ? $lesson_settings['private-lesson'] : '';
			
			$user_id = get_current_user_id();
			
			if(IAMD_USER_ROLE == 's2member_level2' || IAMD_USER_ROLE == 's2member_level3' || IAMD_USER_ROLE == 's2member_level4' || current_user_can($s2_level) || $private_lesson == '') {
			
				if( have_posts() ): while( have_posts() ): the_post();
					$quiz_id = get_the_ID();
				?>
					
                    <?php 
					$quiz_subtitle = dttheme_wp_kses(get_post_meta ( $quiz_id, "quiz-subtitle",true));
					if(isset($quiz_subtitle) && $quiz_subtitle != '')
						echo '<h3>'.$quiz_subtitle.'</h3>';
					?>
			
					<div class="dt-sc-clear"></div>
					<div class="entry">
						<?php the_content(); ?>    
					</div>    
			
					<div class="dt-sc-clear"></div>
					<div class="dt-sc-hr-invisible-small"></div>
		
                    <div style="display:none;" id="dt-quiz-attributes" data-course_id="<?php echo $dt_lesson_course; ?>" data-lesson_id="<?php echo $lessons[0]->ID; ?>" data-quiz_id="<?php echo $quiz_id; ?>" data-user_id="<?php echo $user_id; ?>"></div>
                    <div id="dt-sc-ajax-load-image" style="display:none;"><img src="<?php echo IAMD_BASE_URL."images/loading.png"; ?>" alt="" /></div>
                    <div id="dt-quiz-questions-container">
                    
						<?php 
                        
                        if(isset($_REQUEST['dttype']) && $_REQUEST['dttype'] != '') {
                            
                            dt_list_questions_with_answers($dt_lesson_course, $lessons[0]->ID);
                            
                        } else {
                            
                            if(IAMD_USER_ROLE == 's2member_level2' || IAMD_USER_ROLE == 's2member_level3' || IAMD_USER_ROLE == 's2member_level4' || current_user_can($s2_level) || (is_user_logged_in() && $private_lesson == '')) {
								
                                $user_id = get_current_user_id();
                                
                                $dt_gradings = dt_get_user_gradings_array($dt_lesson_course, $lessons[0]->ID, $quiz_id, $user_id);
                                $dt_grade_post = get_posts( $dt_gradings );
                                
                                $quiz_duration = dttheme_wp_kses(get_post_meta ( $quiz_id, "quiz-duration",true));
                                $quiz_duration = (isset($quiz_duration) && $quiz_duration > 0) ? $quiz_duration : 0;
            
								if(isset($dt_grade_post[0])) {
									
									$grade = get_post_meta ( $dt_grade_post[0]->ID, "graded",true);
									if(isset($grade) && $grade != '') {
										
										echo '<div class="dt-sc-info-box">'.__('Your quiz have been graded already, please check your dashboard for futher details!', 'dt_themes').'</div>';	
										
									} else {
										
										if(dt_can_user_retake_quiz($dt_lesson_course, $lessons[0]->ID, $quiz_id, $user_id)) {
										
											if($quiz_duration > 0) {
												echo '<p class="dt-sc-info-box">';
												echo '<strong>'.__('Note: ', 'dt_themes').'</strong>';
												echo sprintf( __('You have to complete the quiz in %dmin(s). Timer will be triggered once you press the "Start Quiz" button.', 'dt_themes'), $quiz_duration );
												echo '</p>';
											}
											echo '<a class="dt-sc-button small filled" name="start_quiz" id="dt-start-quiz">'.__('Start Quiz','dt_themes').'</a>';
											
										} else {
											echo '<div class="dt-sc-info-box">'.__('You may exceeded number of attempts to retake the quiz or you may not have permission to retake this quiz!.', 'dt_themes').'</div>';
										}
										
									}
									
								} else {
									
									if($quiz_duration > 0) {
										echo '<p class="dt-sc-info-box">';
										echo '<strong>'.__('Note: ', 'dt_themes').'</strong>';
										echo sprintf( __('You have to complete the quiz in %dmin(s). Timer will be triggered once you press the "Start Quiz" button.', 'dt_themes'), $quiz_duration );
										echo '</p>';
									}
									
									echo '<a class="dt-sc-button small filled" name="start_quiz" id="dt-start-quiz">'.__('Start Quiz','dt_themes').'</a>';
									
								}
								
								echo '<a href="'.get_permalink($dt_lesson_course).'" class="dt-sc-button small back-to-course">'.__('Back to ', 'dt_themes').get_the_title($dt_lesson_course).'</a>';	
                                
                            } else if(!is_user_logged_in()) {
                                echo '<div class="dt-sc-warning-box">'.__('Please login to get access to the quiz', 'dt_themes').'</div>';
                            }
                            
                        }
                        
                        ?>
                    
                    </div>
                    
				<?php    
				endwhile; endif;	
			
			} elseif($starting_price == '' && $private_lesson != '') {
				echo '<div class="dt-sc-warning-box">';
				echo __('This lesson is marked as private you can\'t view its content.', 'dt_themes');
				echo '</div>';
			} else {
				echo '<div class="dt-sc-warning-box">';
				echo sprintf( __('You have to purchase the course %s to get access to this quiz.', 'dt_themes'), '<a href="'.get_permalink($dt_lesson_course).'">'.get_the_title($dt_lesson_course).'</a>' );
				echo '</div>';
				echo '<a href="'.get_permalink($dt_lesson_course).'" target="_self"  class="dt-sc-button small">'.__('Purchase Now', 'dt_themes').'</a>';
			}
			
		} else {
			echo '<div class="dt-sc-warning-box">'.__('This quiz not yet assigned for any lesson, please contact your teacher for futher instructions!', 'dt_themes').'</div>';
		}
		
        ?>
        
   </article>     

</section><!-- ** Primary Section End ** -->
<?php
if ( $show_sidebar ):
	if ( $show_right_sidebar ): ?>
		<!-- Secondary Right -->
		<section id="secondary-right" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'right' );?></section><?php
	endif;
endif;
?>
<?php get_footer(); ?>