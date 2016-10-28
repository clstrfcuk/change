<?php /*Template Name: Blog Template*/?>
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

		<!--- Start loop to show blog posts -->
		<?php $post_layout = isset( $tpl_default_settings['blog-post-layout'] ) ? $tpl_default_settings['blog-post-layout'] : "one-column";
		$post_per_page = isset($tpl_default_settings['blog-post-per-page']) ? $tpl_default_settings['blog-post-per-page'] : -1;
		$categories = isset($tpl_default_settings['blog-post-exclude-categories']) ? array_filter($tpl_default_settings['blog-post-exclude-categories']) : NULL;

		$hide_date_meta = isset( $tpl_default_settings['disable-date-info'] ) ? " hidden " : "";
		$hide_comment_meta = isset( $tpl_default_settings['disable-comment-info'] ) ? " hidden " : " comments ";
		$hide_author_meta = isset( $tpl_default_settings['disable-author-info'] ) ? " hidden " : "";
		$hide_category_meta = isset( $tpl_default_settings['disable-category-info'] ) ? " hidden " : "";
		$hide_tag_meta = isset( $tpl_default_settings['disable-tag-info'] ) ? " hidden " : "tags";

		$container_class = "";

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

			case 'post-thumb':
				$post_class = $show_sidebar ? " column blog-thumb with-sidebar" : " column blog-thumb";
				$columns = 1;
				$post_thumbnail = 'blog-thumb';
			break;
		endswitch;
		
		$post_thumbnail = $post_thumbnail.$thumbnail_sidebar;

		if ( empty( $categories ) ):
			$args = array( 'paged'=>get_query_var( 'paged' ), 'posts_per_page'=>$post_per_page, 'post_type'=> 'post' );
		else:
			$exclude_cats = array_unique( $categories );
			$args = array( 'paged'=>get_query_var( 'paged' ), 'posts_per_page'=>$post_per_page, 'category__not_in'=>$exclude_cats, 'post_type'=>'post' );
		endif;

		echo "<div class='tpl-blog-holder {$container_class} '>";

		query_posts($args);
		if( have_posts() ):
			$i = 1;
			while( have_posts() ):
				the_post();

				$temp_class = "";
				if($i == 1) $temp_class = $post_class." first"; else $temp_class = $post_class;
				if($i == $columns) $i = 1; else $i = $i + 1;

				$format = get_post_format(  get_the_id() );?>

				<div class="<?php echo $temp_class;?>">
					<!-- #post-<?php the_ID()?> starts -->
					<article id="post-<?php the_ID(); ?>" <?php post_class('blog-entry'); ?>>
						<div class="blog-entry-inner">

							<div class="entry-thumb">
								<?php 
								$post_meta = get_post_meta(get_the_id() ,'_dt_post_settings',TRUE);
                                $post_meta = is_array( $post_meta ) ? $post_meta  : array(); 
								$pholder = dttheme_option('general', 'disable-placeholder-images');
								?>
                                <?php if( $format === "image" || empty($format) ): ?>
                                        <a href="<?php the_permalink();?>" title="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>">
                                        <?php if( has_post_thumbnail() ):
												$attachment_id = get_post_thumbnail_id(get_the_id());
												$img_attributes = wp_get_attachment_image_src($attachment_id, $post_thumbnail);
												echo "<img src='".$img_attributes[0]."' width='".$img_attributes[1]."' height='".$img_attributes[2]."' />";
                                              elseif($pholder != 'on'):?>
                                                <img src="http://placehold.it/1170x822&text=<?php the_title(); ?>" alt="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>" title="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>" />
                                        <?php endif;?>
                                        </a>
                                <?php elseif( $format === "gallery" && array_key_exists("items", $post_meta)):
                                            echo "<ul class='entry-gallery-post-slider'>";
                                            foreach ( $post_meta['items'] as $item ) {
												$attachment_id = dt_get_attachment_id_from_url($item);
												$img_attributes = wp_get_attachment_image_src($attachment_id, $post_thumbnail);
												echo "<li><img src='".$img_attributes[0]."' width='".$img_attributes[1]."' height='".$img_attributes[2]."' /></li>";
											}
                                            echo "</ul>";
                                      elseif( $format === "video" && ( array_key_exists('oembed-url', $post_meta) || array_key_exists('self-hosted-url', $post_meta) ) ):
                                            if( array_key_exists('oembed-url', $post_meta) ):
                                                echo "<div class='dt-video-wrap'>".wp_oembed_get($post_meta['oembed-url']).'</div>';
                                            elseif( array_key_exists('self-hosted-url', $post_meta) ):
                                                echo "<div class='dt-video-wrap'>".wp_video_shortcode( array('src' => $post_meta['self-hosted-url']) ).'</div>';
                                            endif;
                                      elseif( $format === "audio" && (array_key_exists('oembed-url', $post_meta) || array_key_exists('self-hosted-url', $post_meta)) ):
                                            if( array_key_exists('oembed-url', $post_meta) ):
                                                echo wp_oembed_get($post_meta['oembed-url']);
                                            elseif( array_key_exists('self-hosted-url', $post_meta) ):
                                                echo wp_audio_shortcode( array('src' => $post_meta['self-hosted-url']) );
                                            endif;
                                      else: ?>
                                        <a href="<?php the_permalink();?>" title="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>"><?php
                                            if( has_post_thumbnail() ):
												$attachment_id = get_post_thumbnail_id(get_the_id());
												$img_attributes = wp_get_attachment_image_src($attachment_id, $post_thumbnail);
												echo "<img src='".$img_attributes[0]."' width='".$img_attributes[1]."' height='".$img_attributes[2]."' />";
                                            elseif($pholder != 'on'):?>
                                                <img src="http://placehold.it/1170x822&text=<?php the_title(); ?>" alt="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>" title="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>" />
                                        <?php endif;?></a>
                                <?php endif; ?>
								 <?php if( array_key_exists('blog-post-excerpt-length',$tpl_default_settings) ): ?>
								 	<div class="entry-thumb-desc"><?php echo dttheme_excerpt($tpl_default_settings['blog-post-excerpt-length']);?></div>
								 <?php endif;?>
							</div>

							<div class="entry-details">

								<?php if(is_sticky()): ?>
									<div class="featured-post"> <span class="fa fa-trophy"> </span> <span class="text"> <?php _e('Featured','dt_themes');?> </span></div>
								<?php endif;?>
                                
                                <div class="entry-meta">
                                    <div class="date <?php echo $hide_date_meta;?> ">
                                        <?php echo get_the_date('d M');?>
                                    </div>
                                    <a href="<?php the_permalink();?>" title="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>" class="entry_format"> </a>
                                </div>

								<div class="entry-title">
									<h4>
										<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( '%s'), the_title_attribute( 'echo=0' ) ); ?>"><?php the_title(); ?></a>
									</h4>
								</div>

								<div class="entry-metadata">

									<p class="author <?php echo $hide_author_meta;?>">
										<i class="fa fa-user"> </i>
										<a href="<?php echo get_author_posts_url(get_the_author_meta('ID'));?>" title="<?php _e('View all posts by ', 'dt_themes').get_the_author();?>"><?php echo get_the_author();?></a>
									</p>
									<span class="<?php echo $hide_author_meta;?>"> | </span>

									<?php the_tags("<p class='tags {$hide_tag_meta}'><i class='fa fa-tags'> </i>",', ',"</p> <span class='{$hide_tag_meta}'> | </span>");?>

									<p class="<?php echo $hide_category_meta;?> category"><i class="fa fa-sitemap"> </i> <?php the_category(', '); ?></p>
									<span class="<?php echo $hide_category_meta;?>"> | </span>

									<p class="<?php echo $hide_comment_meta;?> comments">
										<?php comments_popup_link( __('<span class="fa fa-comments-o"> </span> 0','dt_themes'), __('<span class="fa fa-comments-o"> </span> 1','dt_themes'), __('<span class="fa fa-comments-o"> </span> %','dt_themes'),'',__('<span class="fa fa-comments-o"> </span> 0','dt_themes'));?>
									</p>	
								</div><!--  .entry-metadata -->
                                
								 <?php if( array_key_exists('blog-post-excerpt-length',$tpl_default_settings) ): ?>
								 	<div class="entry-details-desc"><?php echo dttheme_excerpt($tpl_default_settings['blog-post-excerpt-length']);?></div>
								 <?php endif;?>

							</div>

						</div>
					</article><!-- #post-<?php the_ID()?> Ends -->
				</div>

			<?php endwhile;
		endif;?>
        
        </div><!-- .tpl-blog-holder  -->

		<!-- **Pagination** -->
		<div class="pagination">
			<div class="prev-post"><?php previous_posts_link('<span class="fa fa-angle-double-left"></span> Prev');?></div>
			<?php echo dttheme_pagination();?>
			<div class="next-post"><?php next_posts_link('Next <span class="fa fa-angle-double-right"></span>');?></div>
		</div><!-- **Pagination - End** -->
	
	<!--- End of loop to show blog posts -->

	</section><!-- ** Primary Section End ** --><?php

	if ( $show_sidebar ):
		if ( $show_right_sidebar ): ?>
			<!-- Secondary Right -->
			<section id="secondary-right" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'right' );?></section><?php
		endif;
	endif;?>
<?php get_footer(); ?>