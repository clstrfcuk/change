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
		'sitemap' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 20,
				'title' => __('Sitemap', 'psp')
				,'icon' => 'assets/menu_icon.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32.png',
				'url'	=> admin_url("admin.php?page=psp#sitemap")
			),
			'description' => __('The sitemap is generated automatically, and you can submit it to Google or Bing right from the dashboard. You can choose what to include, and if you want to include Images as well.', 'psp'),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/sitemap/'
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
			),
			'shortcodes_btn' => array(
				'icon' 	=> 'assets/menu_icon.png',
				'title'	=> __('Insert Sitemap sh', 'psp')
			)
		)
	)
 );