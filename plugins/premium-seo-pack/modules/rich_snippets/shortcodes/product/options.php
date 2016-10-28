<?php

global $psp;

require($psp->cfg['paths']['plugin_dir_path'] . 'modules/rich_snippets/' . 'lists.inc.php');

echo json_encode(
	array(
		array(

			/* product shortcode */
			'psp_rs_product' => array(
				'title' 	=> __('Insert Product Shortcode', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> false, // true|false
				'style' 	=> 'panel', // panel|panel-widget
				
				'exclude_empty_fields'	=> true,
				'shortcode'	=> '[psp_rs_product {atts}]',

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
					,'url' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Website URL:', 'psp'),
						'desc' 		=> __('enter website url', 'psp')
					)
					,'image' => array(
						'type' 		=> 'upload_image',
						'size' 		=> 'large',
						'title' 	=> 'Product Image',
						'value' 	=> 'Upload image',
						'thumbSize' => array(
							'w' => '100',
							'h' => '100',
							'zc' => '2',
						),
						'desc' 		=> 'select product image'
					)
					,'description' 	=> array(
						'type' 		=> 'textarea',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Description:', 'psp'),
						'desc' 		=> __('enter description', 'psp')
					)
					,'brand' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Brand:', 'psp'),
						'desc' 		=> __('enter brand', 'psp')
					)
					,'manufacturer' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Manufacturer:', 'psp'),
						'desc' 		=> __('enter manufacturer', 'psp')
					)
					,'model' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Model:', 'psp'),
						'desc' 		=> __('enter model', 'psp')
					)
					,'prod_id' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Product ID:', 'psp'),
						'desc' 		=> __('enter product id', 'psp')
					)
					,'price' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Price:', 'psp'),
						'desc' 		=> __('enter price', 'psp')
					)
					,'currency' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Currency:', 'psp'),
						'desc' 		=> __('ex: USD, CAD, GBP (full list is on <a href="http://en.wikipedia.org/wiki/ISO_4217" target="_blank">Wikipedia</a>', 'psp')
					)
					,'item_name' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Item Name:', 'psp'),
						'desc' 		=> __('enter item name', 'psp')
					)
					,'best_rating' => array(
						'type' 		=> 'ratestar',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Best Rating:', 'psp'),
						'desc' 		=> __('select best rating', 'psp'),
						'nbstars'	=> 5
					)
					,'worst_rating' => array(
						'type' 		=> 'ratestar',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Worst Rating:', 'psp'),
						'desc' 		=> __('select worst rating', 'psp'),
						'nbstars'	=> 5
					)
					,'current_rating' => array(
						'type' 		=> 'ratestar',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Current Rating:', 'psp'),
						'desc' 		=> __('select current rating', 'psp'),
						'nbstars'	=> 5
					)
					,'avg_rating' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Average Rating:', 'psp'),
						'desc' 		=> __('The count of total number of ratings.', 'psp')
					)
					,'nb_reviews' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Number of Reviews:', 'psp'),
						'desc' 		=> __('The count of total number of reviews.', 'psp')
					)
					,'condition' => array(
						'type' 		=> 'select',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '200',
						'title' 	=> __('Condition:', 'psp'),
						'desc' 		=> 'select condition',
						'options'	=> array_merge( array('none' => __('Select condition', 'psp')), $psp_product_condition )
					)
					,'availability' => array(
						'type' 		=> 'select',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '200',
						'title' 	=> __('Availability:', 'psp'),
						'desc' 		=> 'select availability',
						'options'	=> array_merge( array('none' => __('Select availability', 'psp')), $psp_product_availability )
					)

				)
			) // end shortcode
			
		)
	)
);

?>