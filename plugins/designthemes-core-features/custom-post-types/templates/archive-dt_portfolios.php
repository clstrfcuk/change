<?php get_header();

	$page_layout 	= dttheme_option('specialty','portfolio-archives-layout');
	
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
	
	$pholder = dttheme_option('general', 'disable-placeholder-images');
	
	if ( $show_sidebar ):
		if ( $show_left_sidebar ): ?>
			<!-- Secondary Left -->
			<section id="secondary-left" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'left' );?></section><?php
		endif;
	endif;?>

	<!-- ** Primary Section ** -->
	<section id="primary" class="<?php echo $page_layout;?>">

		<div class="dt-sc-clear"></div>
		<!-- Start loop to show Portfolio Items -->
		<?php $allow_space  =  " with-space ";

		$post_layout = dttheme_option('specialty','portfolio-archives-post-layout'); 
		$post_layout = !empty($post_layout) ? $post_layout : "one-column";
		$post_class = "";

		#TO SET POST LAYOUT
		switch($post_layout):

			case 'one-column':
				$post_class = $show_sidebar ? " portfolio column dt-sc-one-column with-sidebar" : " portfolio column dt-sc-one-column ";
				$columns = 1;
				$post_thumbnail = 'portfolio-one-column';
			break;

			case 'one-half-column';
				$post_class = $show_sidebar ? " portfolio column dt-sc-one-half with-sidebar " : " portfolio column dt-sc-one-half ";
				$columns = 2;
				$post_thumbnail = 'portfolio-two-column';
			break;
			
			case 'one-third-column':
				$post_class = $show_sidebar ? " portfolio column dt-sc-one-third with-sidebar " : " portfolio column dt-sc-one-third ";
				$columns = 3;
				$post_thumbnail = 'portfolio-three-column';
			break;

			case 'one-fourth-column':
				$post_class = $show_sidebar ? " portfolio column dt-sc-one-fourth with-sidebar " : "portfolio column dt-sc-one-fourth";
				$columns = 4;
				$post_thumbnail = 'portfolio-four-column';
			break;
		endswitch;			

		$post_thumbnail = $post_thumbnail.$thumbnail_sidebar;
		
	?>

	<!-- **Portfolio Container** -->
	<div class="dt-sc-portfolio-container gallery <?php echo $allow_space;?>"><?php

		if( have_posts() ):
			$i = 1;
			while( have_posts() ):
				the_post();

				$temp_class = "";
				if($i == 1) $temp_class = $post_class." first"; else $temp_class = $post_class;
				if($i == $columns) $i = 1; else $i = $i + 1;

				$the_id = get_the_ID();

				$portfolio_item_meta = get_post_meta($the_id,'_portfolio_settings',TRUE);
				$portfolio_item_meta = is_array($portfolio_item_meta) ? $portfolio_item_meta  : array();

				?>
				<!-- Portfolio Item -->
				<div id="<?php echo "portfolio-{$the_id}";?>" class="<?php echo $temp_class.$allow_space;?>">
					<figure>
						<?php $popup = "http://placehold.it/1170x878&text=Add%20Image%20/%20Video%20%20to%20Portfolio";
								if( array_key_exists('items_name', $portfolio_item_meta) ) {
								$item =  $portfolio_item_meta['items_name'][0];
								$popup = $portfolio_item_meta['items'][0];

								if( "video" === $item ) {
									$items = array_diff( $portfolio_item_meta['items_name'] , array("video") );
									if( !empty($items) ) {
										echo "<img src='".$portfolio_item_meta['items'][key($items)]."' width='1170' height='878' alt='' />";	
									} elseif($pholder != 'on') {
										echo '<img src="http://placehold.it/1170x878&text=Add%20Image%20/%20Video%20%20to%20Portfolio" width="1170" height="878" alt="" />';
									}
								} else {
									$attachment_id = dt_get_attachment_id_from_url($portfolio_item_meta['items'][0]);
									$img_attributes = wp_get_attachment_image_src($attachment_id, $post_thumbnail);
									echo "<img src='".$img_attributes[0]."' width='".$img_attributes[1]."' height='".$img_attributes[2]."' />";
								}
							} elseif($pholder != 'on') {
								echo "<img src='{$popup}'  alt=''/>";
							}?>
						<div class="image-overlay"> 
							<div class="image-overlay-details"> 
								<h5><a href="<?php the_permalink();?>" title="<?php printf( esc_attr__('%s'), the_title_attribute('echo=0'));?>"><?php the_title();?></a></h5>
								<?php if( array_key_exists("sub-title",$portfolio_item_meta) ): ?>
									<h6><?php echo $portfolio_item_meta["sub-title"];?></h6>
								<?php endif;?>
								<div class="links">
									<a href="<?php echo $popup;?>" data-gal="prettyPhoto[gallery]" title="<?php printf( esc_attr__('%s'), the_title_attribute('echo=0'));?>"> <span class="fa fa-search"> </span> </a>
									<a href="<?php the_permalink();?>" title="<?php printf( esc_attr__('%s'), the_title_attribute('echo=0'));?>"> <span class="fa fa-link"> </span> </a>
								</div>
							</div>
							<a class="close-overlay hidden"> x </a>
						</div>
							
					</figure>
				</div><!-- Portfolio Item -->
			<?php endwhile;
		endif;?></div><!-- **Portfolio Container** -->

		<div class="dt-sc-clear"></div>
		<div class="dt-sc-hr-invisible"> </div>

		<!-- **Pagination** -->
		<div class="pagination">
			<div class="prev-post"><?php previous_posts_link('<span class="fa fa-angle-double-left"></span> Prev');?></div>
			<?php echo dttheme_pagination();?>
			<div class="next-post"><?php next_posts_link('Next <span class="fa fa-angle-double-right"></span>');?></div>
		</div><!-- **Pagination - End** -->
	   
		<!-- End loop to show Portfolio Items -->

	</section><!-- ** Primary Section End ** --><?php

	if ( $show_sidebar ):
		if ( $show_right_sidebar ): ?>
			<!-- Secondary Right -->
			<section id="secondary-right" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'right' );?></section><?php
		endif;
	endif;?>
<?php get_footer(); ?>