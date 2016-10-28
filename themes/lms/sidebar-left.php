<?php
wp_reset_query();
global $post;

if( is_page() ):
	
	dttheme_show_sidebar('page',$post->ID, 'left');

elseif( is_singular('post') ):
	
	dttheme_show_sidebar('post',$post->ID, 'left');
			
elseif( is_singular('dt_courses')):
	
	dttheme_show_sidebar('dt_courses',$post->ID, 'left');

elseif( is_singular('dt_lessons')):
	
	dttheme_show_sidebar('dt_lessons',$post->ID, 'left');

elseif( is_singular('dt_teachers')):
	
	dttheme_show_sidebar('dt_teachers',$post->ID, 'left');

elseif( is_singular('course') ):

	$disable = dttheme_option('sensei',"disable-senseicourse-everywhere-left-sidebar-for-course-layout");
	if( is_null($disable) ):
		if(function_exists('dynamic_sidebar') && dynamic_sidebar(('senseicourse-everywhere-sidebar-left')) ): endif;
	endif;
	
elseif( is_singular('lesson') ):

	$disable = dttheme_option('sensei',"disable-senseicourse-everywhere-left-sidebar-for-lesson-layout");
	if( is_null($disable) ):
		if(function_exists('dynamic_sidebar') && dynamic_sidebar(('senseicourse-everywhere-sidebar-left')) ): endif;
	endif;

elseif( is_singular('quiz') ):

	$disable = dttheme_option('sensei',"disable-senseicourse-everywhere-left-sidebar-for-quiz-layout");
	if( is_null($disable) ):
		if(function_exists('dynamic_sidebar') && dynamic_sidebar(('senseicourse-everywhere-sidebar-left')) ): endif;
	endif;

elseif( is_singular('product') ):

	$disable = dttheme_option('woo',"disable-shop-everywhere-left-sidebar-for-product-layout");
	if( is_null($disable) ):
		if(function_exists('dynamic_sidebar') && dynamic_sidebar('shop-everywhere-sidebar-left') ): endif;
	endif;
	
elseif( is_post_type_archive('dt_portfolios') ):
	
	if(function_exists('dynamic_sidebar') && dynamic_sidebar('custom-post-portfolio-archives-sidebar-left') ): endif;

	$disable = dttheme_option('specialty',"disable-everywhere-left-sidebar-for-portfolio-archives");
	if( is_null($disable) ):
		if(function_exists('dynamic_sidebar') && dynamic_sidebar(('display-everywhere-sidebar-left')) ): endif;
	endif;
	
elseif( is_post_type_archive('dt_teachers') ):

	if(function_exists('dynamic_sidebar') && dynamic_sidebar('custom-post-teacher-archives-sidebar-left') ): endif;

	$disable = dttheme_option('specialty',"disable-everywhere-left-sidebar-for-teacher-archives");
	if( is_null($disable) ):
		if(function_exists('dynamic_sidebar') && dynamic_sidebar(('display-everywhere-sidebar-left')) ): endif;
	endif;

elseif( is_post_type_archive('dt_courses') || is_post_type_archive('dt_lessons') || is_tax('course_category') || is_tax('lesson_complexity')):

	if(function_exists('dynamic_sidebar') && dynamic_sidebar('custom-post-course-archives-sidebar-left') ): endif;
 
	$disable = dttheme_option('dt_course',"disable-everywhere-left-sidebar-for-course-archive");
	if( is_null($disable) ):
		if(function_exists('dynamic_sidebar') && dynamic_sidebar(('display-everywhere-sidebar-left')) ): endif;
	endif;
 
elseif( is_post_type_archive('lesson') || is_post_type_archive('course') || is_tax('course-category') || is_tax('lesson-tag')):

	$disable = dttheme_option('sensei',"disable-senseicourse-everywhere-left-sidebar-for-course-category-layout");
	if( is_null($disable) ):
		if(function_exists('dynamic_sidebar') && dynamic_sidebar(('senseicourse-everywhere-sidebar-left')) ): endif;
	endif;
  	
elseif( is_post_type_archive('product') ):
	
	dttheme_show_sidebar('page',get_option('woocommerce_shop_page_id'), 'left');
		
elseif( class_exists('woocommerce') && is_product_category() ):

	$disable = dttheme_option('woo',"disable-shop-everywhere-left-sidebar-for-product-category-layout");
	if( is_null($disable) ):
		if(function_exists('dynamic_sidebar') && dynamic_sidebar('shop-everywhere-sidebar-left') ): endif;
	endif;

elseif( class_exists('woocommerce') && is_product_tag() ):

	$disable = dttheme_option('woo',"disable-shop-everywhere-left-sidebar-for-product-tag-layout");
	if( is_null($disable) ):
		if(function_exists('dynamic_sidebar') && dynamic_sidebar('shop-everywhere-sidebar-left') ): endif;
	endif;
	
elseif( is_post_type_archive('tribe_events') ):
	
	$disable = dttheme_option('events',"disable-event-everywhere-left-sidebar-for-event-archive-layout");
	if( is_null($disable) ):
		if(function_exists('dynamic_sidebar') && dynamic_sidebar('events-everywhere-sidebar-left') ): endif;
	endif;

elseif( in_array('tribe-filter-live', get_body_class()) ):
	
	$disable = dttheme_option('events',"disable-event-everywhere-left-sidebar-for-event-category-layout");
	if( is_null($disable) ):
		if(function_exists('dynamic_sidebar') && dynamic_sidebar('events-everywhere-sidebar-left') ): endif;
	endif;
	
elseif(is_singular('tribe_events') || is_singular('tribe_venue') || is_singular('tribe_organizer')):

	$disable = dttheme_option('events',"disable-event-everywhere-left-sidebar-for-event-detail-layout");
	if( is_null($disable) ):
		if(function_exists('dynamic_sidebar') && dynamic_sidebar('events-everywhere-sidebar-left') ): endif;
	endif;		
		
elseif( is_archive() ):

	if(function_exists('dynamic_sidebar') && dynamic_sidebar('post-archives-sidebar-left') ): endif;

	$disable = dttheme_option('specialty',"disable-everywhere-left-sidebar-for-post-archives");
	if( is_null($disable) ):
		if(function_exists('dynamic_sidebar') && dynamic_sidebar(('display-everywhere-sidebar-left')) ): endif;
	endif;

elseif( is_search() ):

	if(function_exists('dynamic_sidebar') && dynamic_sidebar('search-sidebar-left') ): endif;

	$disable = dttheme_option('specialty',"disable-everywhere-left-sidebar-for-search");
	if( is_null($disable) ):
		if(function_exists('dynamic_sidebar') && dynamic_sidebar(('display-everywhere-sidebar-left')) ): endif;
	endif;
	
elseif( is_404() ):

	if(function_exists('dynamic_sidebar') && dynamic_sidebar('not-found-404-sidebar-left') ): endif;

	$disable = dttheme_option('specialty',"disable-everywhere-left-sidebar-for-not-found-404");
	if( is_null($disable) ):
		if(function_exists('dynamic_sidebar') && dynamic_sidebar(('display-everywhere-sidebar-left')) ): endif;
	endif;
	
else:
	if(function_exists('dynamic_sidebar') && dynamic_sidebar(('display-everywhere-sidebar-left')) ): endif;
endif;

?>