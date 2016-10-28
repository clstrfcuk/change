<?php get_header();

	$page_layout 	= dttheme_option('specialty','teacher-archives-layout');
	
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

	$post_layout = dttheme_option('specialty','teacher-archives-post-layout'); 
	$post_layout = !empty($post_layout) ? $post_layout : "one-half-column";
	$post_class = "";

	switch($post_layout):	
		
		case 'one-column':
			$post_class =  " column dt-sc-one-column ";
			$firstcnt = 1;
		break;

		case 'one-half-column';
			$post_class = " column dt-sc-one-half ";
			$firstcnt = 2;
		break;

		case 'one-third-column':
			$post_class =  " column dt-sc-one-third ";
			$firstcnt = 3;
		break;

		case 'one-fourth-column':
			$post_class =  "column dt-sc-one-fourth";
			$firstcnt = 4;
		
	endswitch;

	$pholder = dttheme_option('general', 'disable-placeholder-images');
	
	if ( $show_sidebar ):
		if ( $show_left_sidebar ): ?>
			<!-- Secondary Left -->
			<section id="secondary-left" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'left' );?></section><?php
		endif;
	endif;?>

	<!-- ** Primary Section ** -->
	<section id="primary" class="<?php echo $page_layout;?>">
    	
        <?php
		
		if( have_posts() ): $i = 1;
		 while( have_posts() ): the_post(); 
			
			global $post;
			
			$firstcls = $temp_class = '';
			$no = $wp_query->current_post+1;
	
			if(($no%$firstcnt) == 1){ $firstcls = ' first'; }
			$temp_class = 'class="'.$post_class.' '.$firstcls.'"';

			
			$teacher_settings = get_post_meta ( $post->ID, '_teacher_settings', TRUE );
			
			$s = "";
			$path = plugins_url() . '/designthemes-core-features/shortcodes/images/sociables/';
			if(isset($teacher_settings['teacher-social'])) {
				foreach ( $teacher_settings['teacher-social'] as $sociable => $social_link ) {
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
			$link = admin_url('admin-ajax.php?ajax=true&amp;action=dttheme_team_member&amp;post_id='.$post->ID.'&amp;nonce='.$nonce);
			
			?>			
			<div <?php echo $temp_class; ?>>
			   <div class='dt-sc-team'>
					<div class='image'>
						<a href="<?php echo $link; ?>" data-gal="prettyPhoto[pp_gal]">
                        	<?php
							if(get_the_post_thumbnail($post->ID, 'full') != ''):
								echo get_the_post_thumbnail($post->ID, 'full');
							elseif($pholder != 'on'): ?>
								<img src="http://placehold.it/400x420" alt="member-image" />
							<?php endif; ?>
			 			</a>				
			 		</div>
					<div class="team-details">
						<h5><a href="<?php echo $link; ?>" data-gal="prettyPhoto[pp_gal]"><?php echo get_the_title(); ?></a></h5>
                        <?php if($teacher_settings['role'] != '') { ?>
                                <h6><?php echo $teacher_settings['role']; ?></h6>
                        <?php }
						if(isset($teacher_settings['show-social-share']) && $teacher_settings['show-social-share'] != '') echo $s;
						?>
					</div>
			   </div>
			</div>
			
            <?php	
		 endwhile;
		else: ?>
            <h2><?php _e('Nothing Found.', 'dt_themes'); ?></h2><?php
		endif;
		?>
        
    </section><!-- ** Primary Section End ** -->
		
		
		<?php

	if ( $show_sidebar ):
		if ( $show_right_sidebar ): ?>
			<!-- Secondary Right -->
			<section id="secondary-right" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'right' );?></section><?php
		endif;
	endif;?>
<?php get_footer(); ?>