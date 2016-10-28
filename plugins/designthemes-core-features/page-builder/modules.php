<?php

global $dt_modules, $dt_animation_types, $woothemes_sensei, $dtthemes_columns;

$dt_animation_types = array("flash" => "flash", "shake" => "shake", "bounce" => "bounce", "tada" => "tada", "swing" => "swing", "wobble" => "wobble", "pulse" => "pulse", "flip" => "flip", "flipInX" => "flipInX", "flipOutX" => "flipOutX", "flipInY" => "flipInY", "flipOutY" => "flipOutY", "fadeIn" => "fadeIn", "fadeInUp" => "fadeInUp", "fadeInDown" => "fadeInDown", "fadeInLeft" => "fadeInLeft", "fadeInRight" => "fadeInRight", "fadeInUpBig" => "fadeInUpBig", "fadeInDownBig" => "fadeInDownBig", "fadeInLeftBig" => "fadeInLeftBig", "fadeInRightBig" => "fadeInRightBig", "fadeOut" => "fadeOut", "fadeOutUp" => "fadeOutUp","fadeOutDown" => "fadeOutDown", "fadeOutLeft" => "fadeOutLeft", "fadeOutRight" => "fadeOutRight", "fadeOutUpBig" => "fadeOutUpBig", "fadeOutDownBig" => "fadeOutDownBig", "fadeOutLeftBig" => "fadeOutLeftBig","fadeOutRightBig" => "fadeOutRightBig", "bounceIn" => "bounceIn", "bounceInUp" => "bounceInUp", "bounceInDown" => "bounceInDown", "bounceInLeft" => "bounceInLeft", "bounceInRight" => "bounceInRight", "bounceOut" => "bounceOut", "bounceOutUp" => "bounceOutUp", "bounceOutDown" => "bounceOutDown", "bounceOutLeft" => "bounceOutLeft", "bounceOutRight" => "bounceOutRight", "rotateIn" => "rotateIn", "rotateInUpLeft" => "rotateInUpLeft", "rotateInDownLeft" => "rotateInDownLeft", "rotateInUpRight" => "rotateInUpRight", "rotateInDownRight" => "rotateInDownRight", "rotateOut" => "rotateOut", "rotateOutUpLeft" => "rotateOutUpLeft","rotateOutDownLeft" => "rotateOutDownLeft", "rotateOutUpRight" => "rotateOutUpRight", "rotateOutDownRight" => "rotateOutDownRight", "hinge" => "hinge", "rollIn" => "rollIn", "rollOut" => "rollOut", "lightSpeedIn" => "lightSpeedIn", "lightSpeedOut" => "lightSpeedOut", "slideDown" => "slideDown", "slideUp" => "slideUp", "slideLeft" => "slideLeft", "slideRight" => "slideRight", "slideExpandUp" => "slideExpandUp", "expandUp" => "expandUp", "expandOpen" => "expandOpen", "bigEntrance" => "bigEntrance", "hatch" => "hatch", "floating" => "floating", "tossing" => "tossing", "pullUp" => "pullUp", "pullDown" => "pullDown", "stretchLeft" => "stretchLeft", "stretchRight" => "stretchRight");

$variations = array("avocado" => "avocado", "black" => "black", "blue" => "blue", "blueiris" => "blueiris", "blueturquoise" => "blueturquoise", "brown" => "brown", "burntsienna" => "burntsienna", "chillipepper" => "chillipepper", "eggplant" => "eggplant", "electricblue" => "electricblue", "graasgreen" => "graasgreen", "gray" => "gray", "green" => "green", "orange" => "orange", "palebrown" => "palebrown", "pink" => "pink", "radiantorchid" => "radiantorchid", "red" => "red", "skyblue" => "skyblue", "yellow" => "yellow");

$button_types = array('type1' => 'Type 1', 'type2' => 'Type 2');

$button_size = array('small' => 'Small', 'medium' => 'Medium', 'large' => 'Large', 'xlarge' => 'Xlarge');

$page_targets = array('_blank' => 'Blank', '_new' => 'New', '_parent' => 'Parent', '_self' => 'Self', '_top' => 'Top');

$box_types = array('titled-box' => 'Titled Box', 'error-box' => 'Error Box', 'warning-box' => 'Warning Box', 'success-box' => 'Success Box', 'info-box' => 'Info Box');

$blocquote_types = array('type1' => 'Type 1', 'type2' => 'Type 2', 'type3' => 'Type 3', 'type4' => 'Type 4');

$pullquote_types = array('pullquote1' => 'Pullquote 1', 'pullquote2' => 'Pullquote 2', 'pullquote3' => 'Pullquote 3', 'pullquote4' => 'Pullquote 4', 'pullquote5' => 'Pullquote 5', 'pullquote6' => 'Pullquote 6');

$callout_box_types = array('type1' => 'Type 1', 'type2' => 'Type 2', 'type3' => 'Type 3', 'type4' => 'Type 4', 'type5' => 'Type 5');

$pricingtable_types = $colored_icon_box_types = array('type1' => 'Type 1', 'type2' => 'Type 2');

$testimonial_types = array('type1' => 'Type 1', 'type2' => 'Type 2', 'type3' => 'Type 3');

$align = array('left' => 'Left', 'right' => 'Right', 'center' => 'Center');

$yesorno = array('yes' => 'Yes', 'no' => 'No');

$trueorfalse = array('true' => 'True', 'false' => 'False');

$tooltip_positions = array('top' => 'Top', 'right' => 'Right', 'bottom' => 'Bottom', 'left' => 'Left');

$tooltip_types = array('default' => 'Default', 'boxed' => 'Boxed');

$dropcap_types = array('Default' => 'Default', 'Circle' => 'Circle', 'Bordered Circle' => 'Bordered Circle', 'Square' => 'Square', 'Bordered Square' => 'Bordered Square');

$bacground_repeat = array('no-repeat' => 'No Repeat', 'repeat' => 'Repeat', 'repeat-x' => 'Repeat X', 'repeat-y' => 'Repeat Y');

$bacground_position = array('left top' => 'Left Top', 'left center' => 'Left Center', 'left bottom' => 'Left Bottom', 'right top' => 'Right Top', 'right center' => 'Right Center', 'right bottom' => 'Right Bottom', 'center top' => 'Center Top', 'center center' => 'Center Center', 'center bottom' => 'Center Bottom');

$lengths = array(5 => 5, 10 => 10, 15 => 15, 20 => 20, 25 => 25, 30 => 30, 35 => 35, 40 => 40, 45 => 45, 50 => 50, 55 => 55, 60 => 60, 65 => 65, 70 => 70, 75 => 75, 80 => 80, 85 => 85, 90 => 90, 95 => 95, 100 => 100);

$post_columns = array(2 => 2, 3 => 3);

$portfolio_columns = array(2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6);

$teacher_columns = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5);

$icon_box_types = array('type1' => 'Type 1', 'type2' => 'Type 2', 'type3' => 'Type 3', 'type4' => 'Type 4', 'type5' => 'Type 5', 'type6' => 'Type 6', 'type7' => 'Type 7', 'type8' => 'Type 8', 'type9' => 'Type 9', 'type10' => 'Type 10', 'type11' => 'Type 11', 'type12' => 'Type 12', 'type13' => 'Type 13', 'type14' => 'Type 14');

$progressbar_types = array('standard' => 'Standard', 'progress-striped' => 'Striped', 'progress-striped-active' => 'Striped Active');

$oltypes = array('decimal' => 'Decimal', 'decimal-leading-zero' => 'Decimal With Leading Zero', 'lower-alpha' => 'Lower Alpha', 'lower-roman' => 'Lower Roman', 'upper-alpha' => 'Upper Alpha', 'upper-roman' => 'Upper Roman');

