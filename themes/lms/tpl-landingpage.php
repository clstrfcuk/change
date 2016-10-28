<?php
/*
Template Name: Landing Page Template
*/
?>
<?php get_header(); ?>
<section id="primary" class="content-full-width">
    
    <?php $section_name_lp = str_replace(' ', '', trim($post->post_title)); ?>
    <div id="<?php echo $section_name_lp; ?>" class="landing-page-home">
        <?php
        global $post;
        dttheme_slider_section( $post->ID);	
        ?>
        <div class="landing-page-content">
            <?php
            if( have_posts() ):
                while( have_posts() ): the_post();
                    if(get_the_content() != ''):
                        the_content();
                    endif;
                endwhile;
            endif;
            ?>
            <div class="container">
            	<?php
				wp_link_pages(array('before' => '<div class="page-link"><strong>'.__('Pages:', 'dt_themes').'</strong> ', 'after' => '</div>', 'next_or_number' => 'number'));
				edit_post_link(__( ' Edit ','dt_themes' ),'','',$post->ID);
				?>
            </div>
        </div>
    </div>
            
    <?php
    #To get sections for landing page 
    $sections = dttheme_onepage_sections();
    #Begin Section Loop
    $sections_args = array( 'posts_per_page' => -1,'post__in' => (array) $sections,'orderby' => 'post__in', 'post_type'=>array('page'));
    $sections_query = new WP_Query($sections_args);
      
    if( $sections_query->have_posts() ):
        while( $sections_query->have_posts() ):
            $sections_query->the_post();
            
			$section_name = str_replace(' ', '', trim($post->post_title));
            $section_title = $post->post_title;
            
            ?>
            <!-- services section Starts here -->
            <div id="<?php echo $section_name;?>" class="landing-page">
                <?php dttheme_slider_section( $post->ID);	?>
                <div class="landing-page-content">
                    <?php
                    the_content();
					?>
                    <div class="container">
						<?php
                        wp_link_pages(array('before' => '<div class="page-link"><strong>'.__('Pages:', 'dt_themes').'</strong> ', 'after' => '</div>', 'next_or_number' => 'number'));
                        edit_post_link(__( ' Edit ','dt_themes' ),'','',$post->ID);	
                        ?>  
                    </div> 
                </div>
            </div>
         <?php
        endwhile;
    endif;	
    ?>
    
</section>
<?php get_footer(); ?>