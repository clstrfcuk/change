<?php

if ( !function_exists('psp_getLocationsList') ) { function psp_getLocationsList() {
	global $psp;
	global $wpdb;

	ob_start();
	
	$sqlClause = '';

	$sql = "SELECT a.ID
	            FROM " . $wpdb->prefix . "posts as a
	            LEFT JOIN " . $wpdb->prefix . "postmeta as b
	            ON b.post_id = a.ID
	            WHERE 1=1 " . $sqlClause . " AND a.post_status = 'publish' AND a.post_password = ''
	            AND a.post_type = 'psp_locations'
	            AND (b.meta_key = 'psp_locations_meta' AND !ISNULL(b.meta_value) AND b.meta_value != '')
	            ORDER BY a.post_title ASC
	            LIMIT 1000;";

	$res = $wpdb->get_col( $sql );
?>
<div class="psp-form-row">
	<label><?php _e('Select location:', 'psp'); ?></label>
	<div class="psp-form-item large">
	<span class="formNote">&nbsp;</span>

	<select id="psp-location-id" name="location_id" style="width:120px;">
		<option value="all">All locations</option>
	<?php
	foreach ($res as $key => $value) {
		$val = '';
		echo '<option value="' . ( $value ) . '" ' . ( $val == $value ? 'selected="true"' : '' ) . '>' . ( $value ) . '</option>';
	}
	?>
	</select>&nbsp;&nbsp;&nbsp;&nbsp;

	</div>
</div>
<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
} }
global $psp;
echo json_encode(
	array(
		array(

			/* business shortcode */
			// [psp_business id=all show_name=true show_desc=true show_img_logo=true show_img_building=true]
			'psp_business' => array(
				'title' 	=> __('Insert Business Shortcode', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> false, // true|false
				'style' 	=> 'panel', // panel|panel-widget
				
				'shortcode'	=> '[psp_business id={location_id} show_name={show_name} show_desc={show_desc} show_img_logo={show_img_logo} show_img_building={show_img_building}]',

				// create the box elements array
				'elements'	=> array(
				
					'location_id' => array(
						'type' 		=> 'html',
						'html' 		=> psp_getLocationsList()
					),

					'show_name' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show Name:', 'psp'),
						'desc' 		=> __('show business name', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					),
					'show_desc' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show Description:', 'psp'),
						'desc' 		=> __('show business description', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					),
					'show_img_logo' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show Business Logo:', 'psp'),
						'desc' 		=> __('show business logo image', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					),
					'show_img_building' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show Business Building:', 'psp'),
						'desc' 		=> __('show business building image', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					)

				)
			) // end shortcode
			
			/* address shortcode */
			// [psp_address id=all show_street=true show_city=true show_state=true show_zipcode=true show_country=true]
			,'psp_address' => array(
				'title' 	=> __('Insert Address Shortcode', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> false, // true|false
				'style' 	=> 'panel', // panel|panel-widget
				
				'shortcode'	=> '[psp_address id={location_id} show_street={show_street} show_city={show_city} show_state={show_state} show_zipcode={show_zipcode} show_country={show_country}]',

				// create the box elements array
				'elements'	=> array(
				
					'location_id' => array(
						'type' 		=> 'html',
						'html' 		=> psp_getLocationsList()
					),

					'show_street' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show Street:', 'psp'),
						'desc' 		=> __('show street address', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					),
					'show_city' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show City:', 'psp'),
						'desc' 		=> __('show address city', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					),
					'show_state' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show State:', 'psp'),
						'desc' 		=> __('show address state', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					),
					'show_zipcode' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show Zipcode:', 'psp'),
						'desc' 		=> __('show address zipcode', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					),
					'show_country' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show Country:', 'psp'),
						'desc' 		=> __('show address country', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					)

				)
			) // end shortcode
			
			/* contact shortcode */
			// [psp_contact id=all show_phone=true show_altphone=true show_fax=true show_email=true]
			,'psp_contact' => array(
				'title' 	=> __('Insert Contact Shortcode', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> false, // true|false
				'style' 	=> 'panel', // panel|panel-widget
				
				'shortcode'	=> '[psp_contact id={location_id} show_phone={show_phone} show_altphone={show_altphone} show_fax={show_fax} show_email={show_email}]',

				// create the box elements array
				'elements'	=> array(
				
					'location_id' => array(
						'type' 		=> 'html',
						'html' 		=> psp_getLocationsList()
					),

					'show_phone' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show Phone:', 'psp'),
						'desc' 		=> __('show phone contact', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					),
					'show_altphone' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show Alt. Phone:', 'psp'),
						'desc' 		=> __('show alternative phone contact', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					),
					'show_fax' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show Fax:', 'psp'),
						'desc' 		=> __('show fax contact', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					),
					'show_email' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show Email:', 'psp'),
						'desc' 		=> __('show email contact', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					)

				)
			) // end shortcode
			
			/* payment shortcode */
			// [psp_payment id=all show_payment=true show_currencies=true show_pricerange=true]
			,'psp_payment' => array(
				'title' 	=> __('Insert Payment Shortcode', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> false, // true|false
				'style' 	=> 'panel', // panel|panel-widget
				
				'shortcode'	=> '[psp_payment id={location_id} show_payment={show_payment} show_currencies={show_currencies} show_pricerange={show_pricerange}]',

				// create the box elements array
				'elements'	=> array(
				
					'location_id' => array(
						'type' 		=> 'html',
						'html' 		=> psp_getLocationsList()
					),

					'show_payment' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show Payment:', 'psp'),
						'desc' 		=> __('show payment', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					),
					'show_currencies' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show Currencies:', 'psp'),
						'desc' 		=> __('show currencies', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					),
					'show_pricerange' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show Price Range:', 'psp'),
						'desc' 		=> __('show price range', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					)

				)
			) // end shortcode
			
			/* opening hours shortcode */
			// [psp_opening_hours id=all show_head=true]
			,'psp_opening_hours' => array(
				'title' 	=> __('Insert Opening Hours Shortcode', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> false, // true|false
				'style' 	=> 'panel', // panel|panel-widget
				
				'shortcode'	=> '[psp_opening_hours id={location_id} show_head={show_head}]',

				// create the box elements array
				'elements'	=> array(
				
					'location_id' => array(
						'type' 		=> 'html',
						'html' 		=> psp_getLocationsList()
					),

					'show_head' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show Heading:', 'psp'),
						'desc' 		=> __('show heading', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					)

				)
			) // end shortcode
			
			/* full shortcode */
			// [psp_full id=all show_business=true show_address=true show_contact=true show_opening_hours=true show_payment=true show_gmap=true]
			,'psp_full' => array(
				'title' 	=> __('Insert Full Shortcode', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> false, // true|false
				'style' 	=> 'panel', // panel|panel-widget
				
				'shortcode'	=> '[psp_full id={location_id} show_business={show_business} show_address={show_address} show_contact={show_contact} show_opening_hours={show_opening_hours} show_payment={show_payment} show_gmap={show_gmap}]',

				// create the box elements array
				'elements'	=> array(
				
					'location_id' => array(
						'type' 		=> 'html',
						'html' 		=> psp_getLocationsList()
					),

					'show_business' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show Business:', 'psp'),
						'desc' 		=> __('show business details', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					),
					'show_address' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show Address:', 'psp'),
						'desc' 		=> __('show address details', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					),
					'show_contact' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show Contact:', 'psp'),
						'desc' 		=> __('show contact details', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					),
					'show_opening_hours' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show Opening Hours:', 'psp'),
						'desc' 		=> __('show opening hours details', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					),
					'show_payment' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show Payment:', 'psp'),
						'desc' 		=> __('show payment details', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					),
					'show_gmap' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Show Google Map:', 'psp'),
						'desc' 		=> __('show google map details', 'psp'),
						'options'	=> array(
							'true' 		=> __('YES', 'psp'),
							'false' 	=> __('NO', 'psp')
						)
					)

				)
			) // end shortcode
			
			/* google map shortcode */
			// [psp_gmap id=all width=320 height=240 zoom=12 maptype="roadmap" type="static"]
			,'psp_gmap' => array(
				'title' 	=> __('Insert Google Map', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> false, // true|false
				'style' 	=> 'panel', // panel|panel-widget
				
				'shortcode'	=> '[psp_gmap id={location_id} width={width} height={height} zoom={zoom} maptype="{maptype}" type="{type}"]',

				// create the box elements array
				'elements'	=> array(
				
					'location_id' => array(
						'type' 		=> 'html',
						'html' 		=> psp_getLocationsList()
					),

					'width' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '320',
						'size' 		=> 'large',
						'force_width'=> '150',
						'title' 	=> __('Width: ', 'psp'),
						'desc' 		=> __('google map width', 'psp')
					),
					'height' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '240',
						'size' 		=> 'large',
						'force_width'=> '150',
						'title' 	=> __('Height: ', 'psp'),
						'desc' 		=> __('google map height', 'psp')
					),
					'zoom' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '12',
						'size' 		=> 'large',
						'force_width'=> '150',
						'title' 	=> __('Zoom: ', 'psp'),
						'desc' 		=> __('google map zoom (recommended values: 1-20)', 'psp')
					),
					'maptype' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('Map type:', 'psp'),
						'desc' 		=> __('google map type', 'psp'),
						'options'	=> array(
							'roadmap' 		=> __('Roadmap', 'psp'),
							'satellite' 	=> __('Satellite', 'psp'),
							'terrain' 		=> __('Terrain', 'psp'),
							'hybrid' 		=> __('Hybrid', 'psp')
						)
					),
					'type' => array(
						'type' 		=> 'select',
						'std' 		=> 'no',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title' 	=> __('What map do you want to display? ', 'psp'),
						'desc' 		=> __('static: image map; dynamic: javascript map', 'psp'),
						'options'	=> array(
							'static' 		=> __('Static', 'psp'),
							'dynamic' 		=> __('Dynamic', 'psp')
						)
					)

				)
			) // end shortcode
			
		)
	)
);

?>