$ultypes = array('arrow' => 'Arrow', 'rounded-arrow' => 'Rounded Arrow', 'double-arrow' => 'Double Arrow', 'heart' => 'Heart', 'trash' => 'Trash', 'star' => 'Star', 'tick' => 'Tick', 'rounded-tick' => 'Rounded Tick', 'cross' => 'Cross', 'rounded-cross' => 'Rounded Cross', 'rounded-question' => 'Rounded Question', 'rounded-info' => 'Rounded Info', 'delete' => 'Delete', 'warning' => 'Warning', 'comment' => 'Comment', 'edit' => 'Edit', 'share' => 'Share', 'plus' => 'Plus', 'rounded-plus' => 'Rounded Plus', 'minus' => 'Minus', 'rounded-minus' => 'Rounded Minus', 'asterisk' => 'Asterisk', 'cart' => 'Cart', 'folder' => 'Folder', 'folder-open' => 'Folder Open', 'desktop' => 'Desktop', 'tablet' => 'Tablet', 'mobile' => 'Mobile', 'reply' => 'Reply', 'quote' => 'Quote', 'mail' => 'Mail', 'external-link' => 'External Link', 'adjust' => 'Adjust', 'pencil' => 'Pencil', 'print' => 'Print', 'tag' => 'Tag', 'thumbs-up' => 'Thumbs Up', 'thumbs-down' => 'Thumbs Down', 'time' => 'Time', 'globe' => 'Globe', 'pushpin' => 'Pushpin', 'map-marker' => 'Map Marker', 'link' => 'Link', 'paper-clip' => 'Paper Clip', 'download' => 'Download', 'key' => 'Key', 'search' => 'Search', 'rss' => 'Rss', 'twitter' => 'Twitter', 'facebook' => 'Facebook', 'linkedin' => 'Linkedin', 'google-plus' => 'Google Plus', 'circle-tick' => 'Circle Tick');


$course_types = array('featured' => 'Featured', 'all' => 'All');

$course_post_types = array('sensei' => 'Sensei Course', 'cpt' => 'Default Course');

$course_cat = array();
$cats = get_categories('taxonomy=course_category&orderby=name&hide_empty=0');
foreach ( $cats as $cat ) :
	if(function_exists('icl_object_id')) {
		if($cat->term_id == icl_object_id($cat->term_id, 'course_category', false, ICL_LANGUAGE_CODE)){
			$course_cat[$cat->term_id] = esc_html ( $cat->name );
		}
	} else {
		$course_cat[$cat->term_id] = esc_html ( $cat->name );
	}
endforeach;

$coursecolumns  = array(1 => '1 - List', 2 => '2 - List/Grid', 3 => '3 - Grid');
$layout_options = array('grid' => 'Grid', 'list' => 'List');

$course_sensei_types = array('featured' => 'Featured', 'paid' => 'Paid', 'all' => 'All');

$slider_types = array('LayerSlider' => 'Layer Slider', 'RevolutionSlider' => 'Revolution Slider');

/*  Start of Columns Definition */

$dtthemes_columns['full_width'] = array( 
		'name' => __('1 Column', 'dt_themes'),
		'type' => 'column',
	);
$dtthemes_columns['one_half'] = array( 
		'name' => __('1/2 Column', 'dt_themes'),
		'type' => 'column',
	);
$dtthemes_columns['one_third'] = array( 
		'name' => __('1/3 Column', 'dt_themes'),
		'type' => 'column',
	);
$dtthemes_columns['one_fourth'] = array( 
		'name' => __('1/4 Column', 'dt_themes'),
		'type' => 'column',
	);
$dtthemes_columns['one_fifth'] = array( 
		'name' => __('1/5 Column', 'dt_themes'),
		'type' => 'column',
	);
$dtthemes_columns['one_sixth'] = array( 
		'name' => __('1/6 Column', 'dt_themes'),
		'type' => 'column',
	);
$dtthemes_columns['two_third'] = array( 
		'name' => __('2/3 Column', 'dt_themes'),
		'type' => 'column',
	);
$dtthemes_columns['two_fifth'] = array( 
		'name' => __('2/5 Column', 'dt_themes'),
		'type' => 'column',
	);
$dtthemes_columns['two_sixth'] = array( 
		'name' => __('2/6 Column', 'dt_themes'),
		'type' => 'column',
	);
$dtthemes_columns['three_fourth'] = array( 
		'name' => __('3/4 Column', 'dt_themes'),
		'type' => 'column',
	);
$dtthemes_columns['three_fifth'] = array( 
		'name' => __('3/5 Column', 'dt_themes'),
		'type' => 'column',
	);
$dtthemes_columns['three_sixth'] = array( 
		'name' => __('3/6 Column', 'dt_themes'),
		'type' => 'column',
	);
$dtthemes_columns['four_fifth'] = array( 
		'name' => __('4/5 Column', 'dt_themes'),
		'type' => 'column',
	);
$dtthemes_columns['four_sixth'] = array( 
		'name' => __('4/6 Column', 'dt_themes'),
		'type' => 'column',
	);
$dtthemes_columns['five_sixth'] = array( 
		'name' => __('5/6 Column', 'dt_themes'),
		'type' => 'column',
	);
$dtthemes_columns['resizable'] = array( 
		'name' => __('Resizable Column', 'dt_themes') ,
		'type' => 'column',
	);

