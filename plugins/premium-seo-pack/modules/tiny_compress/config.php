<?php
/**
 * Smushit Config file, return as json_encode
 * http://www.aa-team.com
 * ======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
 echo json_encode(
	array(
		'tiny_compress' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 13,
				'title' => __('Tiny Compress', 'psp')
				,'icon' => 'assets/menu_icon.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32.png',
				'url'	=> admin_url('admin.php?page=' . $psp->alias . "_tiny_compress")
			),
			'description' => __('Tiny Compress module uses optimization techniques specific to image format to remove unnecessary bytes from image files, by connecting to the <a href="https://tinypng.com/" target="_blank">TinyPNG service</a>. It is a "lossless" tool, which means it optimizes the images without changing their look or visual quality', 'psp'),
			'module_init' => 'init.php',
      	  	'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/premium-seo-pack/documentation/media-smushit/'
			),
			'load_in' => array(
				'backend' => array(
					'admin.php?page=psp_tiny_compress',
					'admin-ajax.php',
					//'upload.php',
					'media-new.php'
				),
				'frontend' => false
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
            'errors' => array(
                1 => __('
                    You configured Tiny Compress module incorrectly. See 
                    ' . ( $psp->convert_to_button ( array(
                        'color' => 'white_blue psp-show-docs-shortcut',
                        'url' => 'javascript: void(0)',
                        'title' => 'here'
                    ) ) ) . ' for more details on fixing it. <br />
                    Module Tiny Compress verification section: click Verify button and read status 
                    ' . ( $psp->convert_to_button ( array(
                        'color' => 'white_blue',
                        'url' => admin_url( 'admin.php?page=psp_server_status#sect-tiny_compress' ),
                        'title' => 'here',
                        'target' => '_blank'
                    ) ) ) . '<br />
                    Setup the Tiny Compress module 
                    ' . ( $psp->convert_to_button ( array(
                        'color' => 'white_blue',
                        'url' => admin_url( 'admin.php?page=psp#tiny_compress' ),
                        'title' => 'here'
                    ) ) ) . '
                    ', 'psp'),
                2 => __('
                    You don\'t have the cURL library installed! Please make a HTTP client available (activate the cURL library)!
                    ', 'psp')
            )
		)
	)
 );