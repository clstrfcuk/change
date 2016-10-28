<?php
/**
 * Config file, return as json_encode
 * http://www.aa-team.com
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
echo json_encode(
	array(
		'frontend' => array(
			'version' => '1.0',
			'menu' => array(
				'show_in_menu' => false,
				'order' => 1,
				'title' => __('Frontend', 'psp')
				,'icon' => 'assets/menu_icon.png'
			),
			'description' => __("Using this module you can display meta tags in frontend!", 'psp'),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/products/premium-seo-pack/'
			),
			'load_in' => array(
				'backend'	=> false,
				'frontend' 	=> true
			),
		)
	)
 );