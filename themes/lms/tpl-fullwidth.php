<?php /*Template Name: Full Width Template*/?>
<?php get_header();?>

	<!-- ** Primary Section ** -->
	<section id="primary" class="content-full-width"><?php
		if( have_posts() ):
			while( have_posts() ):
				the_post();
				get_template_part( 'framework/loops/content-fullwidth', 'page' );
			endwhile;
		endif;?>
	</section><!-- ** Primary Section End ** -->
<?php get_footer(); ?>