<?php
#Sensei Support
add_action( 'after_setup_theme', 'dt_theme_sensei_support' );
function dt_theme_sensei_support() {
	add_theme_support( 'sensei' );
}

#Before main content
add_action( 'sensei_before_main_content', 'dt_sensei_before_main_content', 10);
if( !function_exists('dt_sensei_before_main_content') ) {
	function dt_sensei_before_main_content() {
		
		if(is_singular('course')) :
			$page_layout = dttheme_option('sensei',"course-layout");
			$page_layout = !empty($page_layout) ? $page_layout : "content-full-width";
			
		elseif(is_singular('lesson')) :
			$page_layout = dttheme_option('sensei',"lesson-layout");
			$page_layout = !empty($page_layout) ? $page_layout : "content-full-width";

		elseif(is_singular('quiz')) :
			$page_layout = dttheme_option('sensei',"quiz-layout");
			$page_layout = !empty($page_layout) ? $page_layout : "content-full-width";

		elseif(taxonomy_exists('course-category') || is_post_type_archive('lesson') || is_post_type_archive('course')):
			$page_layout = dttheme_option('sensei',"course-category-layout");
			$page_layout = !empty($page_layout) ? $page_layout : "content-full-width";
		endif;
		
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
			if ( $show_left_sidebar ):
				echo '<section id="secondary-left" class="secondary-sidebar '.$sidebar_class.'">';
					get_sidebar( 'left' );
				echo '</section>';
			endif;
		endif;
		
		echo '<section id="primary" class="'.$page_layout.'">';
		
	}
}

#After main content
add_action( 'sensei_after_main_content', 'dt_sensei_after_main_content', 20);
if( !function_exists('dt_sensei_after_main_content') ) {
	function dt_sensei_after_main_content() {

		 echo "</section>";

		if(is_singular('course')) :
			$page_layout = dttheme_option('sensei',"course-layout");
			$page_layout = !empty($page_layout) ? $page_layout : "content-full-width";
			
		elseif(is_singular('lesson')) :
			$page_layout = dttheme_option('sensei',"lesson-layout");
			$page_layout = !empty($page_layout) ? $page_layout : "content-full-width";

		elseif(is_singular('quiz')) :
			$page_layout = dttheme_option('sensei',"quiz-layout");
			$page_layout = !empty($page_layout) ? $page_layout : "content-full-width";

		elseif(taxonomy_exists('course-category')):
			$page_layout = dttheme_option('sensei',"course-category-layout");
			$page_layout = !empty($page_layout) ? $page_layout : "content-full-width";
		endif;

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
			if ( $show_right_sidebar ):
				echo '<section id="secondary-right" class="secondary-sidebar '.$sidebar_class.'">';
					get_sidebar( 'right' );
				echo '</section>';
			endif;
		endif;
	

	}
}

#Remove lesson archive title
add_filter('lesson_archive_title', 'dt_lesson_archive_title', 10);
function dt_lesson_archive_title() {
	return false;
}

#Remove course category archive title
add_filter('course_category_archive_title', 'dt_course_category_archive_title', 10);
function dt_course_category_archive_title() {
	return false;
}

#Remove course category archive title
add_filter('course_archive_title', 'dt_course_archive_title', 10);
function dt_course_archive_title() {
	return false;
} ?>