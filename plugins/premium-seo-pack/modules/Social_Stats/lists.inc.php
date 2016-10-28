<?php

global $psp;

$psp_socialsharing_position = array();
$psp_socialsharing_position['horizontal'] = array(
	'left'			=> __('Left', 'psp'),
	'right'			=> __('Right', 'psp'),
	'center'		=> __('Center', 'psp')
);
$psp_socialsharing_position['vertical'] = array(
	'top'			=> __('Top', 'psp'),
	'bottom'		=> __('Bottom', 'psp'),
	'center'		=> __('Center', 'psp')
);

$psp_socialsharing_margin = array(
	'horizontal'	=> __('Horizontal', 'psp'),
	'vertical'		=> __('Vertical', 'psp')
);

$psp_socialsharing_opt = array();
$psp_socialsharing_opt['btnsize'] = array(
	'normal'		=> __('Normal', 'psp'),
	'large'			=> __('Large', 'psp')
);
$psp_socialsharing_opt['viewcount'] = array(
	'no'			=> __('No', 'psp'),
	'yes'			=> __('Yes', 'psp')
);
$psp_socialsharing_opt['withtext'] = array(
	'no'			=> __('No', 'psp'),
	'yes'			=> __('Yes', 'psp')
);
$psp_socialsharing_opt['withmore'] = array(
	'no'			=> __('No', 'psp'),
	'yes'			=> __('Yes', 'psp')
);

$psp_socialsharing_opt['contact'] = array(
	'text_print'	=> array( 'title' => __('Print text', 'psp'), 'std' => __('Print', 'psp') ),
	'text_email'	=> array( 'title' => __('Email text', 'psp'), 'std' => __('Email', 'psp') ),
	'email'			=> array( 'title' => __('Email address', 'psp'), 'std' => __('', 'psp') )
);

$psp_socialsharing_exclude = array(
	'include'		=> array( 'title' => __('Include only', 'psp'), 'std' => __('', 'psp'), 'desc' => __('Include only: the exclusive post, pages IDs list where you want the social share toolbar to appear (separate IDs by ,)', 'psp') ),
	'exclude'		=> array( 'title' => __('Exclude', 'psp'), 'std' => __('', 'psp'), 'desc' => __('Exclude: the post, pages IDs list where you don\'t want the social share toolbar to appear (separate IDs by ,)', 'psp') )
);

$psp_socialsharing_design['background_color'] = array( 'title' => __('Background color', 'psp'), 'std' => __('', 'psp') );

$psp_socialsharing_design['make_floating'] = array(
	'no'			=> __('No', 'psp'),
	'yes'			=> __('Yes', 'psp')
);

$psp_socialsharing_design['floating_beyond_content'] = array(
	'no'			=> __('No', 'psp'),
	'yes'			=> __('Yes', 'psp')
);