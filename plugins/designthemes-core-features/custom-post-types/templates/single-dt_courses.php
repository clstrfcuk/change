<?php get_header();

	//GETTING META VALUES...
	$course_settings = get_post_meta($post->ID, '_course_settings', true);
	$course_settings = is_array( $course_settings ) ? $course_settings  : array();

	if($GLOBALS['force_enable'] == true)
		$page_layout = $GLOBALS['page_layout'];
	else
		$page_layout = !empty($course_settings['layout']) ? $course_settings['layout'] : 'content-full-width';

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

	$ts = get_post_meta($post->ID, '_course_settings', true);
	$s2_level = "access_s2member_ccap_cid_{$post->ID}";
	
	$pholder = dttheme_option('general', 'disable-placeholder-images');
	
	if ( $show_sidebar ):
		if ( $show_left_sidebar ): ?>
			<!-- Secondary Left -->
			<section id="secondary-left" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'left' );?></section><?php
		endif;
	endif;?>

	<!-- ** Primary Section ** -->
	<section id="primary" class="<?php echo $page_layout;?>">
		<?php if( have_posts() ): while( have_posts() ): the_post();
		$the_id = get_the_ID(); 
		?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('dt-sc-course-single'); ?>>
            
            <div class="dt-sc-course-details">
            	<div class="dt-sc-course-image">
					<?php
                    if(has_post_thumbnail()):
                        $image_url = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'full');
                        ?>
                        <img src="<?php echo $image_url[0]; ?>" alt="<?php echo get_the_title(); ?>" />
                    <?php elseif($pholder != 'on'): ?>
                        <img src="http://placehold.it/1170x822&text=Image" alt="<?php echo get_the_title(); ?>" />
                    <?php endif; ?>
                </div>
                <div class="dt-sc-course-details-inner">
                    
                    <?php
                    $featured_course = get_post_meta($the_id, 'featured-course', true);
                    if(isset($featured_course) && $featured_course == 'true') {
                    ?>
                        <div class="featured-post"> <span class="fa fa-trophy"> </span> <span class="text"> <?php _e('Featured','dt_themes');?> </span></div>
                    <?php } ?>
                    
                    <h3><?php the_title(); ?></h3>
                    
                    <?php
                    if(function_exists('the_ratings') && !dttheme_option('general', 'disable-ratings-courses')) { 
                        echo do_shortcode('[ratings id="'.$the_id.'"]');
                    }
                    ?>     
                    <div class="entry-metadata">
        				<div class="dt-sc-meta-container">
							<?php 
                                $lesson_args = array('post_type' => 'dt_lessons', 'posts_per_page' => -1, 'post_status' => 'any', 'meta_key' => 'dt_lesson_course', 'meta_value' => $the_id );								
                                $lessons_array = get_posts( $lesson_args );
                                
                                $count = $duration = 0;
								$count = count($lessons_array);
                                foreach($lessons_array as $lesson) {
                                    $lesson_data = get_post_meta($lesson->ID, '_lesson_settings');
                                    if(isset($lesson_data[0]['lesson-duration'])) {
                                        $duration = $duration + $lesson_data[0]['lesson-duration'];
                                    }
                                }
                                
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
                            <p><i class="fa fa-location-arrow"> </i> <?php the_terms($post->ID,'course_category'); ?></p>
                            <p><i class="fa fa-book"> </i> 
                            <?php echo $count.__(' Lessons', 'dt_themes'); ?>
                            </p>
                            <p><i class="fa fa-clock-o"> </i> <?php echo $duration; ?></p>
                        </div>
                    
                    	<div class="dt-sc-status-container">
                        <?php 
						$starting_price = dttheme_wp_kses(get_post_meta(get_the_ID(), 'starting-price', true));
						
						if($starting_price != ''): 
							if ( IAMD_USER_ROLE == 's2member_level2' || IAMD_USER_ROLE == 's2member_level3' || IAMD_USER_ROLE == 's2member_level4' || current_user_can($s2_level) ){
								
								echo '<span class="dt-sc-purchased"> '.__('Purchased Already','dt_themes').'</span>';
								
							} else {
								
								$page_link = dttheme_get_page_permalink_by_its_template('tpl-membership.php');
								if($page_link != '') {
									
									echo '<a href="'.$page_link.'?courseid='.$the_id.'" target="_self"  class="dt-sc-button small filled"><i class="fa fa-shopping-cart"></i> '.__('Purchase Now','dt_themes').'</a>';
									
								} else {

									$description = (dttheme_option('dt_course','s2member-1-description') != '') ? dttheme_option('dt_course','s2member-1-description') : __('You are about to purchase the Course : ', 'dt_themes').get_the_title($post->ID);
									$period = (dttheme_option('dt_course','s2member-1-period') != '') ? dttheme_option('dt_course','s2member-1-period') : 1;
									$term = (dttheme_option('dt_course','s2member-1-term') != '') ? dttheme_option('dt_course','s2member-1-term') : 'L';									
									
									$price = $starting_price; 
									if(dttheme_option('dt_course','currency-position') == 'after-price') $price = $price.dttheme_wp_kses(dttheme_option('dt_course','currency')); 
									else $price = dttheme_wp_kses(dttheme_option('dt_course','currency')).$price; 
										
									if(dttheme_option('dt_course','currency-s2member') != '') $currency = dttheme_option('dt_course','currency-s2member');
									else $currency = 'USD';
										
									if(dttheme_is_plugin_active('s2member/s2member.php')) {	
										$paypal_sc = do_shortcode("[s2Member-PayPal-Button level='1' ccaps='cid_{$post->ID}' desc='{$description}' ps='paypal' lc='' cc='{$currency}' dg='0' ns='1' custom='".$_SERVER["HTTP_HOST"]."' ta='0' tp='0' tt='D' ra='{$starting_price}' rp='{$period}' rt='{$term}' rr='BN' rrt='' rra='1' image='' output='url'/]");
									} else {
										$paypal_sc = '#';
									}
									
									echo '<a href="'.$paypal_sc.'" target="_self"  class="dt-sc-button small filled"><i class="fa fa-shopping-cart"></i> '.$price.' - '.__('Purchase Now','dt_themes').'</a>';
								
								}
								
							}
						else:
							$login_page_link = dttheme_get_page_permalink_by_its_template('tpl-login.php');
							if($login_page_link != '') echo '<span class="dt-sc-purchased"><a href="'.$login_page_link.'" target="_self">'.__('Free','dt_themes').'</a></span>';
							else echo '<span class="dt-sc-purchased">'.__('Free','dt_themes').'</span>';
						endif;
						?>
                        
                    <?php
					if(is_user_logged_in() && current_user_can($s2_level)) {
						$course_id = $the_id;
						$course_status = dt_get_users_course_status($course_id, '');
						if($course_status)
							echo '<div class="dt-sc-course-completed"> <span class="fa fa-check-circle"> </span> '.__('Completed', 'dt_themes').'</div>';
					}
					?>
                    </div>
                                
                    </div>
                </div>
            </div>
                            
            <div class="dt-sc-clear"></div>
            <div class="dt-sc-hr-invisible-small"></div>
           
            <section class="entry">
            	<?php the_content(); ?>  
                
                <?php if ( IAMD_USER_ROLE == 's2member_level2' || IAMD_USER_ROLE == 's2member_level3' || IAMD_USER_ROLE == 's2member_level4' || current_user_can($s2_level) ){ ?>
                
                    <div class="dt-sc-hr-invisible-small"> </div> 
                    <?php
                    $media_attachments = get_post_meta ( $the_id, "media-attachments", true);
                    if(isset($media_attachments) && !empty($media_attachments)) {
                        echo '<h5>'.__('Media Attachments', 'dt_themes').'</h5>';
                        echo '<ul class="dt-sc-media-attachments">';
                        foreach($media_attachments as $attachment_url) {
                            if($attachment_url != '') {
                                $attachment_id = dt_get_attachment_id_from_url($attachment_url);
                                $attachment_title = get_the_title($attachment_id);
                                if($attachment_title == '') $attachment_title = basename($attachment_url);
                                echo '<li><a href="'.$attachment_url.'" target="_blank">'.$attachment_title.'</a></li>';
                            }
                        }
                        echo '</ul>';
                    }
                    ?>
                
                <?php } ?>
				
                <?php if(isset($course_settings['referrrence_url'])): ?>
                    <?php echo '<strong>'.__('Referrrence URL: ','dt_themes').'</strong><a href="'.esc_url($course_settings['referrrence_url']).'">'.esc_url($course_settings['referrrence_url']).'</a>';?>
                <?php endif;?>
                
				<?php
                if(array_key_exists("show-social-share",$course_settings)):
                    echo '<div class="courses-share">';
                    dttheme_social_bookmarks('sb-courses');
                    echo '</div>';
                endif;
                ?>
            </section>
            <div class="dt-sc-clear"></div>
            <div class="dt-sc-hr-invisible"></div>
            
            <?php 
			$course_video = get_post_meta($the_id, 'course-video', true);
			if(isset($course_video) && $course_video != '') { ?>
                <h4 class="border-title"><?php _e('Course Intro Video', 'dt_themes'); ?><span></span></h4>
                <div class="course-video"><?php if(wp_oembed_get( $course_video ) != '') echo wp_oembed_get( $course_video ); else echo wp_video_shortcode( array('src' => $course_video) ); ?></div>
                <div class="dt-sc-clear"></div>
                <div class="dt-sc-hr-invisible-medium"></div>
            <?php } ?>

            <?php
			$lessons_array = $staffs_id = array();
			$lesson_args = array('sort_order' => 'ASC', 'sort_column' => 'menu_order', 'hierarchical' => 1, 'post_type' => 'dt_lessons', 'posts_per_page' => -1, 'meta_key' => 'dt_lesson_course', 'meta_value' => $the_id );
			$lessons_array = get_pages( $lesson_args );
			
			if(isset($lessons_array) && !empty($lessons_array)) {		
				
				echo '<div class="dt-lesson-wrapper">
					<div class="dt-lesson-inner-wrapper">
					<h4 class="dt-lesson-title">'.__('Lessons', 'dt_themes').'<span></span></h4>';
			
				$lessons_hierarchy_array = array();
				foreach ( (array) $lessons_array as $p ) {
					$lesson_teacher = get_post_meta ( $p->ID, "lesson-teacher",true);
					if($lesson_teacher != '')
						$staffs_id[] = $lesson_teacher;
					
					$parent_id = intval( $p->post_parent );
					$lessons_hierarchy_array[ $parent_id ][] = $p;
				}
				
				if(isset($lessons_hierarchy_array[0])) {
					$out = '';
					$i = 1;
					$out .= '<ol class="dt-sc-lessons-list">';
					foreach($lessons_hierarchy_array[0] as $lesson) {
						$lesson_meta_data = get_post_meta($lesson->ID, '_lesson_settings');
						$lesson_teacher = $lesson_duration = '';
						$private_lesson = !empty($lesson_meta_data[0]['private-lesson']) ? $lesson_meta_data[0]['private-lesson'] : '';
						
						$lesson_teacher = get_post_meta ( $lesson->ID, "lesson-teacher",true);
						if($lesson_teacher != '') {
							$teacher_data = get_post($lesson_teacher);
							if($private_lesson != '') {
								$lesson_teacher = '<p> <i class="fa fa-user"> </i>'.$teacher_data->post_title.'</p>';
							} else {
								$lesson_teacher = '<p> <i class="fa fa-user"> </i><a href="'.get_permalink($teacher_data->ID).'">'.$teacher_data->post_title.'</a></p>';
							}
						}
						if(isset($lesson_meta_data[0]['lesson-duration']) && $lesson_meta_data[0]['lesson-duration'] != '') {
							$lesson_duration .= '<p> <i class="fa fa-clock-o"> </i>'.dttheme_wp_kses($lesson_meta_data[0]['lesson-duration']). __(' mins ', 'dt_themes').'</p>';
						}
						if(isset($lesson_meta_data[0]['private-lesson']) && $lesson_meta_data[0]['private-lesson'] != '') {
							if ( IAMD_USER_ROLE == 's2member_level2' || IAMD_USER_ROLE == 's2member_level3' || IAMD_USER_ROLE == 's2member_level4' || current_user_can($s2_level) ){
								$private_lesson = '';
							} else {
								$private_lesson = 'dt-hidden-lesson';
							}
						} else {
							$private_lesson = '';
						}
						
						$terms = get_the_terms($lesson->ID,'lesson_complexity');
						$lesson_terms = '';
						if(isset($terms) && !empty($terms)) {
							$lesson_terms = array();
							foreach ( $terms as $term ) {
								if($private_lesson != '') {
									$lesson_terms[] = $term->name;
								} else {
									$lesson_terms[] = '<a href="'.get_term_link( $term->slug, 'lesson_complexity' ).'">'.$term->name.'</a>';
								}
							}
							$lesson_terms = join( ", ", $lesson_terms );
						}
						
						$grade_chk = $grade_cls = '';
						if(is_user_logged_in() && $private_lesson != 'dt-hidden-lesson') {
							$user_id = get_current_user_id();
							$course_id = $the_id;
							$lesson_id = $lesson->ID;
							$quiz_id = get_post_meta ($lesson_id, "lesson-quiz", true);
							if(!isset($quiz_id) || $quiz_id == '') $quiz_id = -1;

							$dt_gradings = dt_get_user_gradings_array($course_id, $lesson_id, $quiz_id, $user_id);
							$dt_grade_post = get_posts( $dt_gradings );
							
							$dt_grade_post_id = isset($dt_grade_post[0]->ID) ? $dt_grade_post[0]->ID : 0;
							
							$graded = get_post_meta ( $dt_grade_post_id, "graded",true);
							if(isset($graded) && $graded != '') {
								$grade_chk = '<div class="dt-sc-lesson-completed"> <span class="fa fa-check-circle"> </span> '.__('Completed', 'dt_themes').'</div>';
								$grade_cls = ' dt-lesson-complete';
							}
						}
						
						$out .= '<li class="'.$private_lesson.$grade_cls.'">';
									if($private_lesson != '') {
										$out .= '<div class="hidden-lesson-overlay"> </div>';
									}
							$out .= '<article class="dt_lessons">
										<div class="lesson-title">';
											if($private_lesson != '') {
												$out .= '<h2>'.$lesson->post_title.'</h2>';
											} else {
												$out .= '<h2> <a href="'.get_permalink($lesson->ID).'" title="'.$lesson->post_title.'">'.$lesson->post_title.'</a> </h2>';
											}
											$out .= $grade_chk;
									$out .= '<div class="lesson-metadata">';
											if($lesson_terms != '') { 
												 $out .= '<p> <i class="fa fa-tags"> </i> '.$lesson_terms.' </p>';
											}
											$out .= $lesson_duration.$lesson_teacher.'
										   </div>
										</div>
										
										<div class="dt-sc-clear"></div>
										<div class="dt-sc-hr-invisible-small"></div>
										
										<section class="lesson-details">
											'.$lesson->post_excerpt.'
										</section>
									</article>';
							$out .= dttheme_get_lesson_details( $lessons_hierarchy_array,  $lesson->ID, $s2_level );
						$out .= '</li>';
						
						$i++;
					}
					$out .= '</ol>';
					echo $out;
				}
				echo '</div></div>';
			
			}
	
			$assignments_args = array('sort_order' => 'ASC', 'sort_column' => 'menu_order', 'hierarchical' => 1, 'post_type' => 'dt_assignments', 'posts_per_page' => -1, 'meta_key' => 'dt-assignment-course', 'meta_value' => $the_id );
			$assignments_array = get_pages( $assignments_args );
			
			if(isset($assignments_array) && !empty($assignments_array)) {		
			
				echo '<div class="clear"></div>
					  <div class="dt-sc-hr-invisible"></div>';
						
				echo '<div class="dt-lesson-wrapper">
						<div class="dt-lesson-inner-wrapper">
							<h4 class="dt-lesson-title">'.__('Assignments', 'dt_themes').'<span></span></h4>';
							
							echo '<ol class="dt-sc-lessons-list">';
							foreach($assignments_array as $assignment) {
								
								$grade_chk = $grade_cls = '';
								
								$assignment_id = $assignment->ID;
								$subtitle = get_post_meta ($assignment->ID, "assignment-subtitle", true);
								
								$user_id = get_current_user_id();
								$dtgradings = array( 'post_type' => 'dt_gradings', 'meta_query'=>array() );
								$dtgradings['meta_query'][] = array( 'key' => 'dt-user-id', 'value' => $user_id, 'compare' => '=', 'type' => 'numeric' );
								$dtgradings['meta_query'][] = array( 'key' => 'dt-course-id', 'value' => $the_id, 'compare' => '=', 'type' => 'numeric' );
								$dtgradings['meta_query'][] = array( 'key' => 'dt-assignment-id', 'value' => $assignment_id, 'compare' => '=', 'type' => 'numeric' );
								$dtgradings['meta_query'][] = array( 'key' => 'grade-type', 'value' => 'assignment', 'compare' => '=' );
								$dtgradings_post = get_posts( $dtgradings );
								
								if(isset($dtgradings_post) && !empty($dtgradings_post)) {
									
									$dtgradings_id = $dtgradings_post[0]->ID;
									$marks_obtained_percent = get_post_meta ( $dtgradings_id, "marks-obtained-percent", true); 
									$graded = get_post_meta ($dtgradings_id, "graded", true);
									
									if(isset($graded) && $graded != '') { 
										$grade_chk = '<div class="dt-sc-assignment-completed"> <span class="fa fa-check-circle"> </span> '.__('Completed', 'dt_themes').'</div>';
										$grade_cls = ' dt-assignment-complete';
									}
									
								}
								
								echo '<li class="'.$grade_cls.'">
										<article class="dt_lessons">
											<div class="lesson-title">
												<h2><a href="'.get_permalink($assignment_id).'">'.$assignment->post_title.'</a></h2>
												<h5>'.$subtitle.'</h5>
												'.$grade_chk.'
											</div>
										</article>
									</li>';
							}
							
							echo '</ol>';
							
				echo '	</div>
					</div>';		
			
			}
			
			?>
                        
        </article>
		<?php endwhile; endif; ?>
        
        <?php if(!array_key_exists("disable-staffs",$course_settings) && !empty($staffs_id[0])): ?>
        <div class="clear"> </div>
        <div class="dt-sc-hr-invisible"> </div>
        <h3><?php _e('Staffs','dt_themes');?></h3> 
        
        <?php        
		
		$staffs_id = array_unique (array_filter($staffs_id));
		$out = ''; $cnt = 1;
		
		foreach($staffs_id as $staff_id) {
			
			if(($cnt%4) == 1) $firstcls = ' first'; else $firstcls = '';
			$staff_settings = get_post_meta ( $staff_id, '_teacher_settings', TRUE );
			
			$s = "";
			$sociables_icons_path  = plugin_dir_url(__FILE__);
			$x =  explode ( "designthemes-core-features" , $sociables_icons_path );
			$path = $x[0].'designthemes-core-features/shortcodes/images/sociables/';

			if(isset($staff_settings['teacher-social'])) {
				foreach ( $staff_settings['teacher-social'] as $sociable => $social_link ) {
					if($social_link != '') {
						$img = $sociable;
						$class = explode(".",$img);
						$class = $class[0];
						$s .= "<li class='{$class}'><a href='{$social_link}' target='_blank'> <img src='{$path}hover/{$img}' alt='{$class}'/>  <img src='{$path}{$img}' alt='{$class}'/> </a></li>";
					}
				}
			}
			$s = ! empty ( $s ) ? "<div class='dt-sc-social-icons'><ul>$s</ul></div>" : "";
			
			//FOR AJAX...
			$nonce = wp_create_nonce("dt_team_member_nonce");
			$link = admin_url('admin-ajax.php?ajax=true&amp;action=dttheme_team_member&amp;post_id='.$staff_id.'&amp;nonce='.$nonce);
						
			$out .= '<li class="column dt-sc-one-fourth">';	
			$out .= "   <div class='dt-sc-team'>";
			$out .= "		<div class='image'>";
								if(get_the_post_thumbnail($staff_id, 'full') != ''):
									$out .= get_the_post_thumbnail($staff_id, 'full');
								else:
									$out .= '<img src="http://placehold.it/400x420" alt="member-image" />';
								endif;
			$out .= " 		</div>";
			$out .= '		<div class="team-details">';
			$out .= '			<h5><a href="'.$link.'" data-gal="prettyPhoto[pp_gal]">'.get_the_title($staff_id).'</a></h5>';
								if(isset($staff_settings['role']) && $staff_settings['role'] != '')
									$out .= "<h6>".$staff_settings['role']."</h6>";
								if(isset($staff_settings['show-social-share']) && $staff_settings['show-social-share'] != '') $out .= $s;
			$out .= '		</div>';
			$out .= '   </div>';
			$out .= '</li>';	
			
			$cnt++;
		
		}
		echo '<div class="dt-sc-staff-carousel-wrapper"><ul class="dt-sc-staff-carousel">'.$out.'</ul><div class="carousel-arrows"><a class="staff-prev" href=""></a><a class="staff-next" href=""></a></div></div>';
		?>
        
        <?php 
		endif;
		if(array_key_exists("show-related-course",$course_settings)):
		?>
        
            <div class="clear"> </div>
            <div class="dt-sc-hr-invisible"> </div>
            
            <div class="dt-sc-related-courses">
            <h3><?php _e('Related Courses','dt_themes');?></h3> 
            <?php
            
            $category_ids = array();
            $allcats  = wp_get_object_terms( $post->ID, 'course_category');
            
            foreach($allcats as $category) $category_ids[] = $category->term_id;
            
            $args = array('orderby' => 'rand', 'showposts' => '3', 'post__not_in' => array($post->ID), 'tax_query' => array( array( 'taxonomy'=>'course_category', 'field'=>'id', 'operator'=>'IN', 'terms'=>$category_ids )));
                    
            query_posts($args);
            if( have_posts() ): while( have_posts() ): the_post();
                $no = $wp_query->current_post;
                if($no == 0) $first_cls = 'first'; else $first_cls = '';
                ?>
                
                <div class="column dt-sc-one-third <?php echo $first_cls; ?>">
                    <article id="post-<?php echo get_the_ID(); ?>" class="<?php echo implode(" ", get_post_class("dt-sc-custom-course-type", get_the_ID())); ?>">
                    
                        <div class="dt-sc-course-thumb">
                            <a href="<?php echo the_permalink(); ?>" >
                                <?php
                                if(has_post_thumbnail()):
                                    $image_url = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'full');
                                ?>
                                    <img src="<?php echo $image_url[0]; ?>" alt="<?php echo get_the_title(); ?>" />
                                <?php else: ?>
                                    <img src="http://placehold.it/1170x822&text=Image" alt="<?php echo get_the_title(); ?>" />
                                <?php endif; ?>
                             </a>
                            <div class="dt-sc-course-overlay">
                                <a title="<?php echo get_the_title(); ?>" href="<?php echo the_permalink(); ?>" class="dt-sc-button small white"> <?php echo __('View Course', 'dt_themes'); ?> </a>
                            </div>
                        </div>
                        
                        <?php
                        $lesson_args = array('post_type' => 'dt_lessons', 'posts_per_page' => -1, 'meta_key' => 'dt_lesson_course', 'meta_value' => get_the_ID() );
                        $lessons_array = get_pages( $lesson_args );
                        
                        $count = $duration = 0;
                        if(count($lessons_array) > 0) {
                            foreach($lessons_array as $lesson) {
                                $lesson_data = get_post_meta($lesson->ID, '_lesson_settings');
                                if(isset($lesson_data[0]['lesson-duration'])) $duration = $duration + dttheme_wp_kses($lesson_data[0]['lesson-duration']);
                                $count++;
                            }
                        }
                        
                        if($duration > 0) {
                            $hours = floor($duration/60); 
                            $mins = $duration % 60; 
                            if(strlen($mins) == 1) $mins = '0'.$mins;
                            if(strlen($hours) == 1) $hours = '0'.$hours;
                            if($hours == 0) {
                                $duration = '00 : '.$mins;
                            } else {
                                $duration = $hours . ' : ' . $mins; 				
                            }
                        }
                        ?>
                        
                        <div class="dt-sc-course-details">	
                        
                  
							<?php
							$s2_level_rc = "access_s2member_ccap_cid_".get_the_ID();
                            if ( current_user_can($s2_level_rc) ){
								echo '<div class="dt-sc-purchased-details">';
									echo '<span class="dt-sc-purchased"> '.__('Purchased Already','dt_themes').'</span>';
									$course_status = dt_get_users_course_status($post->ID, '');
									if($course_status)
										echo '<div class="dt-sc-course-completed"> <span class="fa fa-check-circle"> </span> '.__('Completed', 'dt_themes').'</div>';
								echo '</div>';
							} else {
								?>
								<?php $starting_price = dttheme_wp_kses(get_post_meta(get_the_ID(), 'starting-price', true));
								if($starting_price != ''): ?>
                                    <span class="dt-sc-course-price"> <span class="amount"> 
                                    <?php 
                                    if(dttheme_option('dt_course','currency-position') == 'after-price') 
										echo $starting_price.dttheme_wp_kses(dttheme_option('dt_course','currency')); 
                                    else
										echo dttheme_wp_kses(dttheme_option('dt_course','currency')).$starting_price; 
                                    ?>
                                    </span> </span>
								<?php else: ?>
                                    <span class="dt-sc-course-price"> <span class="amount"> <?php echo __('Free', 'dt_themes'); ?> </span> </span>
								<?php endif; ?>
                            <?php } ?>
                        
                            <h5><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h5>
                            
                            <div class="dt-sc-course-meta">
                                <p> <?php the_terms(get_the_ID(), 'course_category', ' ', ', ', ' '); ?> </p>
                                <p> <?php echo $count.'&nbsp;'.__('Lessons', 'dt_themes'); ?> </p>
                            </div>
                            
                            <div class="dt-sc-course-data">
                                <div class="dt-sc-course-duration">
                                    <i class="fa fa-clock-o"> </i>
                                    <span> <?php echo $duration; ?> </span>
                                </div>
                                <?php
                                if(function_exists('the_ratings') && !dttheme_option('general', 'disable-ratings-courses')) { 
                                    echo do_shortcode('[ratings id="'.get_the_ID().'"]');
                                }
                                ?>
                            </div>
                        
                        </div>
                    
                    </article>
                </div>
            
            <?php
            endwhile; endif;
            ?>      
            </div> 
        
        <?php endif; ?>
        
		<?php
        edit_post_link(__('Edit', 'dt_themes'), '<span class="edit-link">', '</span>' );
        if(!dttheme_option('general', 'disable-courses-comment')) { comments_template('', true); } 
        ?>
                
	</section><!-- ** Primary Section End ** --><?php

	if ( $show_sidebar ):
		if ( $show_right_sidebar ): ?>
			<!-- Secondary Right -->
			<section id="secondary-right" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'right' );?></section><?php
		endif;
	endif;?>
<?php get_footer(); ?>