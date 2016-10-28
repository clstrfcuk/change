<?php
/**
 * Dummy module return as json_encode
 * http://www.aa-team.com
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
global $psp;
echo json_encode(
	array(
		$tryed_module['db_alias'] => array(
			// define the form_messages box
			'setup_box' => array(
				'title' 	=> __('Install plugin settings', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> array(
					'install_btn' => array(
						'type' => 'submit',
						'value' => __('Install settings', 'psp'),
						'color' => 'blue',
						'action' => 'psp-installDefaultOptions',
					)
				), // true|false|array
				'style' 	=> 'panel', // panel | panel-widget
				
				// create the box elements array
				'elements'	=> array(
					'install_box' => array(
						'type' 		=> 'textarea',
						'std' 		=> file_get_contents( $tryed_module["folder_path"] . 'default-setup.json' ),
						'size' 		=> 'large',
						'cols' 		=> '130',
						'title' 	=> __('Paste settings here', 'psp'),
						'desc' 		=> __('Default settings configuration loaded here.', 'psp'),
					)
				)
			)

			// define the form_messages box
			, 'import_seo_other_plugins' => array(
				'title' 	=> __('Import settings from other SEO plugins for posts, pages, custom post types', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> array(
					'install_btn' => array(
						'type' => 'submit',
						'value' => __('Import SEO', 'psp'),
						'color' => 'green',
						'action' => 'psp-ImportSEO',
					)
				), // true|false|array
				'style' 	=> 'panel', // panel|panel-widget
				
				// create the box elements array
				'elements'	=> array(
					'from' => array(
						'type' 		=> 'select',
						'std' 		=> 'yoast',
						'size' 		=> 'normal',
						'force_width' => '190',
						'title' 	=> __('Import from:', 'psp'),
						'desc' 		=> __('Select the plugin from which you want to import SEO settings for posts, pages, custom post types.', 'psp'),
						'options'	=> array(
							'Yoast WordPress SEO' 				=> 'Yoast WordPress SEO',
							'SEO Ultimate' 						=> 'SEO Ultimate',
							'All-in-One SEO Pack - old version' => 'All-in-One SEO Pack - old version',
							'All-in-One SEO Pack' 				=> 'All-in-One SEO Pack',
							'WooThemes SEO Framework' 			=> 'WooThemes SEO Framework'
						)
					)
				)
			)

			// define the form_messages box
			, 'backup_box' => array(
				'title' 	=> __('backup you current plugin settings', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> false, // true|false
				'style' 	=> 'panel', // panel|panel-widget
				
				// create the box elements array
				'elements'	=> array(
					'backup_box' => array(
						'type' 		=> 'textarea',
						'std' 		=> $psp->getAllSettings('json'),
						'size' 		=> 'large',
						'cols' 		=> '130',
						'title' 	=> __('Your current settings ', 'psp'),
						'desc' 		=> __('Copy / Paste this file if you want to backup all you plugins settings.', 'psp')
					)
				)
			)

		)
	)
);   
