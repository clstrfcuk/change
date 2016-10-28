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
		'facebook_planner' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 96,
				'show_in_menu' => false,
				'title' => __('Facebook Planner', 'psp')
				,'icon' => 'assets/menu_icon.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32.png',
				'url'	=> admin_url("admin.php?page=psp#facebook_planner")
			),
			'description' => __('This module allows you to post your content to facebook.', 'psp'),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/facebook-planner/'
			),
			'load_in' => array(
				'backend' => array(
					'admin.php?page=psp_facebook_planner',
					'admin-ajax.php',
					'edit.php',
					'post.php',
					'post-new.php'
				),
				'frontend' => false
			),
			'javascript' => array(
				'admin',
				'hashchange',
				'tipsy',
				'jquery-ui-core',
				'jquery-ui-datepicker',
				'jquery-ui-slider',
				'jquery-timepicker'
			),
			'css' => array(
				'admin'
			),
			'errors' => array(
				1 => __('
					You configured Facebook Planner Service incorrectly. See 
					' . ( $psp->convert_to_button ( array(
						'color' => 'white_blue psp-show-docs-shortcut',
						'url' => 'javascript: void(0)',
						'title' => 'here'
					) ) ) . ' for more details on fixing it. <br />
					Module Facebook Planner verification section: click Verify button and read status 
					' . ( $psp->convert_to_button ( array(
						'color' => 'white_blue',
						'url' => admin_url( 'admin.php?page=psp_server_status#sect-facebook_planner' ),
						'title' => 'here',
						'target' => '_blank'
					) ) ) . '<br />
					Setup the Facebook Planner module 
					' . ( $psp->convert_to_button ( array(
						'color' => 'white_blue',
						'url' => admin_url( 'admin.php?page=psp#facebook_planner' ),
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