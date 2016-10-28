<?php

global $psp;

require($psp->cfg['paths']['plugin_dir_path'] . 'modules/rich_snippets/' . 'lists.inc.php');

echo json_encode(
	array(
		array(

			/* recipe shortcode */
			'psp_rs_recipe' => array(
				'title' 	=> __('Insert Recipe Shortcode', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> false, // true|false
				'style' 	=> 'panel', // panel|panel-widget
				
				'exclude_empty_fields'	=> true,
				'shortcode'	=> '[psp_rs_recipe {atts}]',

				// create the box elements array
				'elements'	=> array(
				
					'name' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Name:', 'psp'),
						'desc' 		=> __('enter name', 'psp')
					)
					,'image' => array(
						'type' 		=> 'upload_image',
						'size' 		=> 'large',
						'title' 	=> 'Recipe Image',
						'value' 	=> 'Upload image',
						'thumbSize' => array(
							'w' => '100',
							'h' => '100',
							'zc' => '2',
						),
						'desc' 		=> 'select recipe image'
					)
					,'description' 	=> array(
						'type' 		=> 'textarea',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Description:', 'psp'),
						'desc' 		=> __('enter description', 'psp')
					)
					,'author' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Author:', 'psp'),
						'desc' 		=> __('enter author', 'psp')
					)
					,'pubdate' => array(
						'type' 		=> 'date',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Published Date:', 'psp'),
						'desc' 		=> __('enter published date', 'psp')
					)
					,'prephours' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Preparation hours:', 'psp'),
						'desc' 		=> __('enter preparation duration - hours', 'psp')
					)
					,'prepmins' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Preparation mins:', 'psp'),
						'desc' 		=> __('enter preparation duration - mins', 'psp')
					)
					,'cookhours' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Cook hours:', 'psp'),
						'desc' 		=> __('enter cook duration - hours', 'psp')
					)
					,'cookmins' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Cook mins:', 'psp'),
						'desc' 		=> __('enter cook duration - mins', 'psp')
					)
					
					,'yield' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Recipe Yield:', 'psp'),
						'desc' 		=> __('The quantity produced by the recipe (for example, number of people served, number of servings, etc)', 'psp')
					)
					,'calories' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Calories:', 'psp'),
						'desc' 		=> __('The number of calories', 'psp')
					)
					,'fatcount' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Fat count:', 'psp'),
						'desc' 		=> __('The number of grams of fat', 'psp')
					)
					,'sugarcount' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Sugar count:', 'psp'),
						'desc' 		=> __('The number of grams of sugar', 'psp')
					)
					,'saltcount' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Salt count:', 'psp'),
						'desc' 		=> __('The number of milligrams of sodium', 'psp')
					)
					,'instructions' 	=> array(
						'type' 		=> 'textarea',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Instructions:', 'psp'),
						'desc' 		=> __('The steps to make the dish', 'psp')
					)

				)
			) // end shortcode
			
		)
	)
);

?>