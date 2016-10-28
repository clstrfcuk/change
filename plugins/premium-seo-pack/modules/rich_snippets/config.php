<?php
/**
 * Social_Stats Config file, return as json_encode
 * http://www.aa-team.com
 * ======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
 echo json_encode(
	array(
		'rich_snippets' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 23,
				'title' => __('Rich Snippets', 'psp')
				,'icon' => 'assets/menu_icon.png'
			),
			'description' => __('Rich Snippets - Schema.org', 'psp'),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/products/premium-seo-pack/'
			),
			'load_in' => array(
				'backend' => array(
					'admin-ajax.php',
					'post.php',
					'post-new.php'
				),
				'frontend' => true
			),
			'javascript' => array(
				'admin',
				'hashchange',
				'tipsy',
				'ajaxupload',
				'jquery-rateit-js'
			),
			'css' => array(
				'admin'
			),
			'shortcodes_btn' => array(
				'icon' 	=> 'assets/20-icon.png',
				'title'	=> __('Insert Rich Snippets Shortcodes', 'psp')
			)
		)
	)
 );