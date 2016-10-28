<?php get_header();

	//GETTING META VALUES...
	$lesson_settings = get_post_meta($post->ID, '_lesson_settings', true);
	
	if($GLOBALS['force_enable'] == true)
		$page_layout = $GLOBALS['page_layout'];
	else
		$page_layout = !empty($lesson_settings['layout']) ? $lesson_settings['layout'] : 'content-full-width';
	
	$private_lesson = !empty($lesson_settings['private-lesson']) ? $lesson_settings['private-lesson'] : '';

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

	$ts = get_post_meta($post->ID, '_lesson_settings', true);
	
	if ( $show_sidebar ):
		if ( $show_left_sidebar ): ?>
			<!-- Secondary Left -->
			<section id="secondary-left" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'left' );?></section><?php
		endif;
	endif;?>

	<!-- ** Primary Section ** -->
	<section id="primary" class="<?php echo $page_layout;?>">
    <?php 
	
	$dt_lesson_course = get_post_meta ($post->ID, "dt_lesson_course",true);
	$starting_price = get_post_meta($dt_lesson_course, 'starting-price', true);
	$s2_level = "access_s2member_ccap_cid_{$dt_lesson_course}";
	
	if ( current_user_can($s2_level) || IAMD_USER_ROLE == 's2member_level2' || IAMD_USER_ROLE == 's2member_level3' || IAMD_USER_ROLE == 's2member_level4' || ($private_lesson == '') ){
		
		if( have_posts() ): while( have_posts() ): the_post();
			$the_id = get_the_ID(); 
			$dt_lesson_course = get_post_meta ( $the_id, "dt_lesson_course",true);
			?>
           <div class="column dt-sc-two-third first dt-sc-lessons-content-container">
			<article id="post-<?php the_ID(); ?>" <?php post_class('dt-sc-lesson-single'); ?>>
				<h2><?php the_title(); ?> </h2>
								
	
				<div class="lesson-metadata">
	
				  <?php
                    $dt_lesson_quiz = get_post_meta ($the_id, "lesson-quiz", true);
                    $user_id = get_current_user_id();
                    
                    if(!isset($dt_lesson_quiz) || $dt_lesson_quiz == '') $dt_lesson_quiz = -1;
                    
                    $dt_gradings = dt_get_user_gradings_array($dt_lesson_course, $the_id, $dt_lesson_quiz, $user_id);
                    $dt_grade_post = get_posts( $dt_gradings );
                    
                    if(isset($dt_grade_post[0])) {
                        
                        $grade = get_post_meta ( $dt_grade_post[0]->ID, "graded",true);
                        if(isset($grade) && $grade != '') {
                            echo '<div class="dt-sc-lesson-completed"> <span class="fa fa-check-circle"> </span> '.__('Completed', 'dt_themes').'</div>';
                        }
                        
                    } elseif(isset( $_POST['complete_lesson']) && wp_verify_nonce($_POST['dt_complete_lesson_noonce'], 'dt_complete_lesson_noonce')) {
                        
                        dt_mark_lesson_complete($the_id, $dt_lesson_course);
                        echo '<div class="dt-sc-lesson-completed"> <span class="fa fa-check-circle"> </span> '.__('Completed', 'dt_themes').'</div>';
                        
                    }
                    ?>

					<?php 
						$duration = isset($lesson_settings['lesson-duration']) ? dttheme_wp_kses($lesson_settings['lesson-duration']) : 0; 
						if($duration > 0) {
							$hours = floor($duration/60); 
							$mins = $duration % 60; 
							if($hours == 0) {
								$duration = $mins . __(' mins ', 'dt_themes'); 				
							} elseif($hours == 1) {
								$duration = $hours .  __(' hour ', 'dt_themes') . $mins . __(' mins ', 'dt_themes'); 				
							} else {
								$duration = $hours . __(' hours ', 'dt_themes') . $mins . __(' mins ', 'dt_themes'); 				
							}
						}
					?>
	
				    <?php the_terms($post->ID,'lesson_complexity', '<p><i class="fa fa-tags"> </i>', ', ', '</p>'); ?> 
					<?php if($duration > 0) { ?><p><i class="fa fa-clock-o"> </i> <?php echo $duration; ?> </p><?php } ?>
					<p> <i class="fa fa-book"> </i>
						<?php
						if(isset($dt_lesson_course) && $dt_lesson_course != '') {
							$course_data = get_post($dt_lesson_course);
							echo '<a href="'.get_permalink($course_data->ID).'">'.$course_data->post_title.'</a>';	
						}
						?>
					</p>
					
				</div>
				
				<div class="dt-sc-clear"></div>
				<div class="dt-sc-hr-invisible-small"></div>
				<div class="entry">
				  <?php the_content(); ?>
				</div>   
                 
				<?php 
				$lesson_video = get_post_meta($the_id, 'lesson-video', true);
				if(isset($lesson_video) && $lesson_video != '') { ?>
					<div class="dt-sc-hr-invisible"></div>
                    <h4 class="border-title"><?php _e('Lesson Intro Video', 'dt_themes'); ?><span></span></h4>
					<div class="lesson-video"><?php if(wp_oembed_get( $lesson_video ) != '') echo wp_oembed_get( $lesson_video ); else echo wp_video_shortcode( array('src' => $lesson_video) ); ?></div>
				<?php } ?>
               
               <div class="dt-sc-clear"></div>
               <div class="dt-sc-hr-invisible-small"></div>
                
				<?php
                if(isset($dt_lesson_quiz) && $dt_lesson_quiz > 0) {

                    if(current_user_can($s2_level) || IAMD_USER_ROLE == 's2member_level2' || IAMD_USER_ROLE == 's2member_level3' || IAMD_USER_ROLE == 's2member_level4' || (is_user_logged_in() && $private_lesson == '')) {
                        
                        if(isset($dt_grade_post[0])) {
                            
                            $grade = get_post_meta ( $dt_grade_post[0]->ID, "graded",true);
                            if(!isset($grade) || $grade == '') {
                                
                                if(dt_can_user_retake_quiz($dt_lesson_course, $the_id, $dt_lesson_quiz, $user_id))
                                    echo '<a class="dt-sc-button small filled" href="'.get_permalink($dt_lesson_quiz).'">'.__('Retake Quiz','dt_themes').'</a>';
                                else
                                    echo '<div class="dt-sc-info-box">'.__('You may exceeded number of attempts to retake the quiz or you may not have permission to retake this quiz!.', 'dt_themes').'</div>';
                            
                            }
                            
                        } else {
                        
                            echo '<a class="dt-sc-button small filled" href="'.get_permalink($dt_lesson_quiz).'">'.__('Take Quiz','dt_themes').'</a>';	
                            
                        }
                    
                    } else if(!is_user_logged_in()) {
                        echo '<div class="dt-sc-warning-box">'.__('Please login to get access to the quiz', 'dt_themes').'</div>';
                    }
                
                } else {
                    
					if(is_user_logged_in()) {
						
						if(!isset($dt_grade_post[0])) {
							
							echo '<form method="post" class="frmCompleteLesson" action="'.get_permalink($the_id).'">';
							echo '<input type="hidden" name="dt_complete_lesson_noonce" id="dt_complete_lesson_noonce" value="'.wp_create_nonce('dt_complete_lesson_noonce').'" />';
							echo '<input type="submit" name="complete_lesson" value="'.__('Mark as Completed','dt_themes').'" />';
							echo '</form>';
						
						}
					
					}
                    
                }
                ?>
                
                <?php				
				$lesson_prev_next = dt_get_prev_next_lessons( $the_id );
				$previous_lesson_id = $lesson_prev_next['prev_lesson'];
				$next_lesson_id = $lesson_prev_next['next_lesson'];
                ?>

                <!-- **Post Nav** -->
                <div class="post-nav-container">
                	<?php if ( $previous_lesson_id > 0 ) { ?><div class="post-prev-link"><a href="<?php echo get_permalink($previous_lesson_id); ?>"><i class="fa fa-arrow-circle-left"> </i> <?php echo ' '.get_the_title($previous_lesson_id).'<span> ('.__('Prev Lesson','dt_themes').')</span>'; ?></a></div><?php } ?>
                    <?php if (  $next_lesson_id > 0 ) { ?><div class="post-next-link"><a href="<?php echo get_permalink($next_lesson_id); ?>"><?php echo '<span>('.__('Next Lesson','dt_themes').') </span>'.get_the_title($next_lesson_id).' '; ?> <i class="fa fa-arrow-circle-right"> </i></a></div><?php } ?>
                </div><!-- **Post Nav - End** -->
                
                <div class="dt-sc-hr-invisible-small"> </div>
                
                <?php
                if(isset($dt_lesson_course) && $dt_lesson_course != '') {
                    echo '<a href="'.get_permalink($dt_lesson_course).'" class="dt-sc-button small alignright">'.__('Back to ', 'dt_themes').get_the_title($dt_lesson_course).'</a>';	
                }
				?>

				<?php
				edit_post_link(__('Edit', 'dt_themes'), '<span class="edit-link">', '</span>' );
				if(!dttheme_option('general', 'disable-lessons-comment')) { comments_template('', true); } 
				?>
				
			</article>
            </div>
            <div class="column dt-sc-one-third dt-sc-lessons-menu-container">
            
                <div class="dt-sc-lessons-menu">
                
                	<h4> <?php _e('Course Curriculum', 'dt_themes'); ?> </h4>
                
					<?php
					$lesson_args = array('sort_order' => 'ASC', 'sort_column' => 'menu_order', 'hierarchical' => 1, 'post_type' => 'dt_lessons', 'posts_per_page' => -1, 'meta_key' => 'dt_lesson_course', 'meta_value' => $dt_lesson_course );
					$lessons_array = get_pages( $lesson_args );
					if(isset($lessons_array) && count($lessons_array) > 0) {
						echo '<ul>';
						foreach ($lessons_array as $lesson_item){
							
							if($the_id == $lesson_item->ID) $current_cls = ' current'; else $current_cls = ''; 
							
							$dt_lesson_quiz = get_post_meta ($lesson_item->ID, "lesson-quiz", true);
							if(isset($dt_lesson_quiz) && $dt_lesson_quiz > 0) {
								$dt_gradings = dt_get_user_gradings_array($dt_lesson_course, $lesson_item->ID, $dt_lesson_quiz, $user_id);
							} else {
								$dt_gradings = dt_get_user_gradings_array($dt_lesson_course, $lesson_item->ID, -1, $user_id);
							}
							
							$dt_grade_post = get_posts( $dt_gradings );
							$dt_grade_post_id = $dt_grade_post[0]->ID;
							$graded = get_post_meta ($dt_grade_post_id, "graded", true);
							
							if(isset($dt_grade_post[0]) && isset($graded) && $graded != '') {
								$dt_tooltip_dir = ' dt-sc-tooltip-right';
								$dt_tooltip_title = 'title="'.__('Completed', 'dt_themes').'"';
								$dt_lesson_status = 'dt-lesson-complete'; 
							} else {
								$dt_tooltip_dir = '';
								$dt_tooltip_title = '';
								$dt_lesson_status = '';
							}
															
							echo '<li class="'.$dt_lesson_status.$current_cls.'"><a href="'.get_permalink($lesson_item->ID).'" class="dt-sc-lesson-menu-name '.$dt_tooltip_dir.'" '.$dt_tooltip_title.'>'.$lesson_item->post_title.'</a>';
							
							$lesson_datas = get_post_meta($lesson_item->ID, '_lesson_settings', true);
							$duration = isset($lesson_datas['lesson-duration']) ? dttheme_wp_kses($lesson_datas['lesson-duration']) : 0; 
							if($duration > 0) {
								$hours = floor($duration/60); 
								$mins = $duration % 60; 
								if($hours == 0) {
									$duration = $mins . __(' mins ', 'dt_themes'); 				
								} elseif($hours == 1) {
									$duration = $hours .  __(' hour ', 'dt_themes') . $mins . __(' mins ', 'dt_themes'); 				
								} else {
									$duration = $hours . __(' hours ', 'dt_themes') . $mins . __(' mins ', 'dt_themes'); 				
								}
								echo '<div class="dt-sc-lesson-menu-duration"> <i class="fa fa-clock-o"> </i>'.$duration.'</a></div>';
							}
							
							echo '</li>';
							
						}
						echo '</ul>';
					}
					?>
                    
                    
                </div>
                             
				<?php
				$lesson_teacher = get_post_meta ( $the_id, "lesson-teacher",true);
				if($lesson_teacher != '') {
					$teacher_data = get_post($lesson_teacher);
					$ts = get_post_meta($teacher_data->ID, '_teacher_settings', true);
				?>
                <div class="dt-sc-lesson-staff">
                  <div class="lesson-staff-title">
                    <h5><?php echo __('Staff', 'dt_themes') ?></h5>
                  </div>
                  <div class="lesson-staff-details">
                    <div class="team-thumb"> 
						<?php if( has_post_thumbnail($teacher_data->ID) ):
								echo get_the_post_thumbnail($teacher_data->ID, 'full');
							  else:
								echo '<img src="http://placehold.it/500x500&text=Image" alt="" />';
						endif; ?>
                    </div>
                    <div class="team-meta"> 
                        <h5><?php echo $teacher_data->post_title; ?></h5>
                        <h6> <?php echo __('Role', 'dt_themes'); ?> : <?php if(isset($ts['role'])) echo $ts['role']; ?> </h6>
                    </div>
                    <div class="clear"> </div>
                    <ul class="teachers-details">
                        <li> <?php echo __('Website', 'dt_themes'); ?> : <?php if(isset($ts['url'])) echo '<a href="'.$ts['url'].'">'.$ts['url'].'</a>'; ?> </li>
                        <li> <?php echo __('Experience', 'dt_themes'); ?> : <?php if(isset($ts['exp'])) echo $ts['exp']; ?> </li>
                        <li> <?php echo __('Specialist in', 'dt_themes'); ?> : <?php if(isset($ts['special'])) echo $ts['special']; ?> </li>
                    </ul>
					<a class="dt-sc-button small" href="<?php echo get_permalink($teacher_data->ID); ?>"><?php echo __('Read More', 'dt_themes'); ?></a>
                </div>
                </div>
				<?php
				}
				?>   
            
            </div>
			<?php 
		endwhile; endif;?>
		
        <?php
		
	} elseif($starting_price == '' && $private_lesson != '') {
		echo '<div class="dt-sc-warning-box">';
		echo __('This lesson is marked as private you can\'t view its content.', 'dt_themes');
		echo '</div>';
	} else {
		echo '<div class="dt-sc-warning-box">';
		echo sprintf( __('This lesson is priceable, you have to purchase the course %s to view this lesson.', 'dt_themes'), '<a href="'.get_permalink($dt_lesson_course).'">'.get_the_title($dt_lesson_course).'</a>' );
		echo '</div>';
		echo '<a href="'.get_permalink($dt_lesson_course).'" target="_self"  class="dt-sc-button small">'.__('Purchase Now', 'dt_themes').'</a>';
	}
	?>
    
	</section><!-- ** Primary Section End ** --><?php

	if ( $show_sidebar ):
		if ( $show_right_sidebar ): ?>
			<!-- Secondary Right -->
			<section id="secondary-right" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'right' );?></section><?php
		endif;
	endif;?>
<?php get_footer(); ?>