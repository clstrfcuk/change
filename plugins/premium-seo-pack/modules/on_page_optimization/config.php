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
		'on_page_optimization' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 93,
				'show_in_menu' => false,
				'title' => __('Mass Optimization', 'psp'),
				'icon' => 'assets/menu_icon.png',
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32.png',
				'url'	=> admin_url('admin.php?page=' . $psp->alias . "_massOptimization")
			),
			'description' => __('This is a premium feature that will allow you to mass optimize your wordpress website in just a few clicks! It\'s the most unique feature and you will not find it anywhere else on the market.', 'psp'),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/mass-optimization/'
			),
			'load_in' => array(
				'backend' => array(
					'admin.php?page=psp_massOptimization',
					'admin-ajax.php',
					'edit.php',
					'post.php',
					'post-new.php',
					'edit-tags.php'
				),
				'frontend' => false
			),
			'javascript' => array(
				'admin',
				'hashchange',
				'tipsy',
				'ajaxupload',
				'jquery-ui-core',
				'jquery-ui-autocomplete'
			),
			'css' => array(
				'admin'
			)
		)
	)
 );