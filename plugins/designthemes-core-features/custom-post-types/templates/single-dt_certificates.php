<?php get_header(); ?>

	<!-- ** Primary Section ** -->
	<section id="primary" class="content-full-width">
    
		<?php 
		if( have_posts() ): while( have_posts() ): the_post();
		$the_id = get_the_ID(); 
		?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class('dt-sc-certificate-single'); ?>>
            
            <?php
			echo '<div class="dt-sc-info-box">'.__('Please login to get access to your certificate !', 'dt_themes').'</div>';
			?>
                    
        </article>
            
		<?php
        endwhile; endif;
        ?>       

	</section><!-- ** Primary Section End ** -->
    
<?php get_footer(); ?>