<?php
/**
 * Social_Stats Config file, return as json_encode
 * http://www.aa-team.com
 * ======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
global $psp;
 echo json_encode(
	array(
		'Google_Analytics' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 11,
				'title' => __('Google Analytics', 'psp')
				,'icon' => 'assets/menu_icon.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32.png',
				'url'	=> admin_url('admin.php?page=' . $psp->alias . "_Google_Analytics")
			),
			'description' => __("We’ve made a module that takes the data from Google Analytics and transforms it into a easy to understand dashboard, that will allow you to see the impact on search engines.", 'psp'),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/google-analytics/'
			),
			'load_in' => array(
				'backend' => array(
					'admin.php?page=psp_Google_Analytics',
					'admin-ajax.php'
				),
				'frontend' => true
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
					You configured Google Analytics Service incorrectly. See 
					' . ( $psp->convert_to_button ( array(
						'color' => 'white_blue psp-show-docs-shortcut',
						'url' => 'javascript: void(0)',
						'title' => 'here'
					) ) ) . ' for more details on fixing it. <br />
					Module Google Analytics verification section: click Verify button and read status 
					' . ( $psp->convert_to_button ( array(
						'color' => 'white_blue',
						'url' => admin_url( 'admin.php?page=psp_server_status#sect-google_analytics' ),
						'title' => 'here',
						'target' => '_blank'
					) ) ) . '<br />
					Setup the Google Analytics module 
					' . ( $psp->convert_to_button ( array(
						'color' => 'white_blue',
						'url' => admin_url( 'admin.php?page=psp#Google_Analytics' ),
						'title' => 'here'
					) ) ) . '
					', 'psp'),
				2 => __('
					You don\'t have the cURL library installed! Please activate it!
					', 'psp')
			)
		)
	)
 );