<?php /*Template Name: Portfolio Template*/?>
<?php get_header();

	$tpl_default_settings = get_post_meta( $post->ID, '_tpl_default_settings', TRUE );
	$tpl_default_settings = is_array( $tpl_default_settings ) ? $tpl_default_settings  : array();

	if($GLOBALS['force_enable'] == true)
		$page_layout = $GLOBALS['page_layout'];
	else
		$page_layout  = array_key_exists( "layout", $tpl_default_settings ) ? $tpl_default_settings['layout'] : "content-full-width";

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

	$pholder = dttheme_option('general', 'disable-placeholder-images');
	
	if ( $show_sidebar ):
		if ( $show_left_sidebar ): ?>
			<!-- Secondary Left -->
			<section id="secondary-left" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'left' );?></section><?php
		endif;
	endif;?>

	<!-- ** Primary Section ** -->
	<section id="primary" class="<?php echo $page_layout;?>"><?php
		if( have_posts() ):
			while( have_posts() ):
				the_post();
				get_template_part( 'framework/loops/content', 'page' );
			endwhile;
		endif;?>

		<div class="dt-sc-clear"></div>
		<!-- Start loop to show Portfolio Items -->
		<?php $allow_space  =  array_key_exists("grid_space",$tpl_default_settings) ? " with-space " : " no-space ";
			$post_layout	=	isset( $tpl_default_settings['portfolio-post-layout'] ) ? $tpl_default_settings['portfolio-post-layout'] : "one-half-column";
			$post_per_page	=	$tpl_default_settings['portfolio-post-per-page'];

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

			$categories =	isset($tpl_default_settings['portfolio-categories']) ? array_filter($tpl_default_settings['portfolio-categories']) : "";
			if(empty($categories)):
				$categories = get_categories('taxonomy=portfolio_entries&hide_empty=1');
			else:
				$args = array('taxonomy'=>'portfolio_entries','hide_empty'=>1,'include'=>$categories);
				$categories = get_categories($args);
			endif;?>

			<?php if( sizeof($categories) > 1 ) :
			 		if( array_key_exists("filter",$tpl_default_settings) && (!empty($categories)) ):
			 			$post_class .= " all-sort ";?>
			 			<div class="dt-sc-sorting-container">
			 				<a href="#" class="active-sort" title="" data-filter=".all-sort"><?php _e('All','dt_themes');?></a>
			 				<?php foreach( $categories as $category ): 
								$cat_name = str_replace(' ', '-', $category->cat_name); ?>
			 					<a href='#' data-filter=".<?php echo $cat_name; ?>-sort"><?php echo $category->cat_name;?></a>
			 				<?php endforeach;?>
			 			</div>
			<?php 	endif;
			 	endif;?>

			<!-- **Portfolio Container** -->
			<div class="dt-sc-portfolio-container gallery <?php echo $allow_space;?>"><?php
				$args = array();
				$categories = array_filter($tpl_default_settings['portfolio-categories']);

				if(is_array($categories) && !empty($categories)):
					$terms = $categories;
					$args = array( 
						'orderby' => 'ID',
						'order' => 'ASC',
						'paged' => get_query_var( 'paged' ),
						'posts_per_page' => $post_per_page,
						'tax_query' => array( array( 'taxonomy'=>'portfolio_entries', 'field'=>'id', 'operator'=>'IN', 'terms'=>$terms ) ) );
				else:
					$args = array( 'paged' => get_query_var( 'paged' ) ,'posts_per_page' => $post_per_page,'post_type' => 'dt_portfolios');
				endif;

				query_posts($args);
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

						#Find sort class by using the portfolio_entries
                        $sort = " ";
                        if( array_key_exists("filter",$tpl_default_settings) ):
                        	$item_categories = get_the_terms( $the_id, 'portfolio_entries' );
                        	if(is_object($item_categories) || is_array($item_categories)):
                        		foreach ($item_categories as $category):
									$cat_slug = str_replace(' ', '-', $category->name);
                        			$sort .= $cat_slug.'-sort ';
                        		endforeach;
                            endif;
                         endif;?>
                        <!-- Portfolio Item -->
                        <div id="<?php echo "portfolio-{$the_id}";?>" class="<?php echo $temp_class.$sort.$allow_space;?>">
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
                        				echo "<img src='{$popup}' alt='' />";
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
               
               <?php if(dttheme_pagination() != NULL) { ?>
                   <div class="dt-sc-hr-invisible"> </div>
    
                    <!-- **Pagination** -->
                    <div class="pagination">
                        <div class="prev-post"><?php previous_posts_link('<span class="fa fa-angle-double-left"></span> Prev');?></div>
                        <?php echo dttheme_pagination();?>
                        <div class="next-post"><?php next_posts_link('Next <span class="fa fa-angle-double-right"></span>');?></div>
                    </div><!-- **Pagination - End** -->
               <?php } ?>
               
        <?php
		wp_link_pages( array('before' => '<div class="page-link">','after' =>'</div>', 'link_before' => '<span>', 'link_after' => '</span>', 'next_or_number' => 'number', 'pagelink' => '%', 'echo' => 1 ) );
		edit_post_link( __( ' Edit ','dt_themes' ) );					
		?>
               
		<!-- End loop to show Portfolio Items -->

	</section><!-- ** Primary Section End ** --><?php

	if ( $show_sidebar ):
		if ( $show_right_sidebar ): ?>
			<!-- Secondary Right -->
			<section id="secondary-right" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'right' );?></section><?php
		endif;
	endif;?>
<?php get_footer(); ?>