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
		'remote_support' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 4,
				'show_in_menu' => false,
				'title' => 'Remote Support',
				'icon' => 'assets/16_support.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/support.png',
				'url'	=> admin_url("admin.php?page=psp_remote_support")
			),
			'description' => __("Using this module you can give secured access to your wordpress install in case you have problems with the plugin!", 'psp'),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/remote-support-2/'
			),
			'load_in' => array(
				'backend' => array(
					'admin.php?page=psp_remote_support',
					'admin-ajax.php'
				),
				'frontend' => false
			),
		)
	)
);