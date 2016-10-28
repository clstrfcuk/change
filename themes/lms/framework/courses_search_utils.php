<?php require_once("../../../../wp-load.php"); ?>
<?php

$paged    = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

$post_per_page = isset($_REQUEST['postperpage']) ? $_REQUEST['postperpage'] : -1;
$curr_page = isset($_REQUEST['curr_page']) ? $_REQUEST['curr_page'] : 1;
$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;

$costtype = isset($_REQUEST['costtype']) ? $_REQUEST['costtype'] : '';
$searchtext = isset($_REQUEST['searchtext']) ? $_REQUEST['searchtext'] : '';
$coursetype = isset($_REQUEST['coursetype']) ? $_REQUEST['coursetype'] : '';
$subcoursetype = isset($_REQUEST['subcoursetype']) ? $_REQUEST['subcoursetype'] : '';
$webinar = isset($_REQUEST['webinar']) ? $_REQUEST['webinar'] : '';

global $sitepress;
if(defined('ICL_LANGUAGE_CODE') && !empty($_REQUEST['lang']))
{
	if($sitepress->get_default_language() == $_REQUEST['lang'])
		$sitepress->switch_lang($_REQUEST['lang'], true);
}

$wp_query = new WP_Query();

if(isset($searchtext))
	$searchtext = $searchtext;
else
	$searchtext = '';
	
$tax_query = $meta_query = array();
if( !empty( $coursetype) && $coursetype > 0 ) {
	$tax_query[] = array( 'taxonomy' => 'course_category',
		'field' => 'id',
		'terms' => $coursetype,
		'operator' => 'IN',);
}
if( !empty($subcoursetype) && $subcoursetype > 0 ) {
	$tax_query[] = array( 'taxonomy' => 'course_category',
		'field' => 'id',
		'terms' => $subcoursetype,
		'operator' => 'IN',);
}

if($costtype == 'paid') {
	
	$meta_query[] = array(
					'key'     => 'starting-price',
					'value'   => 0,
					'type'    => 'numeric',
					'compare' => '>'
					);
					
} else if($costtype == 'free') {
	
	$meta_query[] = array(
					'key'     => 'starting-price',
					'compare' => 'NOT EXISTS'
					);
					
}
	
if( isset($webinar) && $webinar == 'on' ) {
	$meta_query[] = array( 'key' => 'course-video', 'compare' => 'EXISTS');
}

echo '<span id="dt-course-search-datas" data-postperpage="'.$post_per_page.'" data-curr_page="'.$curr_page.'" data-offset="'.$offset.'" data-costtype="'.$costtype.'" data-searchtext="'.$searchtext.'" data-coursetype="'.$coursetype.'" data-subcoursetype="'.$subcoursetype.'" data-webinar="'.$webinar.'" style="display:none;"></span>';

$courses_args = array(
	'offset' => $offset,
	'post_type' => 'dt_courses',
	'posts_per_page' => $post_per_page,
	'paged' => $curr_page,
	's' => $searchtext,
	'tax_query' => $tax_query,
	'meta_query' => $meta_query,
	'order_by' => 'published',
);

if(defined('ICL_LANGUAGE_CODE') && $sitepress->get_default_language() != $_REQUEST['lang']) $courses_args['suppress_filters'] = 'false';

echo '<div class="dt-sc-clear"></div>
	  <div class="dt-sc-hr-invisible-medium"></div>';

$wp_query->query( $courses_args );
if( $wp_query->have_posts() ):

	echo '<table border="0" cellpadding="0" class="courses-table-list tablesorter">
			<thead>
			  <tr>
				<th class="courses-table-title-header" scope="col">'.__('Course Name','dt_themes').'</th>
				<th class="courses-table-type-header" scope="col">'.__('Course Type','dt_themes').'</th>
				<th class="courses-table-lessons-header" scope="col">'.__('Lessons #','dt_themes').'</th>
				<th class="courses-table-cost-header" scope="col">'.__('Cost','dt_themes').'</th>
				<th class="courses-table-length-header" scope="col">'.__('Length','dt_themes').'</th>
			  </tr>
			</thead>
			<tbody>';
		  
	while( $wp_query->have_posts() ):
	
		$wp_query->the_post();

		$the_id = get_the_ID();
		$permalink = get_permalink($the_id);
		$title = get_the_title($the_id);
		
		$terms = get_the_terms($the_id,'course_category');
		$course_terms = array();
		foreach ( $terms as $term ) {
			$course_terms[] = '<a href="'.get_term_link( $term->slug, 'course_category' ).'">'.$term->name.'</a>';
		}
		$course_terms = join( ", ", $course_terms );
			
			
		$lesson_args = array('post_type' => 'dt_lessons', 'posts_per_page' => -1, 'meta_key' => 'dt_lesson_course', 'meta_value' => $the_id );
		$lessons_array = get_posts( $lesson_args );
		
		$duration = 0;
		$count = count($lessons_array);
		foreach($lessons_array as $lesson) {
			$lesson_data = get_post_meta($lesson->ID, '_lesson_settings');
			if(isset($lesson_data[0]['lesson-duration'])) {
				$duration = $duration + $lesson_data[0]['lesson-duration'];
			}
		}
		
		if($duration > 0) {
			$hours = floor($duration/60); 
			$mins = $duration % 60; 
			
			if($hours == 0) {
				$duration = $mins . __(' mins ', 'dt_themes'); 				
			} elseif($hours == 1) {
				$duration = $hours .  __(' hour ', 'dt_themes') . $mins . __(' mins ', 'dt_themes'); 				
			} else {
				$duration = $hours . __(' hours ', 'dt_themes') . $mins . __(' mins ', 'dt_themes'); 				
			}
		} else {
			$duration = '';
		}

		$starting_price = dttheme_wp_kses(get_post_meta(get_the_ID(), 'starting-price', true));
		if($starting_price != ''): 
			if(dttheme_option('dt_course','currency-position') == 'after-price') 
				$cost =  $starting_price.dttheme_wp_kses(dttheme_option('dt_course','currency')); 
			else
				$cost = dttheme_wp_kses(dttheme_option('dt_course','currency')).$starting_price; 
		else:
			$cost = __('Free', 'dt_themes'); 
		endif;	
					
										
		echo '<tr>
				<td class="courses-table-title"><a href="'.$permalink.'">'.$title.'</a></td>
				<td class="courses-table-type">'.$course_terms.'</td>
				<td class="courses-table-lessons">'.$count.'</td>
				<td class="courses-table-cost">'.$cost.'</td>
				<td class="courses-table-length">'.$duration.'</td>
			  </tr>';
		
	endwhile;
	
	echo '</tbody>';
	echo '</table>';
	
	$total_posts = $wp_query->found_posts;
	echo dtthemes_ajax_pagination($post_per_page, $curr_page, $total_posts, '');

	echo '<script type="text/javascript">
			jQuery(document).ready(function($){
				if ($(".courses-table-list").length > 0) {
					$(".courses-table-list").tablesorter();
				}	
			});
		  </script>';
		  
else:

	echo '<div class="dt-sc-info-box">'.__('No Courses Found!', 'dt_themes').'</div>';
		  
endif;
	
?>