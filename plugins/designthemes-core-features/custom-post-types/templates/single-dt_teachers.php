<?php get_header();

	//GETTING META VALUES...
	$meta_set = get_post_meta($post->ID, '_teacher_settings', true);
	$meta_set = is_array( $meta_set ) ? $meta_set  : array();
	
	if($GLOBALS['force_enable'] == true)
		$page_layout = $GLOBALS['page_layout'];
	else
		$page_layout = !empty($meta_set['layout']) ? $meta_set['layout'] : 'content-full-width';

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
	$pholder = dttheme_option('general', 'disable-placeholder-images');
	
	if ( $show_sidebar ):
		if ( $show_left_sidebar ): ?>
			<!-- Secondary Left -->
			<section id="secondary-left" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'left' );?></section><?php
		endif;
	endif;?>

	<!-- ** Primary Section ** -->
	<section id="primary" class="<?php echo $page_layout;?>">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        	<h3 class="border-title "><?php the_title(__('About: ', 'dt_themes'), '');?><span></span></h3>
            <div class="column dt-sc-one-fourth space first">
            	<div class="team-thumb">
					<?php if( has_post_thumbnail() ):
                            the_post_thumbnail('full');
                          elseif($pholder != 'on'):?>
                            <img src="http://placehold.it/500x500&text=Image" alt="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>" title="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>" />
                    <?php endif;?>
                    <?php if(function_exists('the_ratings')) { echo do_shortcode('[ratings id="'.$post->ID.'"]'); } ?>
                    <?php
                    if(isset($ts['show-social-share']) && $ts['show-social-share'] != '' && isset($ts['teacher-social'])) {
                        $s = "";
                        $path = plugins_url() . "/designthemes-core-features/shortcodes/images/sociables/";
                        foreach ( $ts['teacher-social'] as $sociable => $social_link ) {
                            if($social_link != '') {
                                $img = $sociable;
                                $class = explode(".",$img);
                                $class = $class[0];
                                $s .= '<li class="'.$class.'"><a href="'.esc_url($social_link).'" target="_blank"> <img src="'.$path.'hover/'.$img.'" alt="'.$class.'"/>  <img src="'.$path.$img.'" alt="'.$class.'"/> </a></li>';
                            }
                        }
                        
                        $s = ! empty ( $s ) ? "<div class='dt-sc-social-icons'><ul>$s</ul></div>" : "";
                        echo $s;
                    }
                    ?>
                </div>
            </div>
            <div class="column dt-sc-three-fourth space">
				<ul class="teachers-details">
                	<li> <?php echo __('Role', 'dt_themes'); ?> : <?php echo dttheme_wp_kses($ts['role']); ?> </li>
                    <li> <?php echo __('Website', 'dt_themes'); ?> : <?php echo '<a href="'.esc_url($ts['url']).'">'.esc_url($ts['url']).'</a>'; ?> </li>
					<li> <?php echo __('Experience', 'dt_themes'); ?> : <?php echo dttheme_wp_kses($ts['exp']); ?> </li>
					<li> <?php echo __('Specialist in', 'dt_themes'); ?> : <?php echo dttheme_wp_kses($ts['special']); ?> </li>
				</ul>

                <?php
				$lesson_args = array('post_type' => 'dt_lessons', 'posts_per_page' => -1, 'meta_key' => 'lesson-teacher', 'meta_value' => $post->ID, 'orderby' => 'post_date', 'order' => 'DESC', );
				$lesson_array = get_posts( $lesson_args );
				
				if(is_array($lesson_array) && !empty($lesson_array)) {
					
					echo '<h5 class="border-title ">'.__('Topics Handling', 'dt_themes').'<span></span></h5>';
					
					echo '<table class="courses-table-list tablesorter">
							<thead>
							  <tr>
							  	<th class="lessons-table-title-header" scope="col">'.__('Course Name','dt_themes').'</th>
								<th class="lessons-table-title-header" scope="col">'.__('Lesson Name','dt_themes').'</th>
								<th class="lessons-table-type-header" scope="col">'.__('Complexity','dt_themes').'</th>
								<th class="lessons-table-length-header" scope="col">'.__('Length','dt_themes').'</th>
							  </tr>
							</thead>
							<tbody>';
					
					foreach($lesson_array as $lesson_item) {
						
						$terms = get_the_terms($lesson_item->ID,'lesson_complexity');
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
						
						$lesson_course = get_post_meta ( $lesson_item->ID, "dt_lesson_course",true);
						$lesson_course_data = get_post($lesson_course);
						
						$lesson_meta_data = get_post_meta($lesson_item->ID, '_lesson_settings');
						if(isset($lesson_meta_data[0]['lesson-duration']) && $lesson_meta_data[0]['lesson-duration'] != '') $duration = dttheme_wp_kses($lesson_meta_data[0]['lesson-duration']).' mins'; else $duration = '';
						
						echo '<tr>
								<td class="lessons-table-course"><a href="'.get_permalink($lesson_course_data->ID).'">'.$lesson_course_data->post_title.'</a></td>
								<td class="lessons-table-title"><a href="'.get_permalink($lesson_item->ID).'">'.$lesson_item->post_title.'</a></td>
								<td class="lessons-table-type">'.$lesson_terms.'</td>
								<td class="lessons-table-length">'.$duration.'</td>
							  </tr>';
					}
					
					echo '</tbody>';
					echo '</table>';

					
				}
				
				?>
                
				<?php
				if( have_posts() ): while( have_posts() ): the_post();
						the_content();
				endwhile; endif;
						
                wp_link_pages(array('before' => '<div class="page-link"><strong>'.__('Pages:', 'dt_themes').'</strong> ', 'after' => '</div>', 'next_or_number' => 'number'));
                ?>
            </div>
            <div class="dt-sc-hr-invisible-small  "></div>
            <?php
			edit_post_link(__('Edit', 'dt_themes'), '<span class="edit-link">', '</span>' );
			if(dttheme_option('general', 'disable-teacher-comment') != true && (isset($meta_set['comment']) != "")) { comments_template('', true); } 
			?>
        </article>

	</section><!-- ** Primary Section End ** --><?php

	if ( $show_sidebar ):
		if ( $show_right_sidebar ): ?>
			<!-- Secondary Right -->
			<section id="secondary-right" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'right' );?></section><?php
		endif;
	endif;?>
<?php get_footer(); ?>