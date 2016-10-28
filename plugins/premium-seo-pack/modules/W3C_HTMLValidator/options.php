<?php
/**
 * module return as json_encode
 * http://www.aa-team.com
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
echo json_encode(
	array(
		$tryed_module['db_alias'] => array(
			/* define the form_messages box */
			'setup_box' => array(
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'toggler' 	=> false, // true|false
				'style' 	=> 'panel-widget', // panel|panel-widget

				// create the box elements array
				'elements'	=> array(
					'install_box' => array(
						'type' 	=> 'app',
						'path' 	=> '{plugin_folder_path}panel.php',
					)
				)
			)
		)
	)
);