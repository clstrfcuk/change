<?php
/**
 * W3C_HTMLValidator Config file, return as json_encode
 * http://www.aa-team.com
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
 echo json_encode(
	array(
		'W3C_HTMLValidator' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 97,
				'show_in_menu' => false,
				'title' => __('W3C Validator', 'psp')
				,'icon' => 'assets/menu_icon.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32.png',
				'url'	=> admin_url('admin.php?page=' . $psp->alias . "_HTMLValidator")
			),
			'description' => __('This module allows you to Mass Check the markup (HTML, XHTML, …) of your pages/posts/custom taxonomies.', 'psp'),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/w3c-validator/'
			),
			'load_in' => array(
				'backend' => array(
					'admin.php?page=psp_HTMLValidator',
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
				'admin'
			)
		)
	)
 );