$dtthemes_columns['fullwidth_section'] = array( 
	'name' => __('Fullwidth Section', 'dt_themes'),
	'type' => 'section',
	'options' => array(
		'backgroundcolor' => array(
			'title' => __('Background Color', 'dt_themes'),
			'type' => 'colorpicker',
			'default_value' => ''
		),
		'backgroundimage' => array(
			'title' => __('Background Image', 'dt_themes'),
			'type' => 'upload',
			'default_value' => ''
		),
		'opacity' => array(
			'title' => __('Background Opacity', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'parallax' => array(
			'title' => __('Parallax Effect', 'dt_themes'),
			'type' => 'select',
			'options' => $yesorno,
			'default_value' => array('no')
		),
		'backgroundrepeat' => array(
			'title' => __('Background Repeat', 'dt_themes'),
			'type' => 'select',
			'options' => $bacground_repeat,
			'default_value' => array('no-repeat')
		),
		'backgroundposition' => array(
			'title' => __('Background Position', 'dt_themes'),
			'type' => 'select',
			'options' => $bacground_position,
			'default_value' => array('left top')
		),
		'paddingtop' => array(
			'title' => __('Padding Top', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'paddingbottom' => array(
			'title' => __('Padding Bottom', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'textcolor' => array(
			'title' => __('Text Color', 'dt_themes'),
			'type' => 'colorpicker',
			'default_value' => ''
		),
		'disable_container' => array(
			'title' => __('Disable Container', 'dt_themes'),
			'type' => 'select',
			'options' => $trueorfalse,
			'default_value' => array('false')
		),
		'class' => array(
			'title' => __('CSS Class', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		)
	)
);

$dtthemes_columns['fullwidth_video'] = array( 
	'name' => __('Fullwidth Section Video', 'dt_themes'),
	'type' => 'section',
	'options' => array(
		'mp4' => array(
			'title' => __('MP4', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'webm' => array(
			'title' => __('WEBM', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'ogv' => array(
			'title' => __('OGV', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'poster' => array(
			'title' => __('Poster Image', 'dt_themes'),
			'type' => 'upload',
			'default_value' => ''
		),
		'backgroundimage' => array(
			'title' => __('Background Image', 'dt_themes'),
			'type' => 'upload',
			'default_value' => ''
		),
		'paddingtop' => array(
			'title' => __('Padding Top (in px)', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'paddingbottom' => array(
			'title' => __('Padding Bottom (in px)', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'class' => array(
			'title' => __('CSS Class', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		)
	)
);

/*  End of Columns definition */


/*  Start of General Modules */

$dt_modules['general']['doshortcode_anycontent'] = array(
	'name' => __('Add Any Content Here', 'dt_themes'),
	'tooltip' => 'Add any content using this module',
	'icon_class' => 'ico-anycontent',
	'options' => array(
		'acc_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true
		)
	)
);
											 
$dt_modules['general']['doshortcode_accordion_framed'] = array(
	'name' => __('Accordion Framed', 'dt_themes'),
	'tooltip' => 'Add Accordion Framed Module',
	'icon_class' => 'ico-accordion',
	'options' => array(
		'acc_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => '[dt_sc_accordion_group]<br>
							[dt_sc_toggle_framed title="Accordion 1"]<br>
							
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.<br>
							
							[/dt_sc_toggle_framed]<br>
							[dt_sc_toggle_framed title="Accordion 2"]<br>
							
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.<br>
							
							[/dt_sc_toggle_framed]<br>
							[dt_sc_toggle_framed title="Accordion 3"]<br>
							
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.<br>
							
							[/dt_sc_toggle_framed]<br>
						[/dt_sc_accordion_group]'
		)
	)
);

$dt_modules['general']['doshortcode_accordion_default'] = array(
	'name' => __('Accordion Default', 'dt_themes'),
	'tooltip' => 'Add Accordion Default Module',
	'icon_class' => 'ico-accordion',
	'options' => array(
		'acc_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => '[dt_sc_accordion_group]<br>
							[dt_sc_toggle title="Accordion 1"]<br>
							
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.<br>
							
							[/dt_sc_toggle]<br>
							[dt_sc_toggle title="Accordion 2"]<br>
							
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.<br>
							
							[/dt_sc_toggle]<br>
							[dt_sc_toggle title="Accordion 3"]<br>
							
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.<br>
							
							[/dt_sc_toggle]<br>
						[/dt_sc_accordion_group]'
		)
	)
);

$dt_modules['general']['animation'] = array(
	'name' => __('Animation', 'dt_themes'),
	'tooltip' => 'Add Animation effect for any content',
	'icon_class' => 'ico-animation',
	'options' => array(
		'effect' => array(
			'title' => __('Choose Effect', 'dt_themes'),
			'type' => 'select',
			'options' => $dt_animation_types,
			'default_value' => array('fadeInUp')
		),
		'delay' => array(
			'title' => __('Delay', 'dt_themes'),
			'type' => 'text',
			'default_value' => 400
		),
		'animation_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => 'Add any content here for animation.'
		)
	)
);


$dt_modules['general']['titled_box'] = array(
	'name' => __('Titled Box', 'dt_themes'),
	'tooltip' => 'Add titled box and different types of message box',
	'icon_class' => 'ico-box',
	'options' => array(
		'type' => array(
			'title' => __('Type', 'dt_themes'),
			'type' => 'select',
			'options' => $box_types,
			'default_value' => array('titled-box')
		),
		'title' => array(
			'title' => __('Title', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'Title Comes Here'
		),
		'icon' => array(
			'title' => __('Fontawesome Icon', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'fa-cogs'
		),
		'bgcolor' => array(
			'title' => __('Background Color', 'dt_themes'),
			'type' => 'colorpicker'
		),
		'variation' => array(
			'title' => __('Variation', 'dt_themes'),
			'type' => 'select',
			'options' => $variations,
			'default_value' => array()
		),
		'textcolor' => array(
			'title' => __('Text Color', 'dt_themes'),
			'type' => 'colorpicker'
		),
		'box_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
		)
	)
);

$dt_modules['general']['button'] = array(
	'name' => __('Button', 'dt_themes'),
	'tooltip' => 'Add Button',
	'icon_class' => 'ico-button',
	'options' => array(
		'type' => array(
			'title' => __('Type', 'dt_themes'),
			'type' => 'select',
			'options' => $button_types,
			'default_value' => array('type1')
		),
		'link' => array(
			'title' => __('Link', 'dt_themes'),
			'type' => 'text',
			'default_value' => '#'
		),
		'size' => array(
			'title' => __('Size', 'dt_themes'),
			'type' => 'select',
			'options' => $button_size,
			'default_value' => array('medium')
		),
		'bgcolor' => array(
			'title' => __('Background Color', 'dt_themes'),
			'type' => 'colorpicker'
		),
		'variation' => array(
			'title' => __('Variation', 'dt_themes'),
			'type' => 'select',
			'options' => $variations,
			'default_value' => array()
		),
		'textcolor' => array(
			'title' => __('Text Color', 'dt_themes'),
			'type' => 'colorpicker'
		),
		'target' => array(
			'title' => __('Target', 'dt_themes'),
			'type' => 'select',
			'options' => $page_targets,
			'default_value' => array('_blank')
		),
		'class' => array(
			'title' => __('Class', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'timeline_button' => array(
			'title' => __('Timeline Button', 'dt_themes'),
			'type' => 'select',
			'options' => $yesorno,
			'default_value' => array('no')
		),
		'button_content' => array(
			'title' => __('Title', 'dt_themes'),
			'type' => 'text',
			'is_content' => true,
			'default_value' => 'Sample Button'
		),
	)
);

$dt_modules['general']['blockquote'] = array(
	'name' => __('Blockquote', 'dt_themes'),
	'tooltip' => 'Add Blockquote',
	'icon_class' => 'ico-blockquote',
	'options' => array(
		'type' => array(
			'title' => __('Type', 'dt_themes'),
			'type' => 'select',
			'options' => $blocquote_types,
			'default_value' => array('type1')
		),
		'align' => array(
			'title' => __('Align', 'dt_themes'),
			'type' => 'select',
			'options' => $align,
			'default_value' => array('left')
		),
		'textcolor' => array(
			'title' => __('Text Color', 'dt_themes'),
			'type' => 'colorpicker'
		),
		'variation' => array(
			'title' => __('Variation', 'dt_themes'),
			'type' => 'select',
			'options' => $variations,
			'default_value' => array('chillipepper')
		),
		'cite' => array(
			'title' => __('Cite', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'role' => array(
			'title' => __('Role', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'blockquote_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
		)
	)
);

$dt_modules['general']['pullquote'] = array(
	'name' => __('Pullquote', 'dt_themes'),
	'tooltip' => 'Add different types of pullquotes',
	'icon_class' => 'ico-quote',
	'options' => array(
		'type' => array(
			'title' => __('Type', 'dt_themes'),
			'type' => 'select',
			'options' => $pullquote_types,
			'default_value' => array('pullquote1')
		),
		'icon' => array(
			'title' => __('Show Icon', 'dt_themes'),
			'type' => 'select',
			'options' => $yesorno,
			'default_value' => array('yes')
		),
		'align' => array(
			'title' => __('Align', 'dt_themes'),
			'type' => 'select',
			'options' => $align,
			'default_value' => array('left')
		),
		'textcolor' => array(
			'title' => __('Text Color', 'dt_themes'),
			'type' => 'colorpicker',
			'default_value' => ''
		),
		'cite' => array(
			'title' => __('Cite Name', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'Dolor sit amet'
		),
		'pq_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
		)
	)
);

$dt_modules['general']['tooltip'] = array(
	'name' => __('Tooltip', 'dt_themes'),
	'tooltip' => 'Add tooltip with different positions',
	'icon_class' => 'ico-pullquote',
	'options' => array(
		'type' => array(
			'title' => __('Type', 'dt_themes'),
			'type' => 'select',
			'options' => $tooltip_types,
			'default_value' => array('default')
		),
		'position' => array(
			'title' => __('Position', 'dt_themes'),
			'type' => 'select',
			'options' => $tooltip_positions,
			'default_value' => array('top')
		),
		'bgcolor' => array(
			'title' => __('Background Color', 'dt_themes'),
			'type' => 'colorpicker'
		),
		'textcolor' => array(
			'title' => __('Text Color', 'dt_themes'),
			'type' => 'colorpicker'
		),
		'tooltip' => array(
			'title' => __('Tooltip Text', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'Consectetur adipisicing elit'
		),
		'href' => array(
			'title' => __('Link', 'dt_themes'),
			'type' => 'text',
			'default_value' => '#'
		),
		'target' => array(
			'title' => __('Target', 'dt_themes'),
			'type' => 'select',
			'options' => $page_targets,
			'default_value' => array('_blank')
		),
		'tp_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'text',
			'is_content' => true,
			'default_value' => 'Lorem ipsum'
		)
	)
);

$dt_modules['general']['callout_box'] = array(
	'name' => __('Callout Box', 'dt_themes'),
	'tooltip' => 'Add the callout box here',
	'icon_class' => 'ico-callout',
	'options' => array(
		'type' => array(
			'title' => __('Type', 'dt_themes'),
			'type' => 'select',
			'options' => $callout_box_types,
			'default_value' => array('type1')
		),
		'link' => array(
			'title' => __('Link', 'dt_themes'),
			'type' => 'text',
			'default_value' => '#'
		),
		'button_text' => array(
			'title' => __('Button Label', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'Purchase Now'
		),
		'icon' => array(
			'title' => __('Icon', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'fa-home'
		),
		'target' => array(
			'title' => __('Target', 'dt_themes'),
			'type' => 'select',
			'options' => $page_targets,
			'default_value' => array('_blank')
		),
		'class' => array(
			'title' => __('Class', 'dt_themes'),
			'type' => 'text'
		),
		'cb_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => "<h4>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempora.</h4>"
		)
	)
);

$dt_modules['general']['fancy_ol'] = array(
	'name' => __('Ordered Lists', 'dt_themes'),
	'tooltip' => 'Add items in ordered list',
	'icon_class' => 'ico-orderedlists',
	'options' => array(
		'style' => array(
			'title' => __('Style', 'dt_themes'),
			'type' => 'select',
			'options' => $oltypes,
			'default_value' => array('decimal')
		),
		'variation' => array(
			'title' => __('Variation', 'dt_themes'),
			'type' => 'select',
			'options' => $variations,
			'default_value' => array()
		),
		'class' => array(
			'title' => __('Class', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'ol_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => '<ol>
							<li>Lorem ipsum dolor sit</li>
							<li>Praesent convallis nibh</li>
							<li>Nullam ac sapien sit</li>
							<li>Phasellus auctor augue</li>
						</ol>'
		)
	)
);

$dt_modules['general']['fancy_ul'] = array(
	'name' => __('Unordered Lists', 'dt_themes'),
	'tooltip' => 'Add items in unordered lists',
	'icon_class' => 'ico-unorderedlists',
	'options' => array(
		'style' => array(
			'title' => __('Style', 'dt_themes'),
			'type' => 'select',
			'options' => $ultypes,
			'default_value' => array('arrow')
		),
		'variation' => array(
			'title' => __('Variation', 'dt_themes'),
			'type' => 'select',
			'options' => $variations,
			'default_value' => array()
		),
		'class' => array(
			'title' => __('Class', 'dt_themes'),
			'type' => 'text'
		),
		'ul_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => '<ul>
							<li>Lorem ipsum dolor sit</li>
							<li>Praesent convallis nibh</li>
							<li>Nullam ac sapien sit</li>
							<li>Phasellus auctor augue</li>
						</ul>'
		)
	)
);

$dt_modules['general']['pricing_table'] = array(
	'name' => __('Pricing Table', 'dt_themes'),
	'tooltip' => 'Add pricing table',
	'icon_class' => 'ico-pricingtable',
	'options' => array(
		'type' => array(
			'title' => __('Type', 'dt_themes'),
			'type' => 'select',
			'options' => $pricingtable_types,
			'default_value' => array('type1')
		),
		'pt_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => "[dt_sc_one_third first]
								[dt_sc_pricing_table_item heading='Heading' button_text='Buy Now' button_link='#' price='$15' per='month' class='' selected]
								<ul>
									<li>Text</li>
									<li>Text</li>
									<li>Text</li>
								</ul>
								[/dt_sc_pricing_table_item]
								[/dt_sc_one_third]
								[dt_sc_one_third]
								[dt_sc_pricing_table_item heading='Heading' button_text='Buy Now' button_link='#' price='$15' per='month' class='' ]
								<ul>
									<li>Text</li>
									<li>Text</li>
									<li>Text</li>
								</ul>
								[/dt_sc_pricing_table_item]
								[/dt_sc_one_third]
								[dt_sc_one_third]
								[dt_sc_pricing_table_item heading='Heading' button_text='Buy Now' button_link='#' price='$15' per='month' class='' ]
								<ul>
									<li>Text</li>
									<li>Text</li>
									<li>Text</li>
								</ul>
								[/dt_sc_pricing_table_item]
								[/dt_sc_one_third]"
		)
	)
);

$dt_modules['general']['progressbar'] = array(
	'name' => __('Progress Bar', 'dt_themes'),
	'tooltip' => 'Add different types of progres bar',
	'icon_class' => 'ico-progressbar',
	'options' => array(
		'type' => array(
			'title' => __('Type', 'dt_themes'),
			'type' => 'select',
			'options' => $progressbar_types,
			'default_value' => array('standard')
		),
		'color' => array(
			'title' => __('Color', 'dt_themes'),
			'type' => 'colorpicker',
			'default_value' => ''
		),
		'value' => array(
			'title' => __('Value', 'dt_themes'),
			'type' => 'text',
			'default_value' => 55
		),
		'content' => array(
			'title' => __('Text', 'dt_themes'),
			'type' => 'text',
			'is_content' => true,
			'default_value' => 'Consectetur'
		)
	)
);

$dt_modules['general']['tabs_horizontal'] = array(
	'name' => __('Tabs - Horizontal', 'dt_themes'),
	'tooltip' => 'Add horizontal tabs',
	'icon_class' => 'ico-tabs',
	'options' => array(
		'th_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => '[dt_sc_tab title="Tab 1"]<br>
							
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.<br>
							
							[/dt_sc_tab]<br>
							[dt_sc_tab title="Tab 2"]<br>
							
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.<br>
							
							[/dt_sc_tab]<br>
							[dt_sc_tab title="Tab 3"]<br>
							
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.<br>
							
							[/dt_sc_tab]'
		)
	)
);

$dt_modules['general']['tabs_vertical'] = array(
	'name' => __('Tabs Vertical', 'dt_themes'),
	'tooltip' => 'Add vertical tabs',
	'icon_class' => 'ico-tabs',
	'options' => array(
		'tv_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => '[dt_sc_tab title="Tab 1"]<br>
							
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.<br>
							
							[/dt_sc_tab]<br>
							[dt_sc_tab title="Tab 2"]<br>
							
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.<br>
							
							[/dt_sc_tab]<br>
							[dt_sc_tab title="Tab 3"]<br>
							
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.<br>
							
							[/dt_sc_tab]'
		)
	)
);

$dt_modules['general']['doshortcode_toggledefault'] = array(
	'name' => __('Toggle Default', 'dt_themes'),
	'tooltip' => 'Add default toggles',
	'icon_class' => 'ico-toggle',
	'options' => array(
		'td_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => '[dt_sc_toggle title="Toggle 1"]<br>
							
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.<br>
							
							[/dt_sc_toggle]<br>
							[dt_sc_toggle title="Toggle 2"]<br>
							
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.<br>
							
							[/dt_sc_toggle]<br>
							[dt_sc_toggle title="Toggle 3"]<br>
							
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.<br>
							
						[/dt_sc_toggle]'
		)
	)
);

$dt_modules['general']['doshortcode_toggleframed'] = array(
	'name' => __('Toggle Framed', 'dt_themes'),
	'tooltip' => 'Add framed toggles',
	'icon_class' => 'ico-toggle',
	'options' => array(
		'tf_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => '[dt_sc_toggle_framed title="Toggle 1"]<br>
							
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.<br>
							
							[/dt_sc_toggle_framed]<br>
							[dt_sc_toggle_framed title="Toggle 2"]<br>
							
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.<br>
							
							[/dt_sc_toggle_framed]<br>
							[dt_sc_toggle_framed title="Toggle 3"]<br>
							
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.<br>
							
						[/dt_sc_toggle_framed]'
		)
	)
);

$dt_modules['general']['dropcap'] = array(
	'name' => __('Dropcap', 'dt_themes'),
	'tooltip' => __('Use this module to add dropcap', 'dt_themes'),
	'icon_class' => 'ico-dropcap',
	'options' => array(
		'type' => array(
			'title' => __('Type', 'dt_themes'),
			'type' => 'select',
			'options' => $dropcap_types,
			'default_value' => array('Default')
		),
		'variation' => array(
			'title' => __('Variation', 'dt_themes'),
			'type' => 'select',
			'options' => $variations,
			'default_value' => array()
		),
		'bgcolor' => array(
			'title' => __('Background Color', 'dt_themes'),
			'type' => 'colorpicker',
			'default_value' => '#808080'
		),
		'textcolor' => array(
			'title' => __('Text Color', 'dt_themes'),
			'type' => 'colorpicker',
			'default_value' => '#4bbcd7'
		),
		'content' => array(
			'title' => __('Text', 'dt_themes'),
			'type' => 'text',
			'is_content' => true,
			'default_value' => 'A'
		)
	)
);

$dt_modules['general']['doshortcode_team'] = array(
	'name' => __('Team', 'dt_themes'),
	'tooltip' => __('Use this module to add member', 'dt_themes'),
	'icon_class' => 'ico-team',
	'options' => array(
		'tf_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => '[dt_sc_team name="DesignThemes" role="Chief Programmer" image="http://placehold.it/500" twitter="#" facebook="#" google="#" linkedin="#"]
								<p>Saleem naijar kaasram eerie can be disbursed in the wofl like of a fox that is her thing smaoasa lase lemedds laasd pamade eleifend sapien.</p>
								[/dt_sc_team]'
		)
	)
);

$dt_modules['general']['testimonial'] = array(
	'name' => __('Testimonial', 'dt_themes'),
	'tooltip' => __('Use this module to add testimonial', 'dt_themes'),
	'icon_class' => 'ico-testimonial',
	'options' => array(
		'name' => array(
			'title' => __('Name', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'Name Comes Here'
		),
		'role' => array(
			'title' => __('Role', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'Role Comes Here'
		),	
		'type' => array(
			'title' => __('Type', 'dt_themes'),
			'type' => 'select',
			'options' => $testimonial_types,
			'default_value' => array('type2')
		),
		'enable_rating' => array(
			'title' => __('Enable Rating', 'dt_themes'),
			'type' => 'select',
			'options' => $trueorfalse,
			'default_value' => array()
		),
		'class' => array(
			'title' => __('Class', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'image' => array(
			'title' => __('Image', 'dt_themes'),
			'type' => 'upload',
			'default_value' => 'http://placehold.it/300'
		),
		'tm_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => 'Saleem naijar kaasram eerie can be disbursed in the wofl like of a fox that is her thing smaoasa lase lemedds laasd pamade eleifend sapien.'
		)
	)
);

$dt_modules['general']['testimonial_carousel'] = array(
	'name' => __('Testimonial Carousel', 'dt_themes'),
	'tooltip' => __('Use this module to add testimonial carousel', 'dt_themes'),
	'icon_class' => 'ico-testimonial',
	'options' => array(
		'tmc_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => '<ul>
									<li>[dt_sc_testimonial name="John Doe" role="Cambridge Telcecom" type="type2" class=""]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.[/dt_sc_testimonial]</li>
									<li>[dt_sc_testimonial name="John Doe" role="Cambridge Telcecom" type="type2" class=""]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.[/dt_sc_testimonial]</li>
									<li>[dt_sc_testimonial name="John Doe" role="Cambridge Telcecom" type="type2" class=""]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi hendrerit elit turpis, a porttitor tellus sollicitudin at. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.[/dt_sc_testimonial]</li>
								</ul>'
		)
	)
);

$dt_modules['general']['clients_carousel'] = array(
	'name' => __('Clients Carousel', 'dt_themes'),
	'tooltip' => __('Use this module to add clients carousel', 'dt_themes'),
	'icon_class' => 'ico-clients',
	'options' => array(
		'cc_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => '<ul>
									<li><a href="#"><img title="Client 1" src="http://placehold.it/163x116" alt="Client 1" /></a></li>
									<li><a href="#"><img title="Client 2" src="http://placehold.it/163x116" alt="Client 2" /></a></li>
									<li><a href="#"><img title="Client 3" src="http://placehold.it/163x116" alt="Client 3" /></a></li>
									<li><a href="#"><img title="Client 4" src="http://placehold.it/163x116" alt="Client 4" /></a></li>
									<li><a href="#"><img title="Client 5" src="http://placehold.it/163x116" alt="Client 5" /></a></li>
								</ul>'
		)
	)
);

$dt_modules['general']['icon_box'] = array(
	'name' => __('Icon Box', 'dt_themes'),
	'tooltip' => __('Use this module to add icon box', 'dt_themes'),
	'icon_class' => 'ico-iconbox',
	'options' => array(
		'type' => array(
			'title' => __('Types', 'dt_themes'),
			'type' => 'select',
			'options' => $icon_box_types,
			'default_value' => array('type1')
		),
		'fontawesome_icon' => array(
			'title' => __('Fontawesome Icon', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'trophy'
		),
		'stroke_icon' => array(
			'title' => __('Stroke Icon', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'custom_icon' => array(
			'title' => __('Custom Icon', 'dt_themes'),
			'type' => 'upload',
			'default_value' => ''
		),
		'title' => array(
			'title' => __('Title', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'Title Comes Here'
		),
		'link' => array(
			'title' => __('Link', 'dt_themes'),
			'type' => 'text',
			'default_value' => '#'
		),
		'custom_bgcolor' => array(
			'title' => __('Icon Background Color (for type13 only)', 'dt_themes'),
			'type' => 'colorpicker',
			'default_value' => '#27ae60'
		),
		'ib_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras cursus sollicitudin nunc nec rhoncus.'
		)
	)
);

/*  End of General Modules */


/*  Start of Unique Modules */

$dt_modules['unique']['courses'] = array(
	'name' => __('Courses', 'dt_themes'),
	'tooltip' => __('Use this module to add courses', 'dt_themes'),
	'icon_class' => 'ico-courses',
	'options' => array(
		'course_type' => array(
			'title' => __('Course Type', 'dt_themes'),
			'type' => 'select',
			'options' => $course_types,
			'default_value' => array('all')
		),
		'limit' => array(
			'title' => __('Limit', 'dt_themes'),
			'type' => 'text',
			'default_value' => '6'
		),
		'carousel' => array(
			'title' => __('Carousel', 'dt_themes'),
			'type' => 'select',
			'options' => $trueorfalse,
			'default_value' => array('false')
		),
		'columns' => array(
			'title' => __('Columns', 'dt_themes'),
			'type' => 'select',
			'options' => $coursecolumns,
			'default_value' => array(2)
		),
		'layout_view' => array(
			'title' => __('Layout View', 'dt_themes'),
			'type' => 'select',
			'options' => $layout_options,
			'default_value' => array('grid')
		),
		'categories' => array(
			'title' => __('Categories', 'dt_themes'),
			'type' => 'select',
			'options' => $course_cat,
			'multiple' => true,
			'default_value' => array()
		),
	)
);

$dt_modules['unique']['courses_search'] = array(
	'name' => __('Courses Search', 'dt_themes'),
	'tooltip' => __('Use this module to add courses search box', 'dt_themes'),
	'icon_class' => 'ico-courses-search',
	'options' => array(
		'title' => array(
			'title' => __('Title', 'dt_themes'),
			'type' => 'text',
			'default_value' => __('Search Courses', 'dt_themes'),
		),		
		'post_per_page' => array(
			'title' => __('Posts Per Page', 'dt_themes'),
			'type' => 'text',
			'default_value' => '5'
		),
	)
);

if(function_exists('dttheme_is_plugin_active') && dttheme_is_plugin_active('woothemes-sensei/woothemes-sensei.php')) {
	
	$dt_modules['unique']['courses_sensei'] = array(
		'name' => __('Courses - Sensei', 'dt_themes'),
		'tooltip' => __('Use this module to add courses', 'dt_themes'),
		'icon_class' => 'ico-courses-sensei',
		'options' => array(
			'course_type' => array(
				'title' => __('Course Type', 'dt_themes'),
				'type' => 'select',
				'options' => $course_sensei_types,
				'default_value' => array()
			),
			'limit' => array(
				'title' => __('Limit', 'dt_themes'),
				'type' => 'text',
				'default_value' => '4'
			),
			'carousel' => array(
				'title' => __('Carousel', 'dt_themes'),
				'type' => 'select',
				'options' => $trueorfalse,
				'default_value' => array('false')
			),
			'categories' => array(
				'title' => __('Categories', 'dt_themes'),
				'type' => 'text',
				'default_value' => ''
			),
		)
	);

}

$dt_modules['unique']['counter'] = array(
	'name' => __('Counter', 'dt_themes'),
	'tooltip' => __('Use this module to add counter', 'dt_themes'),
	'icon_class' => 'ico-counter',
	'options' => array(
		'title' => array(
			'title' => __('Title', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'Title Comes Here'
		),
		'number' => array(
			'title' => __('Number', 'dt_themes'),
			'type' => 'text',
			'default_value' => '1000'
		),
	)
);

$dt_modules['unique']['infographic_bar'] = array(
	'name' => __('Infographic Bar', 'dt_themes'),
	'tooltip' => 'Add infographic bar',
	'icon_class' => 'ico-infographicbar',
	'options' => array(
		'icon' => array(
			'title' => __('Icon', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'fa-female'
		),
		'icon_size' => array(
			'title' => __('Icon size', 'dt_themes'),
			'type' => 'text',
			'default_value' => '50'
		),
		'value' => array(
			'title' => __('Percentage', 'dt_themes'),
			'type' => 'text',
			'default_value' => '75'
		),
		'type' => array(
			'title' => __('Type', 'dt_themes'),
			'type' => 'select',
			'options' => $progressbar_types,
			'default_value' => array('standard')
		),
		'color' => array(
			'title' => __('Color', 'dt_themes'),
			'type' => 'colorpicker',
			'default_value' => ''
		),
		'content' => array(
			'title' => __('Text', 'dt_themes'),
			'type' => 'text',
			'is_content' => true,
			'default_value' => 'Laasd pamade eleifend la sapien. Vestibulum purus quam.'
		)
	)
);

$dt_modules['unique']['donutchart_small'] = array(
	'name' => __('Donut Chart - Small', 'dt_themes'),
	'tooltip' => __('Use this module to add small donutchart', 'dt_themes'),
	'icon_class' => 'ico-donutchart',
	'options' => array(
		'title' => array(
			'title' => __('Title', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'Lorem'
		),
		'bgcolor' => array(
			'title' => __('Background Color', 'dt_themes'),
			'type' => 'colorpicker',
			'default_value' => '#808080'
		),
		'fgcolor' => array(
			'title' => __('Foreground Color', 'dt_themes'),
			'type' => 'colorpicker',
			'default_value' => '#4bbcd7'
		),
		'percent' => array(
			'title' => __('Percent', 'dt_themes'),
			'type' => 'text',
			'default_value' => '40'
		),
	)
);

$dt_modules['unique']['donutchart_medium'] = array(
	'name' => __('Donut Chart - Medium', 'dt_themes'),
	'tooltip' => __('Use this module to add medium donutchart', 'dt_themes'),
	'icon_class' => 'ico-donutchart',
	'options' => array(
		'title' => array(
			'title' => __('Title', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'Lorem'
		),
		'bgcolor' => array(
			'title' => __('Background Color', 'dt_themes'),
			'type' => 'colorpicker',
			'default_value' => '#808080'
		),
		'fgcolor' => array(
			'title' => __('Foreground Color', 'dt_themes'),
			'type' => 'colorpicker',
			'default_value' => '#4bbcd7'
		),
		'percent' => array(
			'title' => __('Percent', 'dt_themes'),
			'type' => 'text',
			'default_value' => '40'
		),
	)
);

$dt_modules['unique']['donutchart_large'] = array(
	'name' => __('Donut Chart - Large', 'dt_themes'),
	'tooltip' => __('Use this module to add large donutchart', 'dt_themes'),
	'icon_class' => 'ico-donutchart',
	'options' => array(
		'title' => array(
			'title' => __('Title', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'Lorem'
		),
		'bgcolor' => array(
			'title' => __('Background Color', 'dt_themes'),
			'type' => 'colorpicker',
			'default_value' => '#808080'
		),
		'fgcolor' => array(
			'title' => __('Foreground Color', 'dt_themes'),
			'type' => 'colorpicker',
			'default_value' => '#4bbcd7'
		),
		'percent' => array(
			'title' => __('Percent', 'dt_themes'),
			'type' => 'text',
			'default_value' => '40'
		),
	)
);

if(function_exists('dttheme_is_plugin_active') && dttheme_is_plugin_active('the-events-calendar/the-events-calendar.php')) {
	
	$dt_modules['unique']['events'] = array(
		'name' => __('Events', 'dt_themes'),
		'tooltip' => __('Use this module to add events list or carousel', 'dt_themes'),
		'icon_class' => 'ico-events',
		'options' => array(
			'limit' => array(
				'title' => __('Limit', 'dt_themes'),
				'type' => 'text',
				'default_value' => '4'
			),
			'carousel' => array(
				'title' => __('Carousel', 'dt_themes'),
				'type' => 'select',
				'options' => $trueorfalse,
				'default_value' => array('false')
			),
			'category_ids' => array(
				'title' => esc_html__('Category Ids (separated by commas)', 'dt_plugins'),
				'type' => 'text',
				'default_value' => ''
			),
		)
	);

}

$dt_modules['unique']['post'] = array(
	'name' => __('Single Post', 'dt_themes'),
	'tooltip' => __('Use this module to display any single post', 'dt_themes'),
	'icon_class' => 'ico-singlepost',
	'options' => array(
		'id' => array(
			'title' => __('Post Id', 'dt_themes'),
			'type' => 'text',
			'default_value' => '1'
		),
		'read_more_text' => array(
			'title' => __('Read More Text', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'Read More'
		),
		'excerpt_length' => array(
			'title' => __('Excerpt Length', 'dt_themes'),
			'type' => 'text',
			'default_value' => '10'
		),
	)
);

$dt_modules['unique']['recent_post'] = array(
	'name' => __('Recent Posts', 'dt_themes'),
	'tooltip' => __('Use this module to display recent posts', 'dt_themes'),
	'icon_class' => 'ico-recentposts',
	'options' => array(
		'columns' => array(
			'title' => __('Columns', 'dt_themes'),
			'type' => 'select',
			'options' => $post_columns,
			'default_value' => array(3)
		),
		'count' => array(
			'title' => __('Limit', 'dt_themes'),
			'type' => 'text',
			'default_value' => '3'
		),
		'read_more_text' => array(
			'title' => __('Read More Text', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'Read More'
		),
		'excerpt_length' => array(
			'title' => __('Excerpt Length', 'dt_themes'),
			'type' => 'text',
			'default_value' => '10'
		),
	)
);

$dt_modules['unique']['timeline_section'] = array(
	'name' => __('Timeline', 'dt_themes'),
	'tooltip' => 'Add timeline process',
	'icon_class' => 'ico-timeline',
	'options' => array(
		'ts_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => '[dt_sc_timeline align="left" class=""]
								[dt_sc_timeline_item fontawesome_icon="home" custom_icon="" link="#" title="Title Comes Here" subtitle="Subtitle Comes Here"]
								Nemo enim ipsam voluptatem quia voluptas sit atur aut odit aut fugit, sed quia consequuntur magni res.
								[/dt_sc_timeline_item]
								[/dt_sc_timeline]
								[dt_sc_timeline align="right" class=""]
								[dt_sc_timeline_item fontawesome_icon="eye" custom_icon="" link="#" title="Title Comes Here" subtitle="Subtitle Comes Here"]
								Nemo enim ipsam voluptatem quia voluptas sit atur aut odit aut fugit, sed quia consequuntur magni res.
								[/dt_sc_timeline_item]
								[/dt_sc_timeline]
								[dt_sc_timeline align="left" class=""]
								[dt_sc_timeline_item fontawesome_icon="cogs" custom_icon="" link="#" title="Title Comes Here" subtitle="Subtitle Comes Here"]
								Nemo enim ipsam voluptatem quia voluptas sit atur aut odit aut fugit, sed quia consequuntur magni res.
								[/dt_sc_timeline_item]
								[/dt_sc_timeline]
								[dt_sc_timeline align="right" class=""]
								[dt_sc_timeline_item fontawesome_icon="institution" custom_icon="" link="#" title="Title Comes Here" subtitle="Subtitle Comes Here"]
								Nemo enim ipsam voluptatem quia voluptas sit atur aut odit aut fugit, sed quia consequuntur magni res.
								[/dt_sc_timeline_item]
								[/dt_sc_timeline]'
		)
	)
);

$dt_modules['unique']['portfolio_item'] = array(
	'name' => __('Portfolio Item', 'dt_themes'),
	'tooltip' => __('Use this module to add individual portfolio items', 'dt_themes'),
	'icon_class' => 'ico-portfolio-item',
	'options' => array(
		'id' => array(
			'title' => __('Item Id', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
	)
);

$dt_modules['unique']['portfolios'] = array(
	'name' => __('Portfolio Items From Category', 'dt_themes'),
	'tooltip' => __('Use this module to add portfolio items from category', 'dt_themes'),
	'icon_class' => 'ico-portfolio-item',
	'options' => array(
		'category_id' => array(
			'title' => __('Category Id', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'column' => array(
			'title' => __('Columns', 'dt_themes'),
			'type' => 'select',
			'options' => $portfolio_columns,
			'default_value' => array(3)
		),
		'count' => array(
			'title' => 'Count',
			'type' => 'text',
			'default_value' => ''
		),
	)
);

$dt_modules['unique']['icon_box_colored'] = array(
	'name' => __('Icon Box Colored', 'dt_themes'),
	'tooltip' => 'Add the colored icon box',
	'icon_class' => 'ico-iconbox-colored',
	'options' => array(
		'type' => array(
			'title' => __('Type', 'dt_themes'),
			'type' => 'select',
			'options' => $colored_icon_box_types,
			'default_value' => array('type1')
		),
		'fontawesome_icon' => array(
			'title' => __('Fontawesome Icons', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'home'
		),
		'custom_icon' => array(
			'title' => __('Custom Icons', 'dt_themes'),
			'type' => 'upload',
			'default_value' => ''
		),
		'title' => array(
			'title' => __('Title', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'Sample Title'
		),
		'bgcolor' => array(
			'title' => __('Background Color', 'dt_themes'),
			'type' => 'colorpicker',
			'default_value' => '#333334'
		),
		'ibc_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempora."
		)
	)
);

$dt_modules['unique']['teacher'] = array(
	'name' => __('Teacher', 'dt_themes'),
	'tooltip' => __('Use this module to add teacher', 'dt_themes'),
	'icon_class' => 'ico-teacher',
	'options' => array(
		'columns' => array(
			'title' => __('Column', 'dt_themes'),
			'type' => 'select',
			'options' => $teacher_columns,
			'default_value' => array(3)
		),
		'limit' => array(
			'title' => __('Limit', 'dt_themes'),
			'type' => 'text',
			'default_value' => 4
		),
	)
);

$dt_modules['unique']['subscription_form'] = array(
	'name' => __('Plan A Visit Form', 'dt_themes'),
	'tooltip' => __('Use this module to add plan a visit form', 'dt_themes'),
	'icon_class' => 'ico-planavisit',
	'options' => array(
		'image_url' => array(
			'title' => __('Image', 'dt_themes'),
			'type' => 'upload',
			'default_value' => 'http://placehold.it/780x502'
		),
		'slider' => array(
			'title' => __('Slider Type', 'dt_themes'),
			'type' => 'select',
			'options' => $slider_types,
			'default_value' => array()
		),
		'slider_id' => array(
			'title' => __('Slider Id', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'title' => array(
			'title' => __('Title', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'Plan a Visit'
		),
		'submit_text' => array(
			'title' => __('Submit Text', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'Submit'
		),
		'success_msg' => array(
			'title' => __('Success Message', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'Thanks for subscribing, we will contact you soon.'
		),
		'error_msg' => array(
			'title' => __('Error Message', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'Mail not sent, please try again Later.'
		),
		'subject' => array(
			'title' => __('Subject', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'Subscription'
		),
		'admin_email' => array(
			'title' => __('Admin(Receiver) Email', 'dt_themes'),
			'type' => 'text',
			'default_value' => get_bloginfo('admin_email')
		),
		'enable_planavisit' => array(
			'title' => __('Enable Plan a Visit', 'dt_themes'),
			'type' => 'select',
			'options' => $trueorfalse,
			'default_value' => array('true')
		),
		'contact_label' => array(
			'title' => __('Contact Label', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'Inquiries'
		),
		'contact_number' => array(
			'title' => __('Contact Number', 'dt_themes'),
			'type' => 'text',
			'default_value' => '0123456789'
		),
		'course_type' => array(
			'title' => __('Course Type', 'dt_themes'),
			'type' => 'select',
			'options' => $course_post_types,
			'default_value' => array()
		),
	)
);

$dt_modules['unique']['doshortcode_subscribed_courses'] = array(
	'name' => __('Subscribed Courses', 'dt_themes'),
	'tooltip' => __('Use this module to list users subscribed courses along with his visit count', 'dt_themes'),
	'icon_class' => 'ico-subscribed-courses',
	'options' => array(
		'sc_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'text',
			'is_content' => true,
			'default_value' => '[dt_sc_subscribed_courses /]'
		),
	)
);

$dt_modules['unique']['newsletter_section'] = array(
	'name' => __('Newsletter Section', 'dt_themes'),
	'tooltip' => __('Use this module to add newsletter section', 'dt_themes'),
	'icon_class' => 'ico-newsletter-section',
	'options' => array(
		'title' => array(
			'title' => __('Title', 'dt_themes'),
			'type' => 'text',
			'default_value' => __('Get in touch with us', 'dt_themes')
		),
		'nl_content' => array(
			'title' => __('Content', 'dt_themes'),
			'type' => 'wp_editor',
			'is_content' => true,
			'default_value' => ''
		),
	)
);

$dt_modules['unique']['slider_search'] = array(
	'name' => __('Slider Search', 'dt_themes'),
	'tooltip' => __('Use this module to add search box along with image or slider', 'dt_themes'),
	'icon_class' => 'ico-slider-search',
	'options' => array(
		'title' => array(
			'title' => __('Title', 'dt_themes'),
			'type' => 'text',
			'default_value' => __('We have the largest collection of courses', 'dt_themes')
		),
		'button_title' => array(
			'title' => __('Button Title', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'button_link' => array(
			'title' => __('Button Link', 'dt_themes'),
			'type' => 'text',
			'default_value' => '#'
		),
		'disable_search' => array(
			'title' => __('Disable Search', 'dt_themes'),
			'type' => 'select',
			'options' => $trueorfalse,
			'default_value' => array('false')
		),
	)
);

/*  End of Unique Modules */

/*  Start of Others Modules */

$dt_modules['others']['h1'] = array(
	'name' => __('Heading 1', 'dt_themes'),
	'tooltip' => __('Use this module to add heading 1', 'dt_themes'),
	'icon_class' => 'ico-headings',
	'options' => array(
		'class' => array(
			'title' => __('Class', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'content' => array(
			'title' => __('Title', 'dt_themes'),
			'type' => 'text',
			'is_content' => true,
			'default_value' => 'Title Comes Here'
		),
	)
);

$dt_modules['others']['h2'] = array(
	'name' => __('Heading 2', 'dt_themes'),
	'tooltip' => __('Use this module to add heading 2', 'dt_themes'),
	'icon_class' => 'ico-headings',
	'options' => array(
		'class' => array(
			'title' => __('Class', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'content' => array(
			'title' => __('Title', 'dt_themes'),
			'type' => 'text',
			'is_content' => true,
			'default_value' => 'Title Comes Here'
		),
	)
);

$dt_modules['others']['h3'] = array(
	'name' => __('Heading 3', 'dt_themes'),
	'tooltip' => __('Use this module to add heading 3', 'dt_themes'),
	'icon_class' => 'ico-headings',
	'options' => array(
		'class' => array(
			'title' => __('Class', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'content' => array(
			'title' => __('Title', 'dt_themes'),
			'type' => 'text',
			'is_content' => true,
			'default_value' => 'Title Comes Here'
		),
	)
);

$dt_modules['others']['h4'] = array(
	'name' => __('Heading 4', 'dt_themes'),
	'tooltip' => __('Use this module to add heading 4', 'dt_themes'),
	'icon_class' => 'ico-headings',
	'options' => array(
		'class' => array(
			'title' => __('Class', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'content' => array(
			'title' => __('Title', 'dt_themes'),
			'type' => 'text',
			'is_content' => true,
			'default_value' => 'Title Comes Here'
		),
	)
);

$dt_modules['others']['h5'] = array(
	'name' => __('Heading 5', 'dt_themes'),
	'tooltip' => __('Use this module to add heading 5', 'dt_themes'),
	'icon_class' => 'ico-headings',
	'options' => array(
		'class' => array(
			'title' => __('Class', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'content' => array(
			'title' => __('Title', 'dt_themes'),
			'type' => 'text',
			'is_content' => true,
			'default_value' => 'Title Comes Here'
		),
	)
);

$dt_modules['others']['h6'] = array(
	'name' => __('Heading 6', 'dt_themes'),
	'tooltip' => __('Use this module to add heading 6', 'dt_themes'),
	'icon_class' => 'ico-headings',
	'options' => array(
		'class' => array(
			'title' => __('Class', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'content' => array(
			'title' => __('Title', 'dt_themes'),
			'type' => 'text',
			'is_content' => true,
			'default_value' => 'Title Comes Here'
		),
	)
);

$dt_modules['others']['address'] = array(
	'name' => __('Address', 'dt_themes'),
	'tooltip' => __('Use this module to add address', 'dt_themes'),
	'icon_class' => 'ico-address',
	'options' => array(
		'line1' => array(
			'title' => __('Line 1', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'No: 58 A, East Madison St'
		),
		'line2' => array(
			'title' => __('Line 2', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'Baltimore, MD, USA'
		),
		'line3' => array(
			'title' => __('Line 3', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),
		'line4' => array(
			'title' => __('Line 4', 'dt_themes'),
			'type' => 'text',
			'default_value' => ''
		),		
	)
);

$dt_modules['others']['phone'] = array(
	'name' => __('Phone', 'dt_themes'),
	'tooltip' => __('Use this module to add phone number alone', 'dt_themes'),
	'icon_class' => 'ico-phone',
	'options' => array(
		'phone' => array(
			'title' => __('Phone', 'dt_themes'),
			'type' => 'text',
			'default_value' => '+1 200 258 2145'
		),		
	)
);

$dt_modules['others']['mobile'] = array(
	'name' => __('Mobile', 'dt_themes'),
	'tooltip' => __('Use this module to add mobile number alone', 'dt_themes'),
	'icon_class' => 'ico-mobile',
	'options' => array(
		'mobile' => array(
			'title' => __('Mobile', 'dt_themes'),
			'type' => 'text',
			'default_value' => '+91 99941 49897'
		),		
	)
);

$dt_modules['others']['fax'] = array(
	'name' => __('Fax', 'dt_themes'),
	'tooltip' => __('Use this module to add fax alone', 'dt_themes'),
	'icon_class' => 'ico-fax',
	'options' => array(
		'fax' => array(
			'title' => __('Fax', 'dt_themes'),
			'type' => 'text',
			'default_value' => '+1 100 458 2345'
		),		
	)
);

$dt_modules['others']['email'] = array(
	'name' => __('Email', 'dt_themes'),
	'tooltip' => __('Use this module to add email alone', 'dt_themes'),
	'icon_class' => 'ico-email',
	'options' => array(
		'emailid' => array(
			'title' => __('Email', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'yourname@somemail.com'
		),		
	)
);

$dt_modules['others']['web'] = array(
	'name' => __('Web', 'dt_themes'),
	'tooltip' => __('Use this module to add web alone', 'dt_themes'),
	'icon_class' => 'ico-web',
	'options' => array(
		'url' => array(
			'title' => __('URL', 'dt_themes'),
			'type' => 'text',
			'default_value' => 'http://www.google.com'
		),		
	)
);

$dt_modules['others']['clear'] = array(
	'name' => __('Clear', 'dt_themes'),
	'tooltip' => __('Add this module to add space between contents', 'dt_themes'),
	'icon_class' => 'ico-divider',
	'disable_resize' => true,
);

$dt_modules['others']['hr_border'] = array(
	'name' => __('Bordered Horizontal Rule', 'dt_themes'),
	'tooltip' => __('Use this module to add bordered horizontal rule', 'dt_themes'),
	'icon_class' => 'ico-divider',
	'disable_resize' => true,
);

$dt_modules['others']['hr'] = array(
	'name' => __('Horizontal Rule', 'dt_themes'),
	'tooltip' => __('Use this module to add horizontal rule', 'dt_themes'),
	'icon_class' => 'ico-divider',
	'disable_resize' => true,
);

$dt_modules['others']['hr_medium'] = array(
	'name' => __('Horizontal Rule Medium', 'dt_themes'),
	'tooltip' => __('Use this module to add medium horizontal rule', 'dt_themes'),
	'icon_class' => 'ico-divider',
	'disable_resize' => true,
);

$dt_modules['others']['hr_large'] = array(
	'name' => __('Horizontal Rule Large', 'dt_themes'),
	'tooltip' => __('Use this module to add large horizontal rule', 'dt_themes'),
	'icon_class' => 'ico-divider',
	'disable_resize' => true,
);

$dt_modules['others']['hr'] = array(
	'name' => __('Horizontal Rule With Top Link', 'dt_themes'),
	'tooltip' => __('Use this module to add horizontal rule with top link', 'dt_themes'),
	'icon_class' => 'ico-divider',
	'disable_resize' => true,
);

$dt_modules['others']['hr_invisible'] = array(
	'name' => __('Whitespace', 'dt_themes'),
	'tooltip' => __('Use this module to add whitespace', 'dt_themes'),
	'icon_class' => 'ico-divider',
	'disable_resize' => true,
);

$dt_modules['others']['hr_invisible_small'] = array(
	'name' => __('Whitespace Small', 'dt_themes'),
	'tooltip' => __('Use this module to add small whitespace', 'dt_themes'),
	'icon_class' => 'ico-divider',
	'disable_resize' => true,
);

$dt_modules['others']['hr_invisible_medium'] = array(
	'name' => __('Whitespace Medium', 'dt_themes'),
	'tooltip' => __('Use this module to add medium whitespace', 'dt_themes'),
	'icon_class' => 'ico-divider',
	'disable_resize' => true,
);

$dt_modules['others']['hr_invisible_large'] = array(
	'name' => __('Whitespace Large', 'dt_themes'),
	'tooltip' => __('Use this module to add large whitespace', 'dt_themes'),
	'icon_class' => 'ico-divider',
	'disable_resize' => true,
);

/*  End of Others Modules */

?>