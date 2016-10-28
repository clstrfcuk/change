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
		'google_pagespeed' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 94,
				'show_in_menu' => false,
				'title' => __('PageSpeed Insights', 'psp'),
				'icon' => 'assets/16_pagespeed.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32.png',
				'url'	=> admin_url('admin.php?page=' . $psp->alias . "_PageSpeedInsights")
			),
			'description' => __('The PageSpeed Insights lets you analyze the performance of your website pages. It offers tailored suggestions for how you can optimize your pages.', 'psp'),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/pagespeed-insights/'
			),
			'load_in' => array(
				'backend' => array(
					'admin.php?page=psp_PageSpeedInsights',
					'admin-ajax.php'
				),
				'frontend' => false
			),
			'javascript' => array(
				'admin',
				'hashchange',
				'tipsy',
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
					You configured PageSpeed Service incorrectly. See 
					' . ( $psp->convert_to_button ( array(
						'color' => 'white_blue psp-show-docs-shortcut',
						'url' => 'javascript: void(0)',
						'title' => 'here'
					) ) ) . ' for more details on fixing it. <br />
					Module Google Pagespeed verification section: click Verify button and read status 
					' . ( $psp->convert_to_button ( array(
						'color' => 'white_blue',
						'url' => admin_url( 'admin.php?page=psp_server_status#sect-google_pagespeed' ),
						'title' => 'here',
						'target' => '_blank'
					) ) ) . '<br />
					Setup the PageSpeed module 
					' . ( $psp->convert_to_button ( array(
						'color' => 'white_blue',
						'url' => admin_url( 'admin.php?page=psp#google_pagespeed' ),
						'title' => 'here'
					) ) ) . '
					', 'psp'),
			)
		)
	)
 );