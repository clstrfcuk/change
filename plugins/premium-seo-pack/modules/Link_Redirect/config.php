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
		'Link_Redirect' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 98,
				'show_in_menu' => false,
				'title' => __('301 Link Redirect', 'psp')
				,'icon' => 'assets/menu_icon.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32.png',
				'url'	=> admin_url('admin.php?page=' . $psp->alias . "_Link_Redirect")
			),
			'description' => __("This module is very useful for any permalink changes.
The Link Redirect Module gives you an easy way of redirecting requests to other pages on your site or anywhere else on the web.
", 'psp'),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/301-link-redirect/'
			),
	        'load_in' => array(
				'backend' => array(
					'admin.php?page=psp_Link_Redirect',
					'admin-ajax.php'
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