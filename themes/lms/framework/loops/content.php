<!-- #post-<?php the_ID()?> starts -->
<?php $post_meta = get_post_meta(get_the_id() ,'_dt_post_settings',TRUE);
    $post_meta = is_array( $post_meta ) ? $post_meta  : array(); 
	?>
<article id="post-<?php the_ID(); ?>" <?php post_class('blog-entry'); ?>>
    <div class="blog-entry-inner">

        <div class="entry-thumb">
            <?php 
			$format = get_post_format(  get_the_id() );
			$pholder = dttheme_option('general', 'disable-placeholder-images');
			if($post->post_type != 'dt_portfolios') {
			?>
				<?php if( $format === "image" || empty($format) ): ?>
                        <a href="<?php the_permalink();?>" title="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>">
                        <?php if( has_post_thumbnail() ):
                                $attachment_id = get_post_thumbnail_id(get_the_id());
                                $img_attributes = wp_get_attachment_image_src($attachment_id, $post_thumbnail);
                                echo "<img src='".$img_attributes[0]."' width='".$img_attributes[1]."' height='".$img_attributes[2]."' />";
                              elseif($pholder != 'on'):?>
                                <img src="http://placehold.it/1170x822&text=<?php echo the_title(); ?>" alt="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>" title="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>" />
                        <?php endif;?>
                        </a>
                <?php elseif( $format === "gallery" && array_key_exists("items", $post_meta) ):
                            echo "<ul class='entry-gallery-post-slider'>";
                            foreach ( $post_meta['items'] as $item ) { 
                                $attachment_id = dt_get_attachment_id_from_url($item);
                                $img_attributes = wp_get_attachment_image_src($attachment_id, $post_thumbnail);
                                echo "<li><img src='".$img_attributes[0]."' width='".$img_attributes[1]."' height='".$img_attributes[2]."' /></li>";
                            }
                            echo "</ul>";
                      elseif( $format === "video" && (array_key_exists('oembed-url', $post_meta) || array_key_exists('self-hosted-url', $post_meta)) ):
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
                      else:?>
                        <a href="<?php the_permalink();?>" title="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>"><?php
                            if( has_post_thumbnail() ):
                                $attachment_id = get_post_thumbnail_id(get_the_id());
                                $img_attributes = wp_get_attachment_image_src($attachment_id, $post_thumbnail);
                                echo "<img src='".$img_attributes[0]."' width='".$img_attributes[1]."' height='".$img_attributes[2]."' />";
                            elseif($pholder != 'on'):?>
                                <img src="http://placehold.it/1170x822&text=<?php echo the_title(); ?>" alt="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>" title="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>" />		<?php endif;?></a>                  
                <?php endif; ?>
            <?php } else { ?>
					<?php
					$portfolio_settings = get_post_meta ( $post->ID, '_portfolio_settings', TRUE );
					$portfolio_settings = is_array ( $portfolio_settings ) ? $portfolio_settings : array ();
					?>
					<a href="<?php the_permalink();?>" title="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>">
                    	<?php if(isset($portfolio_settings["items"][0])) { ?>
                        	<img src="<?php echo $portfolio_settings["items"][0]; ?>" alt="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>" title="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>" />
                        <?php } elseif($pholder != 'on') { ?>
                        	<img src="http://placehold.it/1170x822&text=<?php echo the_title(); ?>" alt="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>" title="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>" />
                        <?php } ?>
                    </a>
            <?php } ?>
            <div class="entry-thumb-desc"><?php echo dttheme_excerpt( 20 );?></div>
        </div>

        <div class="entry-details">

            <?php if(is_sticky()): ?>
                <div class="featured-post"> <span class="fa fa-trophy"> </span> <span class="text"> <?php _e('Featured','dt_themes');?></span></div>
            <?php endif;?>

            <div class="entry-meta">
                <div class="date">
                    <?php echo get_the_date('d M');?>
                </div>
                <a href="<?php the_permalink();?>" title="<?php printf(esc_attr__('%s'),the_title_attribute('echo=0'));?>" class="entry_format"> </a>
            </div>

            <div class="entry-title">
                <h4><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( '%s'), the_title_attribute( 'echo=0' ) ); ?>"><?php the_title(); ?></a></h4>
            </div>

            <div class="entry-metadata">
                <p class="author">
                    <i class="fa fa-user"> </i>
                    <a href="<?php echo get_author_posts_url(get_the_author_meta('ID'));?>" title="<?php _e('View all posts by ', 'dt_themes').get_the_author();?>"><?php echo get_the_author();?></a>
                </p><span> | </span>

                <?php the_tags("<p class='tags'><i class='fa fa-tags'> </i>",', ',"</p> <span> | </span>"); ?>

                <p class="category"><i class="fa fa-sitemap"> </i> <?php the_category(', '); ?></p><span> | </span>

                <p class="comments">
				<?php comments_popup_link( __('<span class="fa fa-comments-o"> </span> 0','dt_themes'), __('<span class="fa fa-comments-o"> </span> 1','dt_themes'), __('<span class="fa fa-comments-o"> </span> %','dt_themes'),'',__('<span class="fa fa-comments-o"> </span> 0','dt_themes'));?>
                </p>
            </div>
            
            <div class="entry-details-desc"><?php echo dttheme_excerpt( 20 );?></div>

        </div>

    </div>
</article><!-- #post-<?php the_ID()?> Ends -->