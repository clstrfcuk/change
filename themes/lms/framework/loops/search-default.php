<?php
$post_layout = dttheme_option('specialty','search-post-layout'); 
$post_layout = !empty($post_layout) ? $post_layout : "one-column";
$post_class = $columns = "";

switch($post_layout):
	case 'one-column':
		$post_class = " column dt-sc-one-column ";
		$columns = "";
		$post_thumbnail = 'blog-one-column';
	break;

	case 'one-half-column';
		$post_class = " column dt-sc-one-half";
		$columns = 2;
		if($thumbnail_sidebar == "-single-sidebar") $post_thumbnail = 'blog-two-column';
		else $post_thumbnail = 'blogcourse-two-column';
	break;

	case 'one-third-column':
		$post_class = " column dt-sc-one-third ";
		$columns = 3;
		$post_thumbnail = 'blogcourse-three-column';
	break;
endswitch;

$post_thumbnail = $post_thumbnail.$thumbnail_sidebar;
set_query_var( 'post_thumbnail', $post_thumbnail );

if( have_posts() ):
	echo "<div class='tpl-blog-holder apply-isotope'>";
	$i = 1;
	while( have_posts() ):
		the_post();
		$temp_class = "";
		if($i == 1) $temp_class = $post_class." first"; else $temp_class = $post_class;
		if($i == $columns) $i = 1; else $i = $i + 1;
		$format = get_post_format(  get_the_id() );?>
		<div class="<?php echo $temp_class;?>"><?php  get_template_part( 'framework/loops/content');?></div>
<?php endwhile;
	echo '</div>';
else:?>
	<h2><?php _e( 'Nothing Found','dt_themes'); ?></h2>
	<h5><?php _e( 'Apologies, but no results were found for the requested archive.', 'dt_themes'); ?></h5>
	<?php get_search_form();?>

<?php 	endif;?>

<!-- **Pagination** -->
<div class="pagination">
    <div class="prev-post"><?php previous_posts_link('<span class="fa fa-angle-double-left"></span> Prev');?></div>
    <?php echo dttheme_pagination();?>
    <div class="next-post"><?php next_posts_link('Next <span class="fa fa-angle-double-right"></span>');?></div>
</div><!-- **Pagination - End** -->