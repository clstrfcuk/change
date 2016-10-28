<?php get_header();

	$page_layout 	= dttheme_option('dt_course','archives-layout');
	
	if($GLOBALS['force_enable'] == true)
		$page_layout = dttheme_option('general', 'global-page-layout');
	else
		$page_layout = !empty($page_layout) ? $page_layout : "content-full-width";
  	
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

	if ( $show_sidebar ):
		if ( $show_left_sidebar ): ?>
			<!-- Secondary Left -->
			<section id="secondary-left" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'left' );?></section><?php
		endif;
	endif;
	
	?>

	<!-- ** Primary Section ** -->
	<section id="primary" class="<?php echo $page_layout;?>">
    
		<?php 
		if( have_posts() ): 
		$out = '<ol class="dt-sc-lessons-list">';
		while( have_posts() ): the_post(); ?>
        	<?php
			
			$dt_lesson_course = get_post_meta ($post->ID, "dt_lesson_course",true);
			$s2_level = "access_s2member_ccap_cid_{$dt_lesson_course}";
			
			$lesson_meta_data = get_post_meta($post->ID, '_lesson_settings');
			$lesson_teacher = $lesson_duration = '';
			$private_lesson = !empty($lesson_meta_data[0]['private-lesson']) ? $lesson_meta_data[0]['private-lesson'] : '';
			
			$lesson_teacher = get_post_meta ( $id, "lesson-teacher",true);
			
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
				if ( current_user_can($s2_level) ){
					$private_lesson = '';
				} else {
					$private_lesson = 'class="dt-hidden-lesson"';
				}
			} else {
				$private_lesson = '';
			}
			
			$terms = get_the_terms($post->ID,'lesson_complexity');
			
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
			
			$out .= '<li '.$private_lesson.'>';
						if($private_lesson != '') {
							$out .= '<div class="hidden-lesson-overlay"> </div>';
						}
				$out .= '<article class="dt_lessons">
							<div class="lesson-title">';
								if($private_lesson != '') {
									$out .= '<h2>'.$post->post_title.'</h2>';
								} else {
									$out .= '<h2> <a href="'.get_permalink($post->ID).'" title="'.$post->post_title.'">'.$post->post_title.'</a> </h2>';
								}
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
								'.get_the_excerpt().'
							</section>
						</article>';
			$out .= '</li>';
			
			?>
        
		<?php endwhile; 
		$out .=  '</ol>';
		endif;
		echo $out;
		?>

        <!-- **Pagination** -->
       <div class="pagination">
            <div class="prev-post"><?php previous_posts_link('<span class="fa fa-angle-double-left"></span> Prev');?></div>
            <?php echo dttheme_pagination();?>
            <div class="next-post"><?php next_posts_link('Next <span class="fa fa-angle-double-right"></span>');?></div>
       </div><!-- **Pagination - End** -->
		           

		</section><!-- ** Primary Section End ** --><?php

	if ( $show_sidebar ):
		if ( $show_right_sidebar ): ?>
			<!-- Secondary Right -->
			<section id="secondary-right" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'right' );?></section><?php
		endif;
	endif;?>
<?php get_footer(); ?>