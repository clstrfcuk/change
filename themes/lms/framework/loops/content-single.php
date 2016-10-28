<?php $tpl_default_settings = get_post_meta($post->ID,'_dt_post_settings',TRUE);
	  $tpl_default_settings = is_array($tpl_default_settings) ? $tpl_default_settings  : array();

	$hide_date_meta = isset( $tpl_default_settings['disable-date-info'] ) ? " hidden " : "";
	$hide_comment_meta = isset( $tpl_default_settings['disable-comment-info'] ) ? " hidden " : " comments ";
	
	$hide_author_meta = isset( $tpl_default_settings['disable-author-info'] ) ? " hidden " : "";
	$hide_category_meta = isset( $tpl_default_settings['disable-category-info'] ) ? " hidden " : "";
	$hide_tag_meta = isset( $tpl_default_settings['disable-tag-info'] ) ? " hidden " : "tags";

	$format = get_post_format(  $post->ID );
	
	$pholder = dttheme_option('general', 'disable-placeholder-images');
	?>

<!--#post-<?php the_ID()?> starts -->
<article id="post-<?php the_ID();?>" <?php post_class(array('blog-entry','blog-single-entry'));?>>
<div class="blog-entry-inner">

	<div class="entry-thumb">
		<?php if( ($format === "image" || empty($format)) && !array_key_exists("disable-featured-image", $tpl_default_settings) ): ?>
				<a href="<?php the_permalink();?>" title="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>">
				<?php if( has_post_thumbnail() ):
						the_post_thumbnail("full");
					  elseif($pholder != "on"):?>
					  	<img src="http://placehold.it/1170x822&text=Image" alt="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>" title="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>" />
				<?php endif;?>
				</a>
		<?php elseif( $format === "gallery" &&  array_key_exists("items", $tpl_default_settings) ):
				echo "<ul class='entry-gallery-post-slider'>";
				foreach ( $tpl_default_settings['items'] as $item ) { echo "<li><img src='{$item}' alt='' /></li>";	}
				echo "</ul>";
			  elseif( $format === "video" && (array_key_exists('oembed-url', $tpl_default_settings) || array_key_exists('self-hosted-url', $tpl_default_settings)) ):
				echo "<div class='dt-video-wrap'>";
					if( array_key_exists("oembed-url", $tpl_default_settings) )
						echo wp_oembed_get($tpl_default_settings['oembed-url']);
					elseif( array_key_exists("self-hosted-url", $tpl_default_settings) )
						echo wp_video_shortcode( array('src' => $post_meta['self-hosted-url']) );
				echo "</div>";
			  elseif( $format === "audio" && (array_key_exists('oembed-url', $tpl_default_settings) || array_key_exists('self-hosted-url', $tpl_default_settings)) ):
			  		if( array_key_exists("oembed-url", $tpl_default_settings) ):
						echo wp_oembed_get($tpl_default_settings['oembed-url']);
			  		elseif( array_key_exists("self-hosted-url", $tpl_default_settings) ):
						echo wp_audio_shortcode( array('src' => $post_meta['self-hosted-url']) );
					endif;
			 elseif(!array_key_exists("disable-featured-image", $tpl_default_settings)):
			 ?>
                  	<a href="<?php the_permalink();?>" title="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>"><?php
						if( has_post_thumbnail() ):
							the_post_thumbnail("full");
						elseif($pholder != "on"):?>
                        	<img src="http://placehold.it/1170x822&text=Image" alt="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>" title="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>" />		<?php endif;?></a>                  
            <?php endif; ?>
	</div>

	<div class="entry-details">

		<?php if(is_sticky()): ?>
			<div class="featured-post"> <span class="fa fa-trophy"> </span> <span class="text"> <?php _e('Featured','dt_themes');?> </span></div>
        <?php endif;?>

        <div class="entry-meta">
            <div class="date <?php echo $hide_date_meta;?>">
                <?php echo get_the_date('d M');?>
            </div>
            <a href="<?php the_permalink();?>" title="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>" class="entry_format"> </a>
        </div>

		<div class="entry-title">
			<h4><a href="<?php the_permalink();?>" title="<?php printf( esc_attr__('%s'), the_title_attribute('echo=0'));?>"><?php the_title();?></a></h4>
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
				<?php comments_popup_link( __('<span class="fa fa-comments-o"> </span> 0','dt_themes'), __('<span class="fa fa-comments-o"> </span> 1','dt_themes'), __('<span class="fa fa-comments-o"> </span> %','dt_themes'),'',__('<span class="fa fa-comments-o"> </span>','dt_themes'));?>
            </p>
		</div><!-- .entry-metadata -->

	</div><!-- .entry-details -->
    
    <div class="entry-body">
    <?php the_content();

        wp_link_pages( array('before'=>'<div class="page-link">', 'after'=>'</div>', 'link_before'=>'<span>', 'link_after'=>'</span>', 'next_or_number'=>'number',	'pagelink' => '%', 'echo' => 1 ) );

        echo '<div class="social-bookmark">';
            show_fblike('post');
            show_googleplus('post');
            show_twitter('post');
            show_stumbleupon('post');
            show_linkedin('post');
            show_delicious('post');
            show_pintrest('post');
            show_digg('post');
        echo '</div>';

        echo '<div class="social-share">';
            dttheme_social_bookmarks('sb-post');
        echo '</div>';
        
        edit_post_link( __( ' Edit ','dt_themes' ) );?>
    </div><!-- .entry-body -->

</div>
</article><!-- #post-<?php the_ID()?> Ends -->
<?php $dttheme_options = get_option(IAMD_THEME_SETTINGS);
	$dttheme_general = $dttheme_options['general'];
	$globally_disable_post_comment =  array_key_exists('global-post-comment',$dttheme_general) ? true : false; 

	if( (!$globally_disable_post_comment) && (! isset($tpl_default_settings['disable-comment'])) ):?>
		<!-- **Comment Entries** -->   	
			<?php  comments_template('', true); ?>
        <!-- **Comment Entries - End** -->
<?php endif;?>