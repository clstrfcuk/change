<?php
/**
 * Config file, return as json_encode
 * http://www.aa-team.com
 * ======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
 echo json_encode(
	array(
		'google_authorship' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 30,
				'title' => __('Google Authorship', 'psp')
				,'icon' => 'assets/menu_icon.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32.png',
				'url'	=> admin_url('admin.php?page=psp#google_authorship')
			),
			'description' => __("Google Publisher & Authorship module.", 'psp'),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/google_authorship/'
			),
			'load_in' => array(
				'backend' => array(
					'admin.php?page=psp_google_authorship',
					'admin-ajax.php',
					'user-edit.php',
					'user-new.php',
					'profile.php'
				),
				'frontend' => true
			),
			'javascript' => array(
				'admin',
				'hashchange',
				'tipsy'
			),
			'css' => array(
				'admin'
			)
		)
	)
 );