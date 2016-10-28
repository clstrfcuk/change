<?php
/**
* Config file, return as json_encode
* http://www.aa-team.com
* =======================
*
* @author		Andrei Dinca, AA-Team
* @version		1.0
*/
echo json_encode(array(
    'depedencies' => array(
        'version' => '1.0',
        'menu' => array(
            'order' => 1,
            'title' => __('Plugin Depedencies', 'psp')
            ,'icon' => 'assets/menu_icon.png'
        ),
        'description' => "Plugin Depedencies",
        'module_init' => 'init.php',
        'help' => array(
			'type' => 'remote',
			'url' => 'http://docs.aa-team.com/products/premium-seo-pack/'
		),
        'load_in' => array(
			'backend' => array(
				'admin-ajax.php'
			),
			'frontend' => false
		),
		'javascript' => array(
			'admin',
			'hashchange',
			'tipsy'
		),
		'css' => array(
			'admin'
		)
    )
));