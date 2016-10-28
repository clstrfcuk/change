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
		'serp' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 14,
				'title' => __('SERP', 'psp')
				,'icon' => 'assets/menu_icon.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32.png',
				'url'	=> admin_url('admin.php?page=' . $psp->alias . "_SERP")
			),
			'description' => __('This module reads the results from Google for you focus keywords', 'psp'),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/serp-tracking/'
			),
			'load_in' => array(
				'backend' => array(
					'admin.php?page=psp_SERP',
					'admin-ajax.php'
				),
				'frontend' => false
			),
			'javascript' => array(
				'admin',
				'hashchange',
				'tipsy',
				'jquery-ui-core',
				'jquery-ui-datepicker',
				'percentageloader-0.1',
				'flot-2.0',
				'flot-tooltip',
				'flot-stack',
				'flot-pie',
				'flot-time',
				'flot-resize'
			),
			'css' => array(
				'admin'
			),
			'errors' => array(
				1 => __('
					You configured Google Serp Service incorrectly. See 
					' . ( $psp->convert_to_button ( array(
						'color' => 'white_blue psp-show-docs-shortcut',
						'url' => 'javascript: void(0)',
						'title' => 'here'
					) ) ) . ' for more details on fixing it. <br />
					Module Google Serp verification section: click Verify button and read status 
					' . ( $psp->convert_to_button ( array(
						'color' => 'white_blue',
						'url' => admin_url( 'admin.php?page=psp_server_status#sect-google_serp' ),
						'title' => 'here',
						'target' => '_blank'
					) ) ) . '<br />
					Setup the Google Serp module 
					' . ( $psp->convert_to_button ( array(
						'color' => 'white_blue',
						'url' => admin_url( 'admin.php?page=psp#serp' ),
						'title' => 'here'
					) ) ) . '
					', 'psp'),
			)
		)
	)
);