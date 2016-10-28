<?php get_header();
	  $portfolio_settings = get_post_meta ( $post->ID, '_portfolio_settings', TRUE );
	  $portfolio_settings = is_array ( $portfolio_settings ) ? $portfolio_settings : array ();
	  
	  $layout = isset( $portfolio_settings['layout'] ) ? $portfolio_settings['layout'] : 'single-portfloio-layout-one';
	  $container_start =  $container_middle =  $container_end = "";
	  if( $layout === "single-portfloio-layout-two" ) {
		  $container_start	 =	'<div class="column dt-sc-two-third first">';
		  $container_middle	 =	'</div>';
		  $container_middle  .=	'<div class="column dt-sc-one-third last">'; 
		  $container_end	 =	'</div>';
		  
	  }elseif( $layout === "single-portfloio-layout-three" ){
		  $container_start	 =	'<div class="column dt-sc-two-third right-gallery first">';
		  $container_middle	 =	'</div>';
		  $container_middle  .=	'<div class="column dt-sc-one-third last">'; 
		  $container_end	 =	'</div>';
	  }elseif( $layout === "single-portfloio-layout-one" ) {
		  $container_middle = "<div class='dt-sc-hr-invisible-small'></div>";
	  }
	  $pholder = dttheme_option('general', 'disable-placeholder-images');
	  ?>
      <!-- **Primary Section** -->
      <section id="primary" class="content-full-width"><?php
	  	if( have_posts() ):
			while( have_posts() ):
				the_post();?>
                <!-- #post-<?php the_ID()?> starts -->
                <article id="post-<?php the_ID(); ?>" <?php post_class('portfolio-single'); ?>><?php 
				echo $container_start; ?>
                	<ul class="portfolio-slider"><?php
						if( array_key_exists("items_name",$portfolio_settings) ) {
							foreach( $portfolio_settings["items_name"] as $key => $item ){
								$current_item = $portfolio_settings["items"][$key];
								
								if( "video" === $item ) {
									echo "<li>".wp_oembed_get( $current_item )."</li>";
								} else {
									echo "<li> <img src='{$current_item}' alt='' title='' /></li>";
								}
							}
						} elseif($pholder != 'on') {
							echo "<li> <img src='http://placehold.it/1170x878&text=Portfolio' alt='' title=''/></li>";
						}?></ul>
          <?php echo $container_middle;
		  
				if( array_key_exists("sub-title",$portfolio_settings) ):
					echo '<h3>'.dttheme_wp_kses($portfolio_settings["sub-title"]).'</h3>';
				endif;
		  
				the_content();?>
                
                <div class="project-details">
                <?php if( isset( $portfolio_settings["client-name"] ) ): ?>
                		<p> <span> <?php _e("Client","dt_themes");?> : </span>  <?php echo dttheme_wp_kses($portfolio_settings["client-name"]);?></p>
                <?php endif;
                    	the_terms($post->ID,'portfolio_entries','<p> <span>'.__(" Category","dt_themes").' : </span> ',', ','</p>'); ?>
                        <p> <span> <?php _e("Date","dt_themes");?> : </span> <?php the_date("d M Y");?></p>
                </div>
                
                <?php if( isset( $portfolio_settings["website-link"] ) ): ?>
                		<a class="dt-sc-button" title="" target="_blank" href="<?php echo esc_url($portfolio_settings["website-link"]);?>"> <span class="fa fa-globe"> </span><?php _e('See it Online','dt_themes');?></a>
                <?php endif;?>
                
                
          <?php if(array_key_exists("show-social-share",$portfolio_settings)):
					echo '<div class="portfolio-share">';
					dttheme_social_bookmarks('sb-portfolio');
					echo '</div>';
				endif;
				
				edit_post_link( __( 'Edit','dt_themes'));
				
				echo $container_end; ?>
                
				 <?php
                if(!dttheme_option('general', 'disable-portfolio-comment')): 
                    comments_template();
                endif;
                ?>             
                
                <!-- **Post Nav** -->
                <div class="post-nav-container">
                	<div class="post-prev-link"><?php previous_post_link('%link','<i class="fa fa-arrow-circle-left"> </i> %title<span> ('.__('Prev Entry','dt_themes').')</span>');?> </div>
                    <div class="post-next-link"><?php next_post_link('%link','<span> ('.__('Next Entry','dt_themes').')</span> %title <i class="fa fa-arrow-circle-right"> </i>');?></div>
                </div><!-- **Post Nav - End** -->
                
          </article><!-- #post-<?php the_ID()?> Ends -->
     <?php  endwhile;
		endif;?>
     <?php if(array_key_exists("show-related-items",$portfolio_settings)): ?>
     <!-- Related Posts Start-->
     	<div class="clear"> </div>
	    <div class="dt-sc-hr-invisible"> </div>
    	<h3><?php _e('Related Projects','dt_themes');?></h3><?php 
			$category_ids = array();
			
			$input  = wp_get_object_terms( $post->ID, 'portfolio_entries');
			
			foreach($input as $category) $category_ids[] = $category->term_id;
			
			$args = array(	'orderby' => 'rand',
					'showposts' => '3' ,
					'post__not_in' => array($post->ID),
					'tax_query' => array( array( 'taxonomy'=>'portfolio_entries', 'field'=>'id', 'operator'=>'IN', 'terms'=>$category_ids )));
					
			query_posts($args);
			if( have_posts() ):
				$count = 1;
				while( have_posts() ):
					the_post();
					$the_id = get_the_ID();
					
					$portfolio_item_meta = get_post_meta($the_id,'_portfolio_settings',TRUE);
					$portfolio_item_meta = is_array($portfolio_item_meta) ? $portfolio_item_meta  : array();
					
					$first = ( $count === 1 ) ? " first" : "";?>
                    <div class="portfolio column dt-sc-one-third gallery <?php echo $first;?>">
                    	<figure><?php $popup = "http://placehold.it/1170x878&text=Add%20Image%20/%20Video%20%20to%20Portfolio";
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
									echo "<img src='".$portfolio_item_meta['items'][0]."' width='1170' height='878' alt='' />";
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
                    </div>
<?php 			$count++;
				endwhile;
			endif;?>
     <!-- Related Posts End-->
     <?php endif;?>   
        </section><!-- **Primary Section** -->
<?php get_footer();?>