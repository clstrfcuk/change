<?php
/**
 * Title & Meta Format Config file, return as json_encode
 * http://www.aa-team.com
 * ======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
 echo json_encode(
	array(
		'title_meta_format' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 10,
				'title' => __('Title & Meta Format', 'psp')
				,'icon' => 'assets/menu_icon.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32.png',
				'url'	=> admin_url("admin.php?page=psp#title_meta_format")
			),
			'description' => __("Using this module you can set custom page titles, meta descriptions, meta keywords, meta robots and social meta using defined format tags for Homepage, Posts, Pages, Categories, Tags, Custom Taxonomies, Archives, Authors, Search, 404 Pages and Pagination.", 'psp'),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/title-meta-format/'
			),
			'load_in' => array(
				'backend' => array(
					'admin-ajax.php'
				),
				'frontend' => true
			),
			'javascript' => array(
				'admin',
				'hashchange',
				'tipsy',
				'ajaxupload'
			),
			'css' => array(
				'admin'
			)
		)
	)
 );