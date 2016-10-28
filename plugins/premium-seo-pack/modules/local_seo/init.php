<?php
/*
* Define class pspMisc
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspLocalSEO') != true) {
    class pspLocalSEO
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_plugin = null;

		protected $module_folder = '';
		protected $module_folder_path = '';
		private $module = '';
		
		protected $settings = array();
		
		static protected $_instance;
		
		static protected $geo_uri = 'http://maps.googleapis.com/maps/api/geocode/{output}?address={address}&sensor=false';
		static protected $geo_uri_js = 'http://maps.google.com/maps/api/js?v=3.exp&sensor=false';
		static protected $slug = 'psplocation';
		
	
        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;
			
        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/local_seo/';
			$this->module_folder_path = $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/local_seo/';
			$this->module = $this->the_plugin->cfg['modules']['local_seo'];
			
			$this->settings = $this->the_plugin->getAllSettings( 'array', 'local_seo' );
			
			if ( isset($this->settings['slug']) && !empty($this->settings['slug']) )
				self::$slug = $this->settings['slug'];

			if ( !$this->the_plugin->verify_module_status( 'local_seo' ) ) ; //module is inactive
			else {
				if ( $this->the_plugin->is_admin === true ) {
					$this->init();
				}
			}
        }
        
        public function init() {

        	$this->init_postType();
        }
        
		public function init_postType() 
		{
		    // get label
		    $labels = array(
		        'name' 					=> __('PSP Locations', 'psp'),
		        'singular_name' 		=> __('psp location', 'psp'),
		        'add_new' 				=> __('Add new location', 'psp'),
		        'add_new_item' 			=> __('Add new location', 'psp'),
		        'edit_item'			 	=> __('Edit location', 'psp'),
		        'new_item' 				=> __('New location', 'psp'),
		        'view_item' 			=> __('View location', 'psp'),
		        'search_items' 			=> __('Search into locations', 'psp'),
		        'not_found' 			=> __('No locations found', 'psp'),
		        'not_found_in_trash' 	=> __('No locations in trash', 'psp')
		    );
		  
		    // start formationg arguments
		    $args = array(
			    'rewrite' => array(
				    'slug' => self::$slug
		    	),
		        'labels' => $labels,
		        'public' => true,
		        'publicly_queryable' => true,
		        'show_ui' => true,
		        'has_archive' => true,
		        'query_var' => true,
				'menu_icon' => $this->module_folder . 'assets/menu_icon.png',
		        'capability_type' => 'post',
		        'show_in_menu' => true,
		        'supports' => array( 'title'/*, 'editor'*/ )
		    );
		
		    register_post_type('psp_locations', $args);
			
			add_action( 'admin_head', array( $this, 'add_32px_icon' ) );
			
			// add meta boxes to "locations" post type
			add_action('admin_menu', array($this, 'add_to_menu_metabox'));
			
			/* use save_post action to handle data entered */
			add_action( 'save_post', array( $this, 'meta_box_save_postdata' ) );
			
			if( isset($_GET['post_type']) && $_GET['post_type'] == 'psp_locations') 
				add_action('admin_head', array( $this, 'extra_css') );
	    }
        
	    
		public function add_32px_icon()
		{
			?>
			<style type="text/css" media="screen">
    			.icon32-posts-psp_locations {
    				background: url(<?php echo $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/local_seo/assets/32.png';?>) no-repeat !important;
    			}
    		</style>
    		<?php 
		}
		
		public function add_to_menu_metabox()
		{
			
			self::getInstance()
	       		->_registerMetaBoxes();
		}
		
		/**
	    * Register plug-in admin metaboxes
	    */
	    protected function _registerMetaBoxes()
	    {
	    	$screens = array(
	    		'psp_locations' => __( 'PSP Locations Details', 'psp' )
	    	);
		    foreach ($screens as $key => $screen) {
		    	$screen = str_replace("_", " ", $screen);
				$screen = ucfirst($screen);
		        add_meta_box(
		            'psp_locations_meta_box',
		            $screen,
		            array($this, 'custom_metabox'),
		            $key,
		            'normal'
		        );
		    }
	        return $this;
	    }
		
		public function custom_metabox() {

			global $post;
			$post_id = (int) $post->ID;
			
			// load the settings template class
			require_once( $this->the_plugin->cfg['paths']['freamwork_dir_path'] . 'settings-template.class.php' );
			
			// Initalize the your aaInterfaceTemplates
			$aaInterfaceTemplates = new aaInterfaceTemplates($this->the_plugin->cfg);
			
			// retrieve the existing value(s) for this meta field. This returns an array
			$locations_meta = get_post_meta( $post_id, 'psp_locations_meta', true );
			 
			// then build the html, and return it as string
			$html_business_information = $aaInterfaceTemplates->bildThePage( $this->business_information_options( $locations_meta ) , $this->the_plugin->alias, array(), false);
			
			$html_business_contact = $aaInterfaceTemplates->bildThePage( $this->business_contact_options( $locations_meta ) , $this->the_plugin->alias, array(), false);
			
			$html_opening_hours = $aaInterfaceTemplates->bildThePage( $this->opening_hours_options( $locations_meta ) , $this->the_plugin->alias, array(), false);
			
			$html_other_details = $aaInterfaceTemplates->bildThePage( $this->other_details_options( $locations_meta ) , $this->the_plugin->alias, array(), false);
		?>
			<link rel='stylesheet' href='<?php echo $this->module_folder;?>app.css' type='text/css' media='screen' />
			<?php /*<link rel='stylesheet' href='<?php echo $this->module_folder;?>jquery-ui-1.10.3.custom.min.css' type='text/css' media='screen' />
			<script type="text/javascript" src="<?php echo $this->module_folder;?>jquery-ui-1.10.3.custom.min.js"></script>*/ ?>
			<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
			
			<div id="psp-meta-box-preload" style="height:200px; position: relative;">
				<!-- Main loading box -->
				<div id="psp-main-loading" style="display:block;">
					<div id="psp-loading-box" style="top: 50px">
						<div class="psp-loading-text"><?php _e('Loading', 'psp');?></div>
						<div class="psp-meter psp-animate" style="width:86%; margin: 34px 0px 0px 7%;"><span style="width:100%"></span></div>
					</div>
				</div>
			</div>
			
			<div class="psp-meta-box-container" style="display:none;">
				<!-- box Tab Menu -->
				<div class="psp-tab-menu">
					<a href="#business_information" class="open"><?php _e('Business Information', 'psp');?></a>
					<a href="#business_contact"><?php _e('Business Contact and Google Map', 'psp');?></a>
					<a href="#opening_hours"><?php _e('Opening Hours', 'psp');?></a>
					<a href="#other_details"><?php _e('Other details', 'psp');?></a>
				</div>
				
				<div class="psp-tab-container">

					<div id="psp-tab-div-id-business_information" style="display:block;">
						<div class="psp-dashboard-box span_3_of_3">
							<!-- Creating the option fields -->
							<div class="psp-form">
								<?php echo $html_business_information;?>
							</div>
						</div>
					</div>
					
					<div id="psp-tab-div-id-business_contact" style="display:none;">
						<div class="psp-dashboard-box span_3_of_3">
							<!-- Creating the option fields -->
							<div class="psp-form">
								<?php echo $html_business_contact;?>
							</div>
						</div>
					</div>
					
					<div id="psp-tab-div-id-opening_hours" style="display:none;">
						<div class="psp-dashboard-box span_3_of_3">
							<!-- Creating the option fields -->
							<div class="psp-form">
								<?php echo $html_opening_hours;?>
							</div>
						</div>
					</div>
					
					<div id="psp-tab-div-id-other_details" style="display:none;">
						<div class="psp-dashboard-box span_3_of_3">
							<!-- Creating the option fields -->
							<div class="psp-form">
								<?php echo $html_other_details;?>
							</div>
						</div>
					</div>
					
				</div>
				<div style="clear:both"></div>
			</div>
		<?php
		}
		
		public function business_information_options( $defaults=array() )
		{
			if( !is_array($defaults) ) $defaults = array();
			
			$options = array(
				array(
					/* define the form_sizes  box */
					'location' => array(
						'title' 	=> 'Business Information Settings',
						'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
						'header' 	=> true, // true|false
						'toggler' 	=> false, // true|false
						'buttons' 	=> false, // true|false
						'style' 	=> 'panel', // panel|panel-widget
						
						// create the box elements array
						'elements'	=> array(
							'bname' => array(
								'type' 		=> 'text',
								'size' 		=> 'large',
								'title' 	=> __('Business name:', 'psp'),
								//'force_width' => '150',
								'std'		=> get_bloginfo('name'),
								'desc' 		=> 'Enter your business name',
							)
							
							,'btype' => array(
								'type' 		=> 'html',
								'html' 		=> $this->business_type_list('btype', isset($defaults['location']['btype']) ? $defaults['location']['btype'] : '')
							)
							
							,'description' => array(
								'type' 		=> 'textarea',
								'size' 		=> 'large',
								'title' 	=> __('Business description:', 'psp'),
								//'force_width' => '150',
								'std'		=> get_bloginfo('description'),
								'desc' 		=> 'Enter your business description (max. 150 characters)',
							)
							
							,'url' => array(
								'type' 		=> 'text',
								'size' 		=> 'large',
								'title' 	=> __('Business website URI:', 'psp'),
								//'force_width' => '150',
								'std'		=> get_bloginfo('url'),
								'desc' 		=> 'Enter your business website URI',
							)
							
							,'logo_image' => array(
								'type' 		=> 'upload_image',
								'size' 		=> 'large',
								'title' 	=> 'Business logo image',
								'value' 	=> 'Upload image',
								'thumbSize' => array(
									'w' => '100',
									'h' => '100',
									'zc' => '2',
								),
								'desc' 		=> 'Choose the logo image used for your businesss',
							)
							
							,'building_image' => array(
								'type' 		=> 'upload_image',
								'size' 		=> 'large',
								'title' 	=> 'Business building image',
								'value' 	=> 'Upload image',
								'thumbSize' => array(
									'w' => '100',
									'h' => '100',
									'zc' => '2',
								),
								'desc' 		=> 'Choose the image of your business building',
							)
							
							,'address' => array(
								'type' 		=> 'text',
								'size' 		=> 'large',
								'title' 	=> __('Street Address:', 'psp'),
								//'force_width' => '150',
								'std'		=> '',
								'desc' 		=> 'Enter your business address',
							)
							
							/*,'unit' => array(
								'type' 		=> 'text',
								'size' 		=> 'large',
								'title' 	=> __('Unit Number:', 'psp'),
								//'force_width' => '150',
								'std'		=> '',
								'desc' 		=> 'Enter your business address unit number (if you have one)',
							)*/
							
							,'city' => array(
								'type' 		=> 'text',
								'size' 		=> 'large',
								'title' 	=> __('City:', 'psp'),
								//'force_width' => '150',
								'std'		=> '',
								'desc' 		=> 'Enter your business city',
							)
							
							
							,'state' => array(
								'type' 		=> 'text',
								'size' 		=> 'large',
								'title' 	=> __('State / Region:', 'psp'),
								//'force_width' => '150',
								'std'		=> '',
								'desc' 		=> 'Enter your business state',
							)
							
							
							,'zipcode' => array(
								'type' 		=> 'text',
								'size' 		=> 'large',
								'title' 	=> __('Postal / Zip code:', 'psp'),
								//'force_width' => '150',
								'std'		=> '',
								'desc' 		=> 'Enter your business postal code / zip code',
							)
							
							,'country' => array(
								'type' 		=> 'select',
								'std' 		=> 'United States of America',
								'size' 		=> 'large',
								'force_width'=> '200',
								'title' 	=> __('Country:', 'psp'),
								'desc' 		=> 'Select your business country',
								'options'	=> $this->business_countries_list()
							)

						)
					)
				)
			);
			
			// setup the default value base on array with defaults
			if(count($defaults) > 0){
				foreach ($options as $option){
					foreach ($option as $box_id => $box){
						if(in_array($box_id, array_keys($defaults))){
							foreach ($box['elements'] as $elm_id => $element){
								if(isset($defaults[$box_id][$elm_id])){
									$option[$box_id]['elements'][$elm_id]['std'] = $defaults[$box_id][$elm_id];
								}
							}
						}
					}
				}
				
				// than update the options for returning
				$options = array( $option );
			}
			
			return $options;
		}
		
		public function business_contact_options( $defaults=array() )
		{
			if( !is_array($defaults) ) $defaults = array();
			
			$options = array(
				array(
					/* define the form_sizes  box */
					'location' => array(
						'title' 	=> 'Business Contact and Google Map Settings',
						'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
						'header' 	=> true, // true|false
						'toggler' 	=> false, // true|false
						'buttons' 	=> false, // true|false
						'style' 	=> 'panel', // panel|panel-widget
						
						// create the box elements array
						'elements'	=> array(
							'phone' => array(
								'type' 		=> 'text',
								'size' 		=> 'large',
								'title' 	=> __('Phone:', 'psp'),
								//'force_width' => '150',
								'std'		=> '',
								'desc' 		=> 'Enter your business phone',
							)
							
							,'phone_alt' => array(
								'type' 		=> 'text',
								'size' 		=> 'large',
								'title' 	=> __('Alternative Phone:', 'psp'),
								//'force_width' => '150',
								'std'		=> '',
								'desc' 		=> 'Enter your business alternative phone',
							)
							
							,'fax' => array(
								'type' 		=> 'text',
								'size' 		=> 'large',
								'title' 	=> __('Fax:', 'psp'),
								//'force_width' => '150',
								'std'		=> '',
								'desc' 		=> 'Enter your business fax',
							)
							
							,'email' => array(
								'type' 		=> 'text',
								'size' 		=> 'large',
								'title' 	=> __('Email:', 'psp'),
								//'force_width' => '150',
								'std'		=> '',
								'desc' 		=> 'Enter your business email',
							)
							
							,'map_name' => array(
								'type' 		=> 'text',
								'size' 		=> 'large',
								'title' 	=> __('Business name on the map:', 'psp'),
								//'force_width' => '150',
								'std'		=> '',
								'desc' 		=> 'Enter your Google Place or Business name',
							)
							
							/*,'map_domain' => array(
								'type' 		=> 'text',
								'size' 		=> 'large',
								'title' 	=> __('Google maps top level domain:', 'psp'),
								//'force_width' => '150',
								'std'		=> '',
								'desc' 		=> 'Top level domain for Google maps. For example: if you live in the USA, this would be "com" for maps.google.com. If you live in England, this would be "co.uk" for maps.google.co.uk',
							)*/
							
							,'map_latitude' => array(
								'type' 		=> 'text',
								'size' 		=> 'large',
								'title' 	=> __('Google Map Latitude:', 'psp'),
								//'force_width' => '150',
								'std'		=> '',
								'readonly'	=> true,
								'desc' 		=> 'retrieved automatically based on your business address',
							)
							
							,'map_longitude' => array(
								'type' 		=> 'text',
								'size' 		=> 'large',
								'title' 	=> __('Google Map Longitude:', 'psp'),
								//'force_width' => '150',
								'std'		=> '',
								'readonly'	=> true,
								'desc' 		=> 'retrieved automatically based on your business address',
							)
							
							,'map_preview' => array(
								'type' 		=> 'html',
								'html' 		=> $this->google_map_preview( array(
									'latitude' 		=> isset($defaults['location']['map_latitude']) ? $defaults['location']['map_latitude'] : '',
									'longitude' 	=> isset($defaults['location']['map_longitude']) ? $defaults['location']['map_longitude'] : ''
								) )
							)
							
						)
					)
				)
			);
			
			// setup the default value base on array with defaults
			if(count($defaults) > 0){
				foreach ($options as $option){
					foreach ($option as $box_id => $box){
						if(in_array($box_id, array_keys($defaults))){
							foreach ($box['elements'] as $elm_id => $element){
								if(isset($defaults[$box_id][$elm_id])){
									$option[$box_id]['elements'][$elm_id]['std'] = $defaults[$box_id][$elm_id];
								}
							}
						}
					}
				}
				
				// than update the options for returning
				$options = array( $option );
			}
			
			return $options;
		}
		
		public function other_details_options( $defaults=array() )
		{
			if( !is_array($defaults) ) $defaults = array();
			
			$options = array(
				array(
					/* define the form_sizes  box */
					'location' => array(
						'title' 	=> 'Other Details Settings',
						'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
						'header' 	=> true, // true|false
						'toggler' 	=> false, // true|false
						'buttons' 	=> false, // true|false
						'style' 	=> 'panel', // panel|panel-widget
						
						// create the box elements array
						'elements'	=> array(
							'payment_forms' => array(
								'type' 		=> 'text',
								'size' 		=> 'large',
								'title' 	=> __('Payment forms accepted:', 'psp'),
								//'force_width' => '150',
								'std'		=> '',
								'desc' 		=> 'ex: Cash, Check, Visa, Paypal',
							)
							
							,'payment_currency' => array(
								'type' 		=> 'text',
								'size' 		=> 'large',
								'title' 	=> __('Currencies accepted:', 'psp'),
								//'force_width' => '150',
								'std'		=> '',
								'desc' 		=> 'ex: USD, CAD, GBP (full list is on <a href="http://en.wikipedia.org/wiki/ISO_4217" target="_blank">Wikipedia</a>',
							)
							
							,'payment_price_range' => array(
								'type' 		=> 'text',
								'size' 		=> 'large',
								'title' 	=> __('Price range:', 'psp'),
								//'force_width' => '150',
								'std'		=> '',
								'desc' 		=> 'enter a range from $ to $$$$$ (used by Google listings)',
							)
							
						)
					)
				)
			);
			
			// setup the default value base on array with defaults
			if(count($defaults) > 0){
				foreach ($options as $option){
					foreach ($option as $box_id => $box){
						if(in_array($box_id, array_keys($defaults))){
							foreach ($box['elements'] as $elm_id => $element){
								if(isset($defaults[$box_id][$elm_id])){
									$option[$box_id]['elements'][$elm_id]['std'] = $defaults[$box_id][$elm_id];
								}
							}
						}
					}
				}
				
				// than update the options for returning
				$options = array( $option );
			}
			
			return $options;
		}
		
		public function opening_hours_options( $defaults=array() )
		{
			if( !is_array($defaults) ) $defaults = array();
			
			$options = array(
				array(
					/* define the form_sizes  box */
					'location' => array(
						'title' 	=> 'Opening Hours Settings',
						'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
						'header' 	=> true, // true|false
						'toggler' 	=> false, // true|false
						'buttons' 	=> false, // true|false
						'style' 	=> 'panel', // panel|panel-widget
						
						// create the box elements array
						'elements'	=> array(
							'oh_heading' => array(
								'type' 		=> 'text',
								'size' 		=> 'large',
								'title' 	=> __('Opening Hours Header:', 'psp'),
								//'force_width' => '150',
								'std'		=> '',
								'desc' 		=> 'ex: Cash, Check, Visa, Paypal',
							)
							
							,'openings_hours_msginfo' => array(
								'type' 		=> 'html',
								'html' 		=> '<div class="psp-form-row">You must use 24h format ( hours from 0-23, minutes from 0-59 ).</div>'
							)
							
							,'openings_hours' => array(
								'type' 		=> 'html',
								'html' 		=> $this->opening_hours_tpl( isset($defaults['location']['oh']) ? $defaults['location']['oh'] : '' )
							)
							
						)
					)
				)
			);
			
			// setup the default value base on array with defaults
			if(count($defaults) > 0){
				foreach ($options as $option){
					foreach ($option as $box_id => $box){
						if(in_array($box_id, array_keys($defaults))){
							foreach ($box['elements'] as $elm_id => $element){
								if(isset($defaults[$box_id][$elm_id])){
									$option[$box_id]['elements'][$elm_id]['std'] = $defaults[$box_id][$elm_id];
								}
							}
						}
					}
				}
				
				// than update the options for returning
				$options = array( $option );
			}
			
			return $options;
		}
		
		public function opening_hours_tpl( $openings=array() ) {
			ob_start();
			
			echo '<input type="hidden" name="psp-opening-nr" id="psp-opening-nr" value="' . ( count($openings) ) . '" />';
			?>
							<a class="psp-button blue small" id="psp-add-new-opening" href="#">
								<img alt="" src="<?php echo $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/local_seo/assets/plus.png';?>">
								Add new Opening
							</a>
			<?php
			echo '<div class="psp-form-row" id="psp-panel-content-dom">';
			if(is_array($openings) && count($openings) > 0) {
				foreach ($openings as $key => $value){
					$__theKey = '';
					//<div class='psp-oh-time-slider' style='width:100px;'></div>
					echo 			"<div class='psp-form-row'>
										<div class='psp-form-col-3-8'>
					" . $this->opening_hours_day( "oh[".$key."][day]", $value['day'] ) . "
										</div>
										<div class='psp-form-col-1-8'> 
											<label>From hour</label> 
											<input type='text' class='opening-from-hour' name='oh[".$key."][from_hour]' value='" . ( $value['from_hour'] ) . "'> 
										</div>
										<div class='psp-form-col-1-8'> 
											<label>From min</label> 
											<input type='text' class='opening-from-min' name='oh[".$key."][from_min]' value='" . ( $value['from_min'] ) . "'> 
										</div>
										<div class='psp-form-col-1-8'> 
											<label>To hour</label> 
											<input type='text' class='opening-to-hour' name='oh[".$key."][to_hour]' value='" . ( $value['to_hour'] ) . "'> 
										</div>
										<div class='psp-form-col-1-8'> 
											<label>To min</label> 
											<input type='text' class='opening-to-min' name='oh[".$key."][to_min]' value='" . ( $value['to_min'] ) . "'> 
										</div>
										<div class='psp-form-col-1-8' style='position: relative;'> 
											<a href='#' class='opening-delete-btn'>Delete</a> 
										</div> 
									</div>";
				}
			}

			// no answer yet
			else {
			?>
									<div class="psp-message psp-info" id="psp-opening-no-items">You currently have not entered any opening. You can do that by click on the <i>"Add new opening"</i> .</div>
			<?php 
			}
			echo '</div>';
			
			echo "
								<div id='psp-locations-opening-tpl' style='display:none;'>
									<div class='psp-form-row'>
										<div class='psp-form-col-3-8'>
										" . $this->opening_hours_day( '', '' ) . "
										</div>
										<div class='psp-form-col-1-8'>
											<label>From hour</label>
											<input type='text' class='opening-from-hour' name='' value=''>
										</div>
										<div class='psp-form-col-1-8'>
											<label>From min</label>
											<input type='text' class='opening-from-min' name='' value=''>
										</div>
										<div class='psp-form-col-1-8'>
											<label>To hour</label>
											<input type='text' class='opening-to-hour' name='' value=''>
										</div>
										<div class='psp-form-col-1-8'>
											<label>To min</label>
											<input type='text' class='opening-to-min' name='' value=''>
										</div>
										<div class='psp-form-col-1-8' style='position: relative;'>
											<a href='#' class='opening-delete-btn'>Delete</a>
										</div>
									</div>
								</div>
			";

			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}
		
		public function opening_hours_day( $field_name, $db_meta_name ) {
			ob_start();
			
			require($this->module_folder_path . 'lists.inc.php');
			
			$val = '';
			if( isset($db_meta_name) ){
				$val = $db_meta_name;
			}
		?>
				<label for='<?php echo $field_name; ?>' style=''><?php echo __('Day', 'psp'); ?>:</label>
				<select class='opening-day' name='<?php echo $field_name; ?>' style='height:29px;'>
					<?php
					foreach ($psp_days_list as $kk => $vv){
						echo 	'<option value="' . ( $kk ) . '" ' . ( $val == $kk ? 'selected="true"' : '' ) . '>' . ( $vv ) . '</option>';
					}
					?>
				</select>
		<?php
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}
		
		public function business_countries_list() {
			require($this->module_folder_path . 'lists.inc.php');

			return $psp_countries_list;
		}
		
		public function business_type_list( $field_name, $db_meta_name ) {
			ob_start();
			
			require($this->module_folder_path . 'lists.inc.php');
			
			$val = '';
			if( isset($db_meta_name) ){
				$val = $db_meta_name;
			}
		?>
		<div class="psp-form-row">
			<label><a href="http://schema.org/docs/full.html#LocalBusiness" target="_blank"><?php _e('Business type:', 'psp'); ?></a></label>
			<div class="psp-form-item large">
				<select id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" style="width:200px;">
					<?php
					foreach ($psp_business_type_list as $k => $v){
						echo '<optgroup label="' . $k . '">';
						foreach ($v as $kk => $vv){
							echo 	'<option value="' . ( $kk ) . '" ' . ( $val == $kk ? 'selected="true"' : '' ) . '>' . ( $vv ) . '</option>';
						}
						echo '</optgroup>';
					}
					?>
				</select>&nbsp;&nbsp;&nbsp;&nbsp;
			</div>
		</div>
		<?php
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}
		
		public function google_map_preview( $map_pms ) {
			ob_start();
			
			require($this->module_folder_path . 'lists.inc.php');
			
			$val = '';
			if( isset($db_meta_name) ){
				$val = $db_meta_name;
			}
		?>

		<script type="text/javascript" src="<?php echo $this->get_geo_uri_js(); ?>" ></script>
		<div class="psp-form-row">
			<label><?php _e('Google Map Preview:', 'psp'); ?>	</label>
			<!--<span class="formNote">You can verify latitude and longitude <a href="http://www.geo-tag.de/generator/en.html" target="_blank">here</a> (only if you think that automatically generated map by using Googles api from the address you've entered, isn't right)</span>-->
			<div class="psp-form-item large">
				<div id="psp-map-canvas"></div>
				<a name="psp-geocode-verify" href='#psp-geocode-verify' class='psp-geocode-verify'>Verify map</a>
				<div class="psp-geocode-status" style="display: none;"></div>
			</div>
		</div>
		<?php
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}

		protected function get_geo_uri_js() {
			return self::$geo_uri_js;
		}

		public function get_geo_location( $location=array(), $output='xml' )
		{
			$ret = array('latitude' => '', 'longitude' => '');
			
			$address = array();
			if ( !empty($location['address']) )
				$address[] = $location['address'];
			//if ( !empty($location['unit']) )
			//	$address[] = $location['unit'];
			if ( !empty($location['city']) )
				$address[] = $location['city'] . ', ';
			if ( !empty($location['state']) )
				$address[] = $location['state'];
			if ( !empty($location['zipcode']) )
				$address[] = $location['zipcode'];
			if ( !empty($location['country']) )
				$address[] = $location['country'];

			$address = implode(' ', $address);
			$address = trim($address);
			$address = rtrim($address, ',');
			$address = ltrim($address, ',');
			
			if ( empty($address) ) return $ret;
			
			$uri = self::$geo_uri;
			$uri = str_replace('{output}', $output, $uri);
			$uri = str_replace('{address}', rawurlencode($address), $uri);

			if( ini_get('allow_url_fopen') ) {
				$gresp = simplexml_load_file( $uri );
			} else {
				$curl = curl_init( $uri );
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$data = curl_exec( $uri );
				$gresp = simplexml_load_string($data);
			}
        
			if ( !$gresp ) return $ret;
			if ( $gresp->status != 'OK' ) return $ret;

			$latitude = (string) $gresp->result->geometry->location->lat;
			$longitude = (string) $gresp->result->geometry->location->lng;
			$ret = array_merge($ret, array('latitude' => $latitude, 'longitude' => $longitude));
			return $ret;
		}
		
		
		public function locations_edit_columns($locations_columns) {
		    $new_columns['cb'] 						= '<input type="checkbox" />';
		    $new_columns['locations_id'] 			= __('ID', 'psp');
		    $new_columns['locations_thumbnail'] 	= __('Image', 'psp');
		    $new_columns['title'] 					= __('Title', 'psp');
		    $new_columns['date'] 					= __('Date', 'psp');
		
		    return $new_columns;
		}
		
		public function locations_posts_columns($column_name, $id) {
		    global $id;
			
			// retrieve the existing value(s) for this meta field. This returns an array
			$locations_meta = get_post_meta( $id, 'psp_locations_meta', true );
			$locations_meta = $locations_meta;
		    switch ($column_name) {
				case 'locations_id':
		            echo $id;
		            break;
		        case 'locations_thumbnail':
					$thumb = $this->the_plugin->image_resize( $locations_meta['location']['logo_image'], 80, 80, 1 );
					echo '<img src="' . ( $thumb ) . '" width="80" style="border: 1px solid #ff;">';
		            break;
		        default:
		            break;
		    } // end switch
		}
		
		/* when the post is saved, save the custom data */
		public function meta_box_save_postdata( $post_id ) 
		{
			global $post;
			
			if( isset($post) ) {
				// do not save if this is an auto save routine
				if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
					return $post_id;
					
				if($post->post_type == 'psp_locations') {

					$locations_meta = array();

					$options = array();
					$tmp1 =  $this->business_information_options();
					$options = array_merge_recursive( $options, reset($tmp1) );
					$tmp2 = $this->business_contact_options();
					$options = array_merge_recursive( $options, reset($tmp2) );
					$tmp3 = $this->opening_hours_options();
					$options = array_merge_recursive( $options, reset($tmp3) );
					$tmp4 = $this->other_details_options();
					$options = array_merge_recursive( $options, reset($tmp4) );

					foreach ($options as $box_id => $box){
						foreach ($box['elements'] as $elm_id => $element){

							if ( $element['type'] == 'html'
								&& !in_array($elm_id, array('btype')) ) {

								continue 1;
							}
							$locations_meta[$box_id][$elm_id] = $_POST[$elm_id];
						}
					}

					$nrOfOpenings = (int) $_POST['psp-opening-nr'];
					if($nrOfOpenings > 0) {
						$openings = array();
						for ($i=1; $i<= $nrOfOpenings; $i++) { 
							$openings[$i] = array(
								'day' => isset($_POST['oh'][$i]['day']) ? $_POST['oh'][$i]['day'] : '',
								'from_hour' => isset($_POST['oh'][$i]['from_hour']) ? $_POST['oh'][$i]['from_hour'] : '',
								'from_min' => isset($_POST['oh'][$i]['from_min']) ? $_POST['oh'][$i]['from_min'] : '',
								'to_hour' => isset($_POST['oh'][$i]['to_hour']) ? $_POST['oh'][$i]['to_hour'] : '',
								'to_min' => isset($_POST['oh'][$i]['to_min']) ? $_POST['oh'][$i]['to_min'] : ''
							);
						}
						$locations_meta['location']['oh'] = $openings;
					}
					
					// get current & rewrite geocode location
					$latlng = $this->get_geo_location( $locations_meta['location'] );
					$locations_meta['location']['map_latitude'] = $latlng['latitude'];
					$locations_meta['location']['map_longitude'] = $latlng['longitude'];
					
					update_post_meta( $post_id, 'psp_locations_meta', $locations_meta );
				}
			}
		}


		/**
	    * Singleton pattern
	    *
	    * @return pspLocalSEO Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }
	        
			if ( self::$_instance->the_plugin->is_admin === true ) {
	        	add_action( 'admin_init', array( self::$_instance, '__instanceActions' ) );
			}
	        
	        return self::$_instance;
	    }
	    
	    public function __instanceActions() {

			// change the layout of locations list
	    	$screens = array('psp_locations');
		    foreach ($screens as $screen) {

				add_filter( 'manage_edit-' . $screen . '_columns', array( &$this, 'locations_edit_columns' ), 10, 1 );
				//add_filter( 'manage_' . $screen . '_posts_columns', array( $this, 'locations_edit_columns' ), 10, 1 );
				add_action( 'manage_' . $screen . '_posts_custom_column', array( $this, 'locations_posts_columns' ), 10, 2 );
		    }
	    }
		
		public function extra_css() 
		{
		    echo "
		        <style type='text/css'>
		        
		        th#locations_id {width: 40px;}
		        th#locations_thumbnail {width: 100px;}
		        th#date {width: 190px;}
		        </style>
			";
		}
    }
}

// Initialize the pspLocalSEO class
//$pspLocalSEO = new pspLocalSEO();
$pspLocalSEO = pspLocalSEO::getInstance();

// shortcodes
require_once( 'sitemap_and_shortcodes.php' );