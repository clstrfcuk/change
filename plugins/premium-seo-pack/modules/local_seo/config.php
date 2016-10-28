<?php
/**
 * Local SEO Config file, return as json_encode
 * http://www.aa-team.com
 * ======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
 echo json_encode(
	array(
		'local_seo' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 13,
				'title' => __('Local SEO', 'psp'),
				'icon' => 'assets/menu_icon.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32.png',
				'url'	=> admin_url("admin.php?page=psp#local_seo")
			),
			'description' => __('Local SEO', 'psp'),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/local-seo/'
			),
	        'load_in' => array(
				'backend' => array(
					'@all'
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
				'title'	=> __('Insert Local SEO Shortcodes', 'psp')
			)
		)
	)
 );