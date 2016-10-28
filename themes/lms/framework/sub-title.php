<?php

    #if Buddypress exists
    if (class_exists('BP_Core_user') && !bp_is_blog_page() ):
        dttheme_bpress_subtitle();
    #If bbPress is installed and we're on a bbPress page.
    elseif ( function_exists( 'is_bbpress' ) && is_bbpress() ):
        dttheme_bpress_subtitle();
    elseif ( is_page() ):
        global $post;
        dttheme_subtitle_section( $post->ID, 'page' );
	elseif( is_post_type_archive('tribe_events') ):
		dttheme_custom_subtitle_section( '', "events-bg");
    elseif( is_post_type_archive('product') ):
        dttheme_subtitle_section( get_option('woocommerce_shop_page_id'), 'page' );
    elseif( is_post_type_archive('dt_portfolios') ):
        $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
        $title = __("Portfolio Archives",'dt_themes');
        dttheme_custom_subtitle_section( $title, " subtitle-for-archive-term");
    elseif( is_post_type_archive('lesson') ):
		$title = __("Lesson Archives",'dt_themes');
		dttheme_custom_subtitle_section( $title, "courses-bg");
    elseif( is_post_type_archive('course') ):
		$title = __("Course Archives",'dt_themes');
		dttheme_custom_subtitle_section( $title, "courses-bg");
    elseif( is_post_type_archive('sensei_message') ):
		$title = __("Message Archives",'dt_themes');
		dttheme_custom_subtitle_section( $title, "courses-bg");
    elseif( is_post_type_archive('dt_courses') ):
		$title = __("Course Archives",'dt_themes');
		dttheme_custom_subtitle_section( $title, "courses-bg");
    elseif( is_post_type_archive('dt_lessons') ):
		$title = __("Lesson Archives",'dt_themes');
		dttheme_custom_subtitle_section( $title, "courses-bg");
    elseif( is_post_type_archive('dt_quizes') ):
		$title = __("Quizes Archives",'dt_themes');
		dttheme_custom_subtitle_section( $title, "courses-bg");
    elseif( is_post_type_archive('dt_questions') ):
		$title = __("Questions Archives",'dt_themes');
		dttheme_custom_subtitle_section( $title, "courses-bg");
    elseif( is_post_type_archive('dt_assignments') ):
		$title = __("Assignments Archives",'dt_themes');
		dttheme_custom_subtitle_section( $title, "courses-bg");
    elseif( is_post_type_archive('dt_gradings') ):
		$title = __("Gradings Archives",'dt_themes');
		dttheme_custom_subtitle_section( $title, "courses-bg");
    elseif( is_post_type_archive('dt_certificates') ):
		$title = __("Certificates Archives",'dt_themes');
		dttheme_custom_subtitle_section( $title, "courses-bg");
    elseif( is_post_type_archive('dt_teachers') ):
		$title = __("Teacher Archives",'dt_themes');
		dttheme_custom_subtitle_section( $title, 'dark-bg');
    elseif( is_single() ):
        if( is_attachment() ):
        else:
            $post_type = get_post_type();
            if( $post_type === 'post' )   {
                dttheme_subtitle_section( $post->ID, 'post' );
            }elseif(  $post_type === "dt_teachers"  ) {
                dttheme_subtitle_section( $post->ID, 'dt_teachers' );
            }elseif(  $post_type === "dt_courses"  ) {
                dttheme_subtitle_section( $post->ID, 'dt_courses' );
            }elseif(  $post_type === "dt_lessons"  ) {
                dttheme_subtitle_section( $post->ID, 'dt_lessons' );
            }elseif(  $post_type === "dt_quizes"  ) {
                dttheme_subtitle_section( $post->ID, 'dt_quizes' );
            }elseif(  $post_type === "dt_questions"  ) {
                dttheme_subtitle_section( $post->ID, 'dt_questions' );
            }elseif(  $post_type === "dt_assignments"  ) {
                dttheme_subtitle_section( $post->ID, 'dt_assignments' );
            }elseif(  $post_type === "dt_gradings"  ) {
                dttheme_subtitle_section( $post->ID, 'dt_gradings' );	
            }elseif(  $post_type === "dt_certificates"  ) {
                dttheme_subtitle_section( $post->ID, 'dt_certificates' );				
            }elseif(  $post_type === "course"  ) {
                dttheme_subtitle_section( $post->ID, 'course' );
            }elseif(  $post_type === "lesson"  ) {
                dttheme_subtitle_section( $post->ID, 'lesson' );
            }elseif(  $post_type === "quiz"  ) {
                dttheme_subtitle_section( $post->ID, 'quiz' );				
            }elseif(  $post_type === "dt_portfolios"  ) {
                dttheme_subtitle_section( $post->ID, 'dt_portfolios' );
			} elseif( in_array('events-single', get_body_class()) ) {
				dttheme_custom_subtitle_section( '', "events-bg");
			} elseif( in_array('single-tribe_venue', get_body_class()) ) {
				dttheme_custom_subtitle_section( '', "events-bg");
			} elseif( in_array('single-tribe_organizer', get_body_class()) ) {
				dttheme_custom_subtitle_section( '', "events-bg");
            } elseif( $post_type === "product" ) {
                $title = get_the_title($post->ID);
                $subtitle = __("Shop",'dt_themes');
                $icon = "fa-shopping-cart";
                dttheme_custom_subtitle_section( $title, " subtitle-for-single-product");
			}
        endif; 
    elseif( is_tax() ):
        $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
        $title = __("Term Archives",'dt_themes');
        dttheme_custom_subtitle_section( $title, " subtitle-for-archive-term");
    elseif( is_category( ) ):
        $title = __("Category Archives",'dt_themes');
        dttheme_custom_subtitle_section( $title, " subtitle-for-archive-categories");
    elseif( is_tag() ):
        $title = __("Tag Archives",'dt_themes');
        dttheme_custom_subtitle_section( $title, " subtitle-for-archive-tags");
    elseif( is_month() ):
        $title = __("Monthly Archives",'dt_themes');
        dttheme_custom_subtitle_section( $title, " subtitle-for-archive-month");
    elseif( is_year() ):
        $title = __("Yearly Archives",'dt_themes');
        dttheme_custom_subtitle_section( $title, " subtitle-for-archive-year");
    elseif(is_day() || is_time()):
    elseif( is_author() ):
        $curauth = get_user_by('slug',get_query_var('author_name')) ;
        $title  = __("Author Archives",'dt_themes');
        dttheme_custom_subtitle_section( $title, " subtitle-for-archive-author");
	elseif(in_array('events-archive', get_body_class())):
		dttheme_custom_subtitle_section( '', "events-bg");
    elseif( is_search() ):
        $title  = __("Search Result for ",'dt_themes').get_search_query();
        dttheme_custom_subtitle_section( $title, " subtitle-for-search");
    elseif( is_404() ):
        $title  = __("Lost ",'dt_themes');
        dttheme_custom_subtitle_section( $title, " subtitle-for-404");
	elseif(in_array('learner-profile', get_body_class())):
        $title  = __("Learner Profile ",'dt_themes');
        dttheme_custom_subtitle_section( $title, " learner-profile");
	elseif(in_array('course-results', get_body_class())):
        $title  = __("Course Results ",'dt_themes');
        dttheme_custom_subtitle_section( $title, " course-results");
    endif; ?>