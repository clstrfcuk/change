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

			

			/* define the form_messages box */

			'module_box' => ($psp->get_plugin_status() != 'valid_hash' ? array(

					'title' 	=> __('Unlock - Premium SEO Pack', 'psp'),

					'icon' 		=> '{plugin_folder_uri}assets/validation_icon.png',

					'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4

					'header' 	=> true, // true|false

					'toggler' 	=> false, // true|false

					'buttons' 	=> false, // true|false

					'style' 	=> 'panel', // panel|panel-widget

					

					// create the box elements array

					'elements'	=> array(

						array(

							'type' 		=> 'message',

							'status' 	=> 'info',

							'html' 		=> __('You need to log into your CodeCanyon account and go to your “Downloads�? page. Locate this plugin you purchased in your “Downloads�? list and click on the “License Certificate�? link next to the download link. After you have downloaded the certificate you can open it in a text editor such as Notepad and copy the Item Purchase Code. How to image: <a href="http://support.alexandraipate.com/bb-plugins/epcv/key_instructions.jpg" target="_blank">link</a>', 'psp'),

						)

						,'productKey' => array(

							'type' 		=> 'text',

							'std' 		=> '',

							'size' 		=> 'small',

							'title' 	=> __('Item Purchase Code', 'psp'),

							'desc' 		=> __('Get it from CodeCanyon account and go to your “Downloads�? page.', 'psp')

						)

						

						,'yourEmail' => array(

							'type' 		=> 'text',

							'std' 		=> get_option('admin_email'),

							'size' 		=> 'small',

							'title' 	=> __('Your Email', 'psp'),

							'desc' 		=> __('We will notify you via this email about this product update and bug fix.', 'psp')

						)

						

						,'sendActions' => array(

							'type' 		=> 'buttons',

							'options'	=> array(

								array(

									'action' 	=> 'psp_activate_product',

									'width'		=> '100px',

									'type'		=> 'submit',

									'color'		=> 'green',

									'pos'		=> 'left',

									'value'		=> 'Activate now'

								)

							)

						)

					)

				) 

				: array(

					'title' 	=> __('Modules Manager', 'psp'),

					'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',

					'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4

					'header' 	=> true, // true|false

					'toggler' 	=> false, // true|false

					'buttons' 	=> false, // true|false

					'style' 	=> 'panel', // panel|panel-widget

					

					// create the box elements array

					'elements'	=> array(

						array(

							'type' 		=> 'app',

							'path' 		=> '{plugin_folder_path}lists.php',

						)

					)

				)

			)

		)

	)

);