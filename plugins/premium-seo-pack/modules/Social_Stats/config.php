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
		'Social_Stats' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 18,
				'title' => __('Social Stats', 'psp')
				,'icon' => 'assets/menu_icon.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32.png',
				'url'	=> admin_url('admin.php?page=' . $psp->alias . "_Social_Stats")
			),
			'description' => __('You’re putting a lot of effort on marketing your website trough social media? Want to know for sure if your tactics have results? ', 'psp'),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/social-stats/'
			),
			'load_in' => array(
				'backend' => array(
					'admin.php?page=psp_Social_Stats',
					'admin-ajax.php'
				),
				'frontend' => true
			),
			'javascript' => array(
				'admin',
				'hashchange',
				'tipsy',
				'ajaxupload',
				'jquery-ui-core',
				//'jquery-ui-widget',
				//'jquery-ui-mouse',
				//'jquery-ui-accordion',
				//'jquery-ui-autocomplete',
				//'jquery-ui-slider',
				//'jquery-ui-tabs',
				'jquery-ui-sortable',
				//'jquery-ui-draggable',
				//'jquery-ui-droppable',
				//'jquery-ui-datepicker',
				//'jquery-ui-resize',
				//'jquery-ui-dialog',
				//'jquery-ui-button'
			),
			'css' => array(
				'admin'
			)
		)
	)
 );