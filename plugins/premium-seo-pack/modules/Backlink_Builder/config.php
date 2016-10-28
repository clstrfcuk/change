<?php
/**
 * Config file, return as json_encode
 * http://www.aa-team.com
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
global $psp;
echo json_encode(
	array(
		'Backlink_Builder' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 99,
				'show_in_menu' => false,
				'title' => __('Backlink Builder', 'psp'),
				'icon' => 'assets/menu_icon.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32.png',
				'url'	=> admin_url('admin.php?page=' . $psp->alias . "_Backlink_Builder")
			),
			'description' => __("Our Backlink Builder Module will automatically add your link to thousands of different website directories that will automatically provide free backlinks for you in just minutes!", $psp->localizationName),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/backlink-builder/'
			),
			'load_in' => array(
				'backend' => array(
					'admin.php?page=psp_Backlink_Builder',
					'admin-ajax.php'
				),
				'frontend' => false
			),
			'javascript' => array(
				'admin',
				'hashchange',
				'tipsy'
			),
			'css' => array(
				'admin',
				'tipsy'
			)
		)
	)
);