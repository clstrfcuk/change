<?php get_header();?>
<?php
$pageid = get_option('page_for_posts');
if($pageid > 0) {

	$tpl_default_settings = get_post_meta( $pageid, '_tpl_default_settings', TRUE );
	$tpl_default_settings = is_array( $tpl_default_settings ) ? $tpl_default_settings  : array();

	if($GLOBALS['force_enable'] == true)
		$page_layout = $GLOBALS['page_layout'];
	else
		$page_layout  = array_key_exists( "layout", $tpl_default_settings ) ? $tpl_default_settings['layout'] : "content-full-width";

} else {
	
	$page_layout 	= dttheme_option('specialty','post-archives-layout');
	
	if($GLOBALS['force_enable'] == true)
		$page_layout = $GLOBALS['page_layout'];
	else
		$page_layout = !empty($page_layout) ? $page_layout : "content-full-width";

}

$show_sidebar = $show_left_sidebar = $show_right_sidebar =  false;
$sidebar_class = "";

switch ( $page_layout ) {
	case 'with-left-sidebar':
		$page_layout = "page-with-sidebar with-left-sidebar";
		$show_sidebar = $show_left_sidebar = true;
		$sidebar_class = "secondary-has-left-sidebar";
	break;

	case 'with-right-sidebar':
		$page_layout = "page-with-sidebar with-right-sidebar";
		$show_sidebar = $show_right_sidebar	= true;
		$sidebar_class = "secondary-has-right-sidebar";
	break;

	case 'both-sidebar':
		$page_layout = "page-with-sidebar page-with-both-sidebar";
		$show_sidebar = $show_right_sidebar	= $show_left_sidebar = true;
		$sidebar_class = "secondary-has-both-sidebar";
	break;

	case 'content-full-width':
	default:
		$page_layout = "content-full-width";
	break;
}

if ( $show_sidebar ):
	if ( $show_left_sidebar ): ?>
		<!-- Secondary Left -->
		<section id="secondary-left" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'left' );?></section><?php
	endif;
endif;?>

<!-- **Primary Section** -->
<section id="primary" class="<?php echo $page_layout;?>">

		<?php
		
		if($pageid > 0) {
			
			$post_class = $show_sidebar ? " column dt-sc-one-column with-sidebar blog-fullwidth" : " column dt-sc-one-column blog-fullwidth";
			$container_class = "";
			
		} else {
	
			$post_layout = dttheme_option('specialty','post-archives-post-layout'); 
			$post_layout = !empty($post_layout) ? $post_layout : "one-column";
			$post_class = $container_class = "";
	
			switch($post_layout):
				case 'one-column':
					$post_class = $show_sidebar ? " column dt-sc-one-column with-sidebar blog-fullwidth" : " column dt-sc-one-column blog-fullwidth";
					$columns = 1;
				break;
	
				case 'one-half-column';
					$post_class = $show_sidebar ? " column dt-sc-one-half with-sidebar" : " column dt-sc-one-half";
					$columns = 2;
					$container_class = "apply-isotope";
				break;
	
				case 'one-third-column':
					$post_class = $show_sidebar ? " column dt-sc-one-third with-sidebar" : " column dt-sc-one-third";
					$columns = 3;
					$container_class = "apply-isotope";
				break;
	
			endswitch;
		
		}

		echo "<div class='tpl-blog-holder {$container_class}'>";
		if( have_posts() ):
			$i = 1;
			while( have_posts() ):
				the_post();
				$temp_class = "";
				if($i == 1) $temp_class = $post_class." first"; else $temp_class = $post_class;
				if($i == $columns) $i = 1; else $i = $i + 1;
				$format = get_post_format(  get_the_id() );?>
				<div class="<?php echo $temp_class;?>"><?php  get_template_part( 'framework/loops/content');?></div>
<?php 		endwhile;
		endif;?>
		</div><!-- .tpl-blog-holder  -->

      <!-- **Pagination** -->
      <div class="pagination">
            <div class="prev-post"><?php previous_posts_link('<span class="fa fa-angle-double-left"></span> Prev');?></div>
            <?php echo dttheme_pagination();?>
            <div class="next-post"><?php next_posts_link('Next <span class="fa fa-angle-double-right"></span>');?></div>
      </div><!-- **Pagination - End** -->
</section><!-- **Primary Section** -->

<?php
if ( $show_sidebar ):
	if ( $show_right_sidebar ): ?>
		<!-- Secondary Right -->
		<section id="secondary-right" class="secondary-sidebar <?php echo $sidebar_class;?>"><?php get_sidebar( 'right' );?></section><?php
	endif;
endif;
?>
<?php get_footer();?>	        