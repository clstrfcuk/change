<?php

global $psp;

require($psp->cfg['paths']['plugin_dir_path'] . 'modules/rich_snippets/' . 'lists.inc.php');

echo json_encode(
	array(
		array(

			/* event shortcode */
			'psp_rs_event' => array(
				'title' 	=> __('Insert Event Shortcode', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> false, // true|false
				'style' 	=> 'panel', // panel|panel-widget
				
				'exclude_empty_fields'	=> true,
				'shortcode'	=> '[psp_rs_event {atts}]',

				// create the box elements array
				'elements'	=> array(
				
					'eventtype' => array(
						'type' 		=> 'select',
						'std' 		=> 'Event',
						'size' 		=> 'large',
						'force_width'=> '200',
						'title' 	=> __('Event Type:', 'psp'),
						'desc' 		=> 'select event type',
						'options'	=> $psp_event_type
					)
					,'name' 	=> array(
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
						'title' 	=> 'Event Image',
						'value' 	=> 'Upload image',
						'thumbSize' => array(
							'w' => '100',
							'h' => '100',
							'zc' => '2',
						),
						'desc' 		=> 'select event image'
					)
					,'description' 	=> array(
						'type' 		=> 'textarea',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Description:', 'psp'),
						'desc' 		=> __('enter description', 'psp')
					)
					,'startdate' => array(
						'type' 		=> 'date',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Start Date:', 'psp'),
						'desc' 		=> __('enter start date', 'psp')
					)
					,'starttime' => array(
						'type' 		=> 'time',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Start Time:', 'psp'),
						'desc' 		=> __('enter start time', 'psp'),
						
						'ampm'				=> true
					)
					,'enddate' => array(
						'type' 		=> 'date',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('End Date:', 'psp'),
						'desc' 		=> __('enter end date', 'psp')
					)
					,'duration' => array(
						'type' 		=> 'time',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Duration:', 'psp'),
						'desc' 		=> __('enter duration', 'psp')
					)
					,'street' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Street Address:', 'psp'),
						'desc' 		=> __('enter street address', 'psp')
					)
					,'pobox' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('P.O. Box:', 'psp'),
						'desc' 		=> __('enter p.o. box', 'psp')
					)
					,'city' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('City:', 'psp'),
						'desc' 		=> __('enter city', 'psp')
					)
					,'state' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('State or Region:', 'psp'),
						'desc' 		=> __('enter state or region', 'psp')
					)
					,'postalcode' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Postal code or Zipcode:', 'psp'),
						'desc' 		=> __('enter postal code or zipcode', 'psp')
					)
					,'country' => array(
						'type' 		=> 'select',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '200',
						'title' 	=> __('Country:', 'psp'),
						'desc' 		=> 'select country',
						'options'	=> array_merge( array('none' => __('Select country', 'psp')), $psp_countries_list )
					)
                    ,'place_name'     => array(
                        'type'      => 'text',
                        'std'       => '',
                        'size'      => 'large',
                        'force_width'=> '400',
                        'title'     => __('Place Name:', 'psp'),
                        'desc'      => __('enter place name', 'psp')
                    )
					,'map_latitude' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Latitude:', 'psp'),
						'desc' 		=> __('enter latitude', 'psp')
					)
					,'map_longitude' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '400',
						'title' 	=> __('Longitude:', 'psp'),
						'desc' 		=> __('enter longitude', 'psp')
					)

				)
			) // end shortcode
			
		)
	)
);

?>