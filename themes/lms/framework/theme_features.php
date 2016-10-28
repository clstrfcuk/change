<?php if (!function_exists('dt_theme_features')) {

	// Register Theme Features
	function dt_theme_features() {
		global $wp_version;
		
		// Add theme support for Custom Background
		$b_args = array(
			'default-color' => 'ffffff',
			'default-image' => '',
			'wp-head-callback' => '_custom_background_cb',
			'admin-head-callback' => '',
			'admin-preview-callback' => ''
		);
		add_theme_support('custom-background', $b_args);
		// END of Custom Background Feature

		// Add theme support for Custom Header
		$hargs = array( 'default-image'=>'',	'random-default'=>false,	'width'=>0,					'height'=>0,
				'flex-height'=> false,	'flex-width'=> false,		'default-text-color'=> '',	'header-text'=> false,
				'uploads'=> true,		'wp-head-callback'=> '',	'admin-head-callback'=> '',	'admin-preview-callback' => '');
				
		add_theme_support('custom-header', $hargs);
		// END of Custom Header Feature
		
		# Now Theme supports WooCommerce
		add_theme_support('woocommerce');
		
		// Add theme support for Translation
		load_theme_textdomain('dt_themes', IAMD_TD.'/languages');

		// Add theme support for Post Formats
		$formats = array(
			'status',
			'quote',
			'gallery',
			'image',
			'video',
			'audio',
			'link',
			'aside',
			'chat'
		);
		add_theme_support('post-formats', $formats);
		// END of Post Formats

		// Add theme support for custom CSS in the TinyMCE visual editor
		add_editor_style('custom-editor-style.css');

		// Add theme support for Automatic Feed Links
	
		add_theme_support('automatic-feed-links');
		// END of Automatic Feed Links

		// Add theme support for Featured Images
		add_theme_support('post-thumbnails', array(
			'post',
			'page',
			'product',
			'tribe_events',
			'dt_teachers',
			'course',
			'lesson',
			'dt_courses'
		));

		add_image_size('dt-course-widget', 110, 90, true);
		
		
		add_image_size('blog-one-column', 1170, 822, true);
		add_image_size('blog-one-column-single-sidebar', 880, 618, true);
		add_image_size('blog-one-column-both-sidebar', 590, 415, true);

		add_image_size('blogcourse-two-column', 573, 403, true);
		add_image_size('blog-two-column-single-sidebar', 429, 302, true);
		add_image_size('course-two-column-single-sidebar', 431, 303, true);
		add_image_size('blogcourse-two-column-both-sidebar', 420, 295, true);

		add_image_size('blogcourse-three-column', 420, 295, true);
		add_image_size('blogcourse-three-column-single-sidebar', 420, 295, true);
		add_image_size('blogcourse-three-column-both-sidebar', 420, 295, true);

		add_image_size('blog-thumb', 420, 295, true);
		add_image_size('blog-thumb-single-sidebar', 420, 295, true);
		add_image_size('blog-thumb-both-sidebar', 420, 295, true);


		add_image_size('portfolio-one-column', 1170, 878, true);
		add_image_size('portfolio-one-column-single-sidebar', 940, 705, true);
		add_image_size('portfolio-one-column-both-sidebar', 650, 488, true);

		add_image_size('portfolio-two-column', 633, 475, true);
		add_image_size('portfolio-two-column-single-sidebar', 489, 367, true);
		add_image_size('portfolio-two-column-both-sidebar', 420, 315, true);

		add_image_size('portfolio-three-column', 434, 326, true);
		add_image_size('portfolio-three-column-single-sidebar', 420, 315, true);
		add_image_size('portfolio-three-column-both-sidebar', 420, 315, true);

		add_image_size('portfolio-four-column', 420, 315, true);
		add_image_size('portfolio-four-column-single-sidebar', 420, 315, true);
		add_image_size('portfolio-four-column-both-sidebar', 420, 315, true);
		
		add_image_size('portfolio-widget-thumb', 70, 53, true);
		
		
		// END of Featured Images option
		
		if (version_compare($wp_version, '3.6', '>=')) :
		
			$args = array(
				'search-form',
				'comment-form',
				'comment-list'
			);
		
			add_theme_support( 'html5', $args );		
		endif;

	}
	// Hook into the 'after_setup_theme' action
	add_action('after_setup_theme', 'dt_theme_features');

}

if (!function_exists('dt_theme_navigation_menus')) {

	// Register Navigation Menus
	function dt_theme_navigation_menus() {
		$locations = array(
			'header_menu' => __('Header Menu', 'dt_themes'),
			'landingpage_menu' => __('Landing Page Menu', 'dt_themes')
		);
		register_nav_menus($locations);
	}

	// Hook into the 'init' action
	add_action('init', 'dt_theme_navigation_menus');
}