<?php get_header();

	$page_layout 	= dttheme_option('specialty','post-archives-layout');
	
	if($GLOBALS['force_enable'] == true)
		$page_layout = dttheme_option('general', 'global-page-layout');
	else
		$page_layout = !empty($page_layout) ? $page_layout : "content-full-width";
  
	$show_sidebar = $show_left_sidebar = $show_right_sidebar =  false;
	$sidebar_class = $thumbnail_sidebar = $post_thumbnail = "";

	switch ( $page_layout ) {
		case 'with-left-sidebar':
			$page_layout = "page-with-sidebar with-left-sidebar";
			$show_sidebar = $show_left_sidebar = true;
			$sidebar_class = "secondary-has-left-sidebar";
			$thumbnail_sidebar = "-single-sidebar";
		break;

		case 'with-right-sidebar':
			$page_layout = "page-with-sidebar with-right-sidebar";
			$show_sidebar = $show_right_sidebar	= true;
			$sidebar_class = "secondary-has-right-sidebar";
			$thumbnail_sidebar = "-single-sidebar";
		break;

		case 'both-sidebar':
			$page_layout = "page-with-sidebar page-with-both-sidebar";
			$show_sidebar = $show_right_sidebar	= $show_left_sidebar = true;
			$sidebar_class = "secondary-has-both-sidebar";
			$thumbnail_sidebar = "-both-sidebar";
		break;

		case 'content-full-width':
		default:
			$page_layout = "content-full-width";
			$thumbnail_sidebar = "";
		break;
	}

	if ( $show_sidebar ):
		if ( $show_left_sidebar ): ?>
			<!-- Secondary Left -->
			<section id="secondary-left" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'left' );?></section><?php
		endif;
	endif;?>

	<!-- ** Primary Section ** -->
	<section id="primary" class="<?php echo $page_layout;?>"><?php
	
		$post_layout = dttheme_option('specialty','post-archives-post-layout'); 
		$post_layout = !empty($post_layout) ? $post_layout : "one-column";
		$post_class = $container_class = "";

		switch($post_layout):
			case 'one-column':
				$post_class = $show_sidebar ? " column dt-sc-one-column with-sidebar blog-fullwidth" : " column dt-sc-one-column blog-fullwidth";
				$columns = 1;
				$post_thumbnail = 'blog-one-column';
			break;

			case 'one-half-column';
				$post_class = $show_sidebar ? " column dt-sc-one-half with-sidebar" : " column dt-sc-one-half";
				$columns = 2;
				$container_class = "apply-isotope";
				if($thumbnail_sidebar == "-single-sidebar") $post_thumbnail = 'blog-two-column';
				else $post_thumbnail = 'blogcourse-two-column';
			break;

			case 'one-third-column':
				$post_class = $show_sidebar ? " column dt-sc-one-third with-sidebar" : " column dt-sc-one-third";
				$columns = 3;
				$container_class = "apply-isotope";
				$post_thumbnail = 'blogcourse-three-column';
			break;

		endswitch;
		
		$post_thumbnail = $post_thumbnail.$thumbnail_sidebar;
		
		set_query_var( 'post_thumbnail', $post_thumbnail );
		
		echo "<div class='tpl-blog-holder {$container_class}'>";
		if( have_posts() ):
			$i = 1;
			while( have_posts() ):
				the_post();
				$temp_class = "";
				if($i == 1) $temp_class = $post_class." first"; else $temp_class = $post_class;
				if($i == $columns) $i = 1; else $i = $i + 1;
				$format = get_post_format(  get_the_id() );?>
				<div class="<?php echo $temp_class;?>"><?php  get_template_part( 'framework/loops/content');?></div>
<?php 		endwhile;
		endif;?>
		</div><!-- .tpl-blog-holder  -->


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