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
	endif;?>

    <!-- ** Primary Section ** -->
    <section id="primary" class="<?php echo $page_layout;?>">
    
    	<div class="courses-sorting">
            <div class="courses-popular-type">
            	<label> <?php echo __('Filter by :', 'dt_themes'); ?> </label>
                <select name="courses-type" id="courses-type" data-postid="<?php echo $post->ID; ?>">
                    <option value="all"><?php echo __('All Course Type', 'dt_themes'); ?></option>
                    <option value="featured"><?php echo __('Featured Courses', 'dt_themes'); ?></option>
                    <?php if(function_exists('the_ratings')) { ?>
                        <option value="popular"><?php echo __('Popular Courses', 'dt_themes'); ?></option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="courses-price-type">
                <a class="course-price course-all-price active" data-postid="<?php echo $post->ID; ?>" data-price_type="all"> <span> </span><?php _e('All','dt_themes');?></a>
                <a class="course-price course-paid-price" data-postid="<?php echo $post->ID; ?>" data-price_type="paid"> <span> </span><?php _e('Paid','dt_themes');?></a>
                <a class="course-price course-free-price" data-postid="<?php echo $post->ID; ?>" data-price_type="free"> <span> </span><?php _e('Free','dt_themes');?></a>
            </div>    
        </div>    
        
        <div class="courses-view-type">
            <a class="course-layout course-grid-type active" data-postid="<?php echo $post->ID; ?>" data-view_type="grid"> <span> </span><?php _e('Grid','dt_themes');?></a>
            <a class="course-layout course-list-type" data-postid="<?php echo $post->ID; ?>" data-view_type="list"> <span> </span><?php _e('List','dt_themes');?></a>
        </div>
        <div class="dt-sc-clear"></div>
        
        <div id="dt-sc-ajax-load-image" style="display:none;"><img src="<?php echo IAMD_BASE_URL."images/loading.png"; ?>"  alt="" /></div>
        <div id="ajax_tpl_course_content"></div>
    
    </section><!-- ** Primary Section End ** -->
		
		
		<?php

	if ( $show_sidebar ):
		if ( $show_right_sidebar ): ?>
			<!-- Secondary Right -->
			<section id="secondary-right" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'right' );?></section><?php
		endif;
	endif;?>
<?php get_footer(); ?>