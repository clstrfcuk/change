<?php
/*
* Define class aaInterfaceTemplates
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
! defined( 'ABSPATH' ) and exit;

if(class_exists('aaInterfaceTemplates') != true) {

	class aaInterfaceTemplates {

		/*
		* Some required plugin information
		*/
		const VERSION = '1.0';

		/*
		* Store some helpers config
		*
		*/
		public $cfg	= array();

		/*
		* Required __construct() function that initalizes the AA-Team Framework
		*/
		public function __construct($cfg)
		{
			global $psp;
			$this->cfg = $cfg;
		}


		/*
		* bildThePage, method
		* -------------------
		*
		* @params $options = array (requiered)
		* @params $alias = string (requiered)
		* this will create you interface via options array elements
		*/
		public function bildThePage ( $options = array(), $alias='', $module=array(), $showForm=true )
		{
			global $psp;

			// reset as array, this will stock all the html content, and at the end return it
			$html = array();

			if(count($options) == 0) {
				return __('Please fill whit some options content first!', $psp->localizationName);
			}

			$noRowElements = array('message', 'html', 'app');

			foreach ( $options as $theBoxs ) {

				// loop the all the boxs
				foreach ( $theBoxs as $box_id => $box ){

					$box_id = $alias . "_" . $box_id;
					$settings = array();

					// get the values from DB
					$dbValues = get_option($box_id);

					// check if isset and string have content
					if(isset($dbValues) && @trim($dbValues) != ""){
						$settings = maybe_unserialize($dbValues);
					}

					// create defalt setup for each header, prevent php notices
					if(!isset($box['header'])) $box['header']= false;
					if(!isset($box['toggler'])) $box['toggler']= false;
					if(!isset($box['buttons'])) $box['buttons']= false;
					if(!isset($box['style'])) $box['style']= 'panel';

					// container setup
					$html[] = '<div class="psp-' . ( $box['size'] ) . '">
                        	<div class="psp-' . ( $box['style'] ) . '">';

					// hide panel header only if it's requested
					if( $box['header'] == true ) {
						$html[] = '<div class="psp-panel-header">
							<span class="psp-panel-title">
								' . ( isset($box['icon']) ? '<img src="' . ( $box['icon'] ) . '" />' : '' ) . '
								' . ( $box['title'] ) . '
							</span>
							 ' . ( $box['toggler'] == true ? '<span class="psp-panel-toggler"></span>' : '' ) . '
						</div>';
					}

					$html[] = '<div class="psp-panel-content">';
					if($showForm){
						$html[] = '<form class="psp-form" id="' . ( $box_id ) . '" action="#save_with_ajax">';
					}

					// create a hidden input for sending the prefix
					$html[] = '<input type="hidden" id="box_id" name="box_id" value="' . ( $box_id ) . '" />';

					$html[] = '<input type="hidden" id="box_nonce" name="box_nonce" value="' . ( wp_create_nonce( $box_id . '-nonce') ) . '" />';
					
					$html[] = $this->tabsHeader($box); // tabs html header
					$html[] = $this->subtabsHeader($box); // subtabs html header
					
					// loop the box elements
					if(count($box['elements']) > 0){

						// loop the box elements now
						foreach ( $box['elements'] as $elm_id => $value ){

							// some helpers. Reset an each loop, prevent collision
							$val = '';
							$select_value = '';
							$checked = '';
							$option_name = isset($option_name) ? $option_name : '';

							// Set default value to $val 
							if ( isset( $value['std']) ) {
								$val = $value['std'];
							}

							// If the option is already saved, ovveride $val
							if ( ( $value['type'] != 'info' ) ) {
								if ( isset($settings[($elm_id)] )
									&& (
										( !is_array($settings[($elm_id)]) && @trim($settings[($elm_id)]) != "" )
										||
										( is_array($settings[($elm_id)]) /*&& !empty($settings[($elm_id)])*/ )
									)
								) {
										$val = $settings[( $elm_id )];

										// Striping slashes of non-array options
										if ( !is_array($val) ) {
											$val = stripslashes( $val );
											//if($val == '') $val = true;
										}
								}
							}

							// If there is a description save it for labels
							$explain_value = '';
							if ( isset( $value['desc'] ) ) {
								$explain_value = $value['desc'];
							}

							if(!in_array( $value['type'], $noRowElements)){
								// the row and the label
								$html[] = '<div class="psp-form-row' . ($this->tabsElements($box, $elm_id)) . '">
									   <label for="' . ( $elm_id ) . '">' . ( isset($value['title']) ? $value['title'] : '' ) . '</label>
									   <div class="psp-form-item'. ( isset($value['size']) ? " " . $value['size'] : '' ) .'">';
							}

							// the element description
							if(isset($value['desc'])) $html[]	= '<span class="formNote">' . ( $value['desc'] ) . '</span>';

							switch ( $value['type'] ) {

								// Basic text input
								case 'text':
									$html[] = '<input ' . ( isset($value['readonly']) && $value['readonly'] == true ? 'readonly ' : '' ) . ' ' . ( isset($value['force_width']) ? "style='width:" . ( $value['force_width'] ) . "px;'" : '' ) . ' id="' . esc_attr( $elm_id ) . '" name="' . esc_attr( $option_name . $elm_id ) . '" type="text" value="' . esc_attr( $val ) . '" />';
								break;

								// Basic checkbox input
								case 'checkbox':
									if($val == '') $val = true;
									$html[] = '<input ' . ( isset($value['force_width']) ? "style='width:" . ( $value['force_width'] ) . "px;'" : '' ) . ' ' . ( $val == true ? 'checked' : '' ). ' id="' . esc_attr( $elm_id ) . '" name="' . esc_attr( $option_name . $elm_id ) . '" type="checkbox" value="" />';
								break;

								// Basic upload_image
								case 'upload_image':
									$html[] = '<table border="0">';
									$html[] = '<tr>';
									$html[] = 	'<td>';
									$html[] = 		'<input class="upload-input-text" name="' . ( $elm_id ) . '" id="' . ( $elm_id ) . '_upload" type="text" value="' . ( $val ) . '" />';

									$html[] = 		'<script type="text/javascript">
										jQuery("#' . ( $elm_id ) . '_upload").data({
											"w": ' . ( $value['thumbSize']['w'] ) . ',
											"h": ' . ( $value['thumbSize']['h'] ) . ',
											"zc": ' . ( $value['thumbSize']['zc'] ) . '
										});
									</script>';

									$html[] = 	'</td>';
									$html[] = '<td>';
									$html[] = 		'<a href="#" class="button upload_button" id="' . ( $elm_id ) . '">' . ( $value['value'] ) . '</a> ';
									//$html[] = 		'<a href="#" class="button reset_button ' . $hide . '" id="reset_' . ( $elm_id ) . '" title="' . ( $elm_id ) . '">Remove</a> ';
									$html[] = '</td>';
									$html[] = '</tr>';
									$html[] = '</table>';

									$html[] = '<a class="thickbox" id="uploaded_image_' . ( $elm_id ) . '" href="' . ( $val ) . '" target="_blank">';

									if(!empty($val)){
										$imgSrc = $psp->image_resize( $val, $value['thumbSize']['w'], $value['thumbSize']['h'], $value['thumbSize']['zc'] );
										$html[] = '<img style="border: 1px solid #dadada;" id="image_' . ( $elm_id ) . '" src="' . ( $imgSrc ) . '" />';
									}
									$html[] = '</a>';
									
									$html[] = 		'<script type="text/javascript">
										psp_loadAjaxUpload( jQuery("#' . ( $elm_id ) . '") );
									</script>';

								break;
								
								// Basic upload_image
								case 'upload_image_wp':
									$preview_size = (isset($value['preview_size']) ? $value['preview_size'] : 'thumbnail');
									if( (int) $val > 0 ){
										$image = wp_get_attachment_image_src( $val, $preview_size );
										$image_full = wp_get_attachment_image_src( $val, 'full' );
										if( count($image) > 0 ){
											$image = $image[0];
										}
										
										if( count($image_full) > 0 ){
											$image_full = $image_full[0];
										}
									}
									
									$html[] = '<div class="psp-upload-image-wp-box">';
									$html[] = 	'<a data-previewsize="' . ( $preview_size ) . '" class="upload_image_button_wp psp-button blue" ' . ( isset($value['force_width']) ? "style='" . ( trim($val) != "" ? 'display:none;' : '' ) . "width:" . ( $value['force_width'] ) . "px;'" : '' ) . ' href="#">' . ( $value['value'] ) . '</a>';
									$html[] = 	'<input type="hidden" name="' . ( $elm_id ) . '" value="' . ( $val ) . '">';
									$html[] = 	'<a href="' . ( $image_full ) . '" target="_blank" class="upload_image_preview" style="display: ' . ( trim($val) == "" ? 'none' : 'block' ). '">';
									$html[] = 		'<img src="' . ( $image ) . '" style="display: ' . ( trim($val) == "" ? 'none' : 'inline-block' ). '">';	
									$html[] = 	'</a>';
									$html[] =	'<div class="psp-prev-buttons" style="display: ' . ( trim($val) == "" ? 'none' : 'inline-block' ). '">';
									$html[] = 		'<span class="change_image_button_wp psp-button green">Change Image</span>';
									$html[] = 		'<span class="remove_image_button_wp psp-button red">Remove Image</span>';
									$html[] =	'</div>';
									$html[] = '</div>';
								break;

								// Basic textarea
								case 'textarea-array':
									$textType = 'array';
								case 'textarea':
									$cols = "120";
									if(isset($value['cols'])) {
										$cols = $value['cols'];
									}
									$height = "style='height:120px;'";
									if(isset($value['height'])) {
										$height = "style='height:{$value['height']};'";
									}
									
  									if ( isset($textType) && $textType == 'array' )
  										$val = var_export($val, true);
									$val = esc_attr( $val );
  
									$html[] = '<textarea id="' . esc_attr( $elm_id ) . '" ' . $height . ' cols="' . ( $cols ) . '" name="' . esc_attr( $option_name . $elm_id ) . '">' . $val . '</textarea>';
								break;

								// Basic html/text message
								case 'message':
									$html[] = '<div class="psp-message psp-' . ( isset($value['status']) ? $value['status'] : '' ) . ' ' . ($this->tabsElements($box, $elm_id)) . '">' . ( $value['html'] ) . '</div>';
								break;

								// buttons
								case 'buttons':

									// buttons for each box

									if(count($value['options']) > 0){
										foreach ($value['options'] as $key => $value){
											$html[] = '<input
												type="' . ( isset($value['type']) ? $value['type'] : '' ) . '"
												style="width:' . ( isset($value['width']) ? $value['width'] : '' ) . '"
												value="' . ( isset($value['value']) ? $value['value'] : '' ) . '"
												class="psp-button ' . ( isset($value['color']) ? $value['color'] : '' ) . ' ' . ( isset($value['pos']) ? $value['pos'] : '' ) . ' ' . ( isset($value['action']) ? $value['action'] : '' ) . '"
											/>';
										}
									}

								break;


								// Basic html/text message
								case 'html':
									$html[] = $value['html'];
								break;

								// Basic app, load the path of this file
								case 'app':

									$tryLoadInterface = str_replace("{plugin_folder_path}", $module["folder_path"], $value['path']);

									if(is_file($tryLoadInterface)) {
										// Turn on output buffering
										ob_start();

										require_once( $tryLoadInterface  );

										//copy current buffer contents into $message variable and delete current output buffer
										$html[] = ob_get_clean();
									}
								break;

								// Select Box
								case 'select':
									$html[] = '<select ' . ( isset($value['force_width']) ? "style='width:" . ( $value['force_width'] ) . "px;'" : '' ) . ' name="' . esc_attr( $elm_id ) . '" id="' . esc_attr( $elm_id ) . '">';

									foreach ($value['options'] as $key => $option ) {
										$selected = '';
										if( $val != '' ) {
											if ( $val == $key || $val == $option ) { $selected = ' selected="selected"';}
										}
										$html[] = '<option'. $selected .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
									 }
									$html[] = '</select>';
								break;

								// multiselect Box
								case 'multiselect':
									$html[] = '<select ' . ( isset($value['force_width']) ? "style='width:" . ( $value['force_width'] ) . "px;'" : '' ) . ' multiple="multiple" size="6" name="' . esc_attr( $elm_id ) . '[]" id="' . esc_attr( $elm_id ) . '">';

									if(count($value['options']) > 0){
										foreach ($value['options'] as $key => $option ) {
											$selected = '';
											if( $val != '' ) {
												if ( in_array($key, $val) ) { $selected = ' selected="selected"';}
											}
											$html[] = '<option'. $selected .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
										}
									}
									$html[] = '</select>';
								break;
								
								// multiselect Box
								case 'multiselect_left2right':

									$available = array(); $selected = array();
									foreach ($value['options'] as $key => $option ) {
										if( $val != '' ) {
											if ( in_array($key, $val) ) { $selected[] = $key; } 
										}
									}
									$available = array_diff(array_keys($value['options']), $selected);
									
									$html[] = '<div class="psp-multiselect-half psp-multiselect-available' . ( isset($value['cssclass']) && !empty($value['cssclass']) ? ' ' . $value['cssclass'] . '' : '' ) . '" style="margin-right: 2%;">';
									if( isset($value['info']['left']) ){
										$html[] = '<h5>' . ( $value['info']['left'] ) . '</h5>';
									}
									$html[] = '<select multiple="multiple" size="' . (isset($value['rows_visible']) ? $value['rows_visible'] : 5) . '" name="' . esc_attr( $elm_id ) . '-available[]" id="' . esc_attr( $elm_id ) . '-available" class="multisel_l2r_available">';
									
									if(count($available) > 0){
										foreach ($value['options'] as $key => $option ) {
											if ( !in_array($key, $available) ) continue 1;
											$html[] = '<option value="' . esc_attr( $key ) . '" title="'.esc_html( $option ).'" alt="'.esc_html( $option ).'">' . esc_html( $option ) . '</option>';
										} 
									}
									$html[] = '</select>';
									
									$html[] = '</div>';
									
									$html[] = '<div class="psp-multiselect-half psp-multiselect-selected' . ( isset($value['cssclass']) && !empty($value['cssclass']) ? ' ' . $value['cssclass'] . '' : '' ) . '">';
									if( isset($value['info']['right']) ){
										$html[] = '<h5>' . ( $value['info']['right'] ) . '</h5>';
									}
									$html[] = '<select multiple="multiple" size="' . (isset($value['rows_visible']) ? $value['rows_visible'] : 5) . '" name="' . esc_attr( $elm_id ) . '[]" id="' . esc_attr( $elm_id ) . '" class="multisel_l2r_selected">';
									
									if(count($selected) > 0){
										foreach ($value['options'] as $key => $option ) {
											if ( !in_array($key, $selected) ) continue 1;
											$isselected = ' selected="selected"'; 
											$html[] = '<option'. $isselected .' value="' . esc_attr( $key ) . '" title="'.esc_html( $option ).'" alt="'.esc_html( $option ).'">' . esc_html( $option ) . '</option>';
										} 
									}
									$html[] = '</select>';
									$html[] = '</div>';
									$html[] = '<div style="clear:both"></div>';
									$html[] = '<div class="multisel_l2r_btn' . ( isset($value['cssclass']) && !empty($value['cssclass']) ? ' ' . $value['cssclass'] . '' : '' ) . '" style="">';
									$html[] = '<span style="display: inline-block; width: 24.1%; text-align: center;"><input id="' . esc_attr( $elm_id ) . '-moveright" type="button" value="Move Right" class="moveright psp-button gray"></span>';
									$html[] = '<span style="display: inline-block; width: 24.1%; text-align: center;"><input id="' . esc_attr( $elm_id ) . '-moverightall" type="button" value="Move Right All" class="moverightall psp-button gray"></span>';
									$html[] = '<span style="display: inline-block; width: 24.1%; text-align: center;"><input id="' . esc_attr( $elm_id ) . '-moveleft" type="button" value="Move Left" class="moveleft psp-button gray"></span>';
									$html[] = '<span style="display: inline-block; width: 24.1%; text-align: center;"><input id="' . esc_attr( $elm_id ) . '-moveleftall" type="button" value="Move Left All" class="moveleftall psp-button gray"></span>';
									$html[] = '</div>';
								break;
								
								// Basic authorization facebook button
								case 'authorization_button':
   
									// load the facebook SDK
									require_once( $this->cfg['paths']['scripts_dir_path'] . '/facebook/facebook.php' );
								
									$fb_details = $psp->getAllSettings('array', 'facebook_planner');

									if( (isset($fb_details['app_id']) && trim($fb_details['app_id']) != '') && ( isset($fb_details['app_secret']) && trim($fb_details['app_secret']) != '') ) {
										$facebook = new psp_Facebook(array(
											'appId'  => $fb_details['app_id'],
											'secret' => $fb_details['app_secret']
										));
									} 
									
									// publish_actions instead of publish_stream
									if( isset($facebook) ) {
										$validAuth = false;
										$state = isset($_REQUEST['state']) ? trim($_REQUEST['state']) : '';
										$dbToken = get_option('psp_fb_planner_token');
										
										if(trim($dbToken) != "" && $state == "") {
											$facebook->setAccessToken($dbToken);
											
											try {
												// get user profile
												$uid = $facebook->getUser();
												$user_profile = $facebook->api('/'.$uid);
												//$user_profile = $facebook->api('/me');
												
												if(count($user_profile) > 0){
													$validAuth = true;
													
													$html[] = '<p>This plugin is <b>authorized</b> for: <a target="_blank" href="' . ( $user_profile['link'] ) . '">' . $user_profile['name'] . '</a></p>';
													
													// login url
													$loginUrl = $facebook->getLoginUrl(
														array(
														'scope' => 'email,publish_actions,manage_pages,user_groups,offline_access',
														'redirect_uri' => admin_url('admin-ajax.php?action=psp_facebookAuth')
														)
													);
						
													$html[] = '<a href="' . ($loginUrl) . '" style="width: 133px;" class="psp-button blue">'. (__( 'Authorize this app again', $psp->localizationName )) .'</a>';
												}
												
											} catch (psp_FacebookApiException $e) {
												
												// clean token
												//update_option('psp_fb_planner_token', $token);
											}
										}
								
										if( $validAuth == false ) {
											// login url
											$loginUrl = $facebook->getLoginUrl(
												array(
													'scope' => 'email,publish_actions,user_groups,manage_pages,offline_access',
													'redirect_uri' => admin_url('admin-ajax.php?action=psp_facebookAuth')
												)
											);

											$html[] = '<a href="' . ($loginUrl) . '" style="width: 84px;" type="button" class="psp-button blue">'. (__( 'Authorizate app', $psp->localizationName )) .'</a>';
										}
									}
								break;
								
								case 'date':

									$html[] = '<input ' . ( isset($value['readonly']) && $value['readonly'] == true ? 'readonly ' : '' ) . ' ' . ( isset($value['force_width']) ? "style='width:" . ( $value['force_width'] ) . "px;'" : '' ) . ' id="' . esc_attr( $elm_id ) . '" name="' . esc_attr( $option_name . $elm_id ) . '" type="text" value="' . esc_attr( $val ) . '" />';
									$html[] = '<input type="hidden" id="' . esc_attr( $elm_id ) . '-format" value="" />';
									
									$defaultDate = '';
									if ( isset($value['std']) && !empty($value['std']) )
										$defaultDate = $value['std'];
									if ( isset($value['defaultDate']) && !empty($value['defaultDate']) )
										$defaultDate = $value['defaultDate'];
										
									$html[] = "<script type='text/javascript'>
										jQuery(document).ready(function($){
										 	// datepicker
										 	var atts = {
												changeMonth:	true,
												changeYear:		true,
												onClose: function() {
													$('input#" . ( $elm_id ) . "').trigger('change');
												}
											};
											atts.dateFormat 	= '" . ( isset($value['format']) && !empty($value['format']) ? $value['format'] : 'yy-mm-dd' ) . "';
											atts.defaultDate 	= '" . ( isset($defaultDate) && !empty($defaultDate) ? $defaultDate : null ) . "';
											atts.altField		= 'input#" . ( $elm_id ) . "-format';
											atts.altFormat		= 'yy-mm-dd';";

									if ( isset($value['yearRange']) && !empty($value['yearRange']) )
										$html[] = "atts.yearRange	= '" . $value['yearRange'] . "';";

									$html[] = "$( 'input#" . ( $elm_id ) . "' ).datepicker( atts ); // end datepicker
										});
									</script>";

									break;

								case 'time':

									$__hourmin_init = array();
									if ( isset($value['std']) && !empty($value['std']) )
										$__hourmin_init = $this->getTimeDefault( $value['std'] );
									if ( isset($value['defaultDate']) && !empty($value['defaultDate']) )
										$__hourmin_init = $this->getTimeDefault( $value['defaultDate'] );
										
									$__hour_range = array();
									if ( isset($value['hour_range']) && !empty($value['hour_range']) )
										$__hour_range = $this->getTimeDefault( $value['hour_range'] );
										
									$__min_range = array();
									if ( isset($value['min_range']) && !empty($value['min_range']) )
										$__min_range = $this->getTimeDefault( $value['min_range'] );
									
									$html[] = '<input ' . ( isset($value['readonly']) && $value['readonly'] == true ? 'readonly ' : '' ) . ' ' . ( isset($value['force_width']) ? "style='width:" . ( $value['force_width'] ) . "px;'" : '' ) . ' id="' . esc_attr( $elm_id ) . '" name="' . esc_attr( $option_name . $elm_id ) . '" type="text" value="' . esc_attr( $val ) . '" />';
									
									$html[] = "<script type='text/javascript'>
										jQuery(document).ready(function($){
										 	// timepicker
										 	var atts = {};";

									if ( isset($value['ampm']) && ( $value['ampm'] || $value['ampm'] == 'true' ) )
										$html[] = "atts.ampm	= true;";
									else 
										$html[] = "atts.ampm	= false;";

									if ( isset($__hourmin_init) && !empty($__hourmin_init) )
										$html[] = "atts.defaultValue	= '" . $value['std'] . "';";

									if ( isset($__hourmin_init) && !empty($__hourmin_init) )
										$html[] = "atts.hour	= " . $__hourmin_init[0] . ";";
									if ( isset($__hourmin_init) && !empty($__hourmin_init) )
										$html[] = "atts.minute	= " . $__hourmin_init[1] . ";";
									if ( isset($__hour_range) && !empty($__hour_range) )
										$html[] = "atts.hourMin	= " . $__hour_range[0] . ";";
									if ( isset($__hour_range) && !empty($__hour_range) )
										$html[] = "atts.hourMax	= " . $__hour_range[1] . ";";
									if ( isset($__min_range) && !empty($__min_range) )
										$html[] = "atts.minuteMin	= " . $__min_range[0] . ";";
									if ( isset($__min_range) && !empty($__min_range) )
										$html[] = "atts.minuteMax	= " . $__min_range[1] . ";";

									$html[] = "$( 'input#" . ( $elm_id ) . "' ).timepicker( atts ); // end timepicker
										});
									</script>";

									break;
									
								case 'ratestar':

									$html[] = '<input id="' . esc_attr( $elm_id ) . '" name="' . esc_attr( $option_name . $elm_id ) . '" type="hidden" value="' . esc_attr( $val ) . '" />';
									$html[] = '<div id="rateit-' . esc_attr( $elm_id ) . '"></div>';
									$html[] = "<script type='text/javascript'>
											 jQuery(document).ready(function($){
												$('#rateit-" . ( $elm_id ) . "').rateit({ max: " . ( isset($value['nbstars']) && !empty($value['nbstars']) ? $value['nbstars'] : 10 ) . ", step: 1, backingfld: '#" . ( $elm_id ) . "' });
											});
									</script>";

									break;

							}

							if( !in_array( ( isset($value['type']) ? $value['type'] : '' ) , $noRowElements)){
								// close: .psp-form-row
								$html[] = '</div>';

								// close: .psp-form-item
								$html[] = '</div>';
							}

						}
					}

					// psp-message use for status message, default it's hidden
					$html[] = '<div class="psp-message" id="psp-status-box" style="display:none;"></div>';

					if( $box['buttons'] == true && !is_array($box['buttons']) ) {
						// buttons for each box
						$html[] = '<div class="psp-button-row">
							<input type="reset" value="' . __('Reset to default value', $psp->localizationName) . '" class="psp-button gray left" />
							<input type="submit" value="' . __('Save the settings', $psp->localizationName) . '" class="psp-button green psp-saveOptions" />
						</div>';
					}
					elseif( is_array($box['buttons']) ){
						// buttons for each box
						$html[] = '<div class="psp-button-row">';

						foreach ( $box['buttons'] as $key => $value ){
							$html[] = '<input type="submit" value="' . ( $value['value'] ) . '" class="psp-button ' . ( $value['color'] ) . ' ' . ( $value['action'] ) . '" />';
						}

						$html[] = '</div>';
					}

					if($showForm){
						// close: form
						$html[] = '</form>';
					}

					// close: box size div
					$html[] = '</div>';

					// close: .psp-panel
					$html[] = '</div>';

					// close: .psp-panel-content
					$html[] = '</div>';
				}
			}

			// return the $html
			return implode("\n", $html);
		}

		/*
		* printBaseInterface, method
		* --------------------------
		*
		* this will add the base DOM code for you options interface
		*/
		public function printBaseInterface( $pluginPage='' )
		{
?>
		<div id="psp-wrapper" class="fluid wrapper-psp">

			<!-- Header -->
			<?php
			// show the top menu
			pspAdminMenu::getInstance()->show_menu( $pluginPage );
			?>

			<!-- Content -->
			<div id="psp-content">
				<h1 class="psp-section-headline"></h1>

				<!-- Container -->
				<div class="psp-container clearfix">

					<!-- Main Content Wrapper -->
					<div id="psp-content-wrap" class="clearfix">

						<!-- Content Area -->
						<div id="psp-content-area">
							<!-- Content Area -->
							<div id="psp-ajax-response"></div>

							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

<?php
		}
		
		//make Tabs!
		private function tabsHeader($box, $parent_tab='', $single_tab=true) {
			$html = array();
			
			$__allowLimit = 0;
			if ( isset($single_tab) && !$single_tab ) $__allowLimit = 1;
			
			// get tabs
			$__tabs = isset($box['tabs']) ? $box['tabs'] : array();
			if ( !empty($parent_tab) )
				$__tabs = isset($box['subtabs']["$parent_tab"]) ? $box['subtabs']["$parent_tab"] : array();

			$__ret = '';
			if (is_array($__tabs) && count($__tabs)>$__allowLimit) {
				$html[] = '<ul class="' . (!empty($parent_tab) ? 'subtabsHeader ' . $parent_tab : 'tabsHeader') . '" data-parent="' . $parent_tab . '">';
				$html[] = '<li style="display:none;" class="tabsCurrent" ' . (/*!empty($parent_tab) ? 'class="tabsCurrent"' : 'id="tabsCurrent"'*/'') . ' title=""></li>'; //fake li with the current tab value!
				foreach ($__tabs as $tabClass=>$tabElements) {
					$html[] = '<li><a href="javascript:void(0);" title="'.$tabClass.'">'.$tabElements[0].'</a></li>';
				}
				$html[] = '</ul>';
				$__ret = implode('', $html);
				
			}
			return $__ret;
		}
		
		private function tabsElements($box, $elemKey, $parent_tab=false) {
			
			$__allowLimit = 0;
			if ( isset($single_tab) && !$single_tab ) $__allowLimit = 1;

			// get tabs
			$__tabs = isset($box['tabs']) ? $box['tabs'] : array();
			if ( !empty($parent_tab) )
				$__tabs = isset($box['subtabs']["$parent_tab"]) ? $box['subtabs']["$parent_tab"] : array();

			$__ret = '';
			if (is_array($__tabs) && count($__tabs)>$__allowLimit) {
				foreach ($__tabs as $tabClass=>$tabElements) {

					$tabElements = $tabElements[1];
					$tabElements = trim($tabElements);
					$tabElements = array_map('trim', explode(',', $tabElements));
					if (in_array($elemKey, $tabElements)) 
						$__ret .= ($tabClass.' '); //support element on multiple tabs!
						
					// subtabs per parent tab!
					$subtabs = $this->tabsElements($box, $elemKey, $tabClass, false);
					if ( !empty($subtabs) )
						$__ret .= ($subtabs.' ');
				}
			}
			return ' '.trim($__ret).' ';
		}
		
		//make Tabs!
		private function subtabsHeader($box) {
			$html = array();

			// get tabs
			$__tabs = isset($box['subtabs']) ? $box['subtabs'] : array();

			$__ret = '';
			if (is_array($__tabs) && count($__tabs)>0) {
				foreach ($__tabs as $tabClass=>$tabElements) {

					$subtabs = $this->tabsHeader($box, $tabClass, false);
					if ( !empty($subtabs) ) {
						$html[] = $subtabs;
					}
				}
				$__ret = implode('', $html);
				
			}
			return $__ret;
		}
		
		
		// retrieve default from option
		private function getTimeDefault( $range='0:0' ) {
			
			if ( empty($range) ) return array(0, 0);
			
			$range = isset($range) && !empty($range) ? $range : '0:0';
			$range = explode(':', $range);
			if ( count($range)==2 )
				return array( (int) $range[0], (int) $range[1]);
			else 
				return array(0, 0);
		}
	}
}