<!-- #post-<?php the_ID(); ?> -->
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php the_content(); 
	  wp_link_pages( array(	'before'			=>	'<div class="page-link">',
	  						'after'				=>	'</div>',
							'link_before'		=>	'<span>',
							'link_after'		=>	'</span>',
							'next_or_number'	=>	'number',
							'pagelink' 			=>	'%',
							'echo' 				=>	1 ) );
	 
	 edit_post_link( __( ' Edit ','dt_themes' ) );?>
	
</div><!-- #post-<?php the_ID(); ?> -->