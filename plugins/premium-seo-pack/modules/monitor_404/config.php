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
		'monitor_404' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 92,
				'show_in_menu' => false,
				'title' => __('Monitor 404', 'psp')
				,'icon' => 'assets/menu_icon.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32.png',
				'url'	=> admin_url('admin.php?page=' . $psp->alias . "_mass404Monitor")
			),
			'description' => __('On this module you can see what URLs are referring visitors to 404 pages, how many hits it had and redirect them to another page.', 'psp'),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/404-monitor/'
			),
			'load_in' => array(
				'backend' => array(
					'admin.php?page=psp_mass404Monitor',
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
			),
			'shortcodes_btn' => array(
				'icon' 	=> 'assets/menu_icon.png',
				'title'	=> __('Insert Monitor 404 sh', 'psp')
			)
		)
	)
 );