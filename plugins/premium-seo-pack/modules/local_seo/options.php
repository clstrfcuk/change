<?php
/**
 * module return as json_encode
 * http://www.aa-team.com
 * ======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */

function __pspNotifyEngine_localseo( $engine='google', $action='default' ) {
	global $psp;
	
	$req['action'] = $action;
	
	if ( $req['action'] == 'getStatus' ) {
		$notifyStatus = $psp->get_theoption('psp_localseo_engine_notify');
		if ( $notifyStatus === false || !isset($notifyStatus["$engine"]) )
			return '';
		return $notifyStatus["$engine"]["msg_html"];
	}

	$html = array();
	
	$html[] = '<div class="psp-form-row psp-notify-engine-ping psp-notify-' . $engine . '">';

	if ( $engine == 'google' ) {
		$html[] = '<div class="">' . sprintf( __('Notify Google: you can check statistics on <a href="%s" target="_blank">Google Webmaster Tools</a>', 'psp'), 'http://www.google.com/webmasters/tools/' ). '</div>';
	} else if ( $engine == 'bing' ) {
		$html[] = '<div class="">' . sprintf( __('Notify Bing: you can check statistics on <a href="%s" target="_blank">Bing Webmaster Tools</a>', 'psp'), 'http://www.bing.com/toolbox/webmaster' ). '</div>';
	}

	$html[] = '<input type="button" class="psp-button blue" style="width: 160px;" id="psp-notify-' . $engine . '" value="' . ( __('Notify '.ucfirst($engine), 'psp') ) . '">
	<span style="margin:0px 0px 0px 10px" class="response">' . __pspNotifyEngine_localseo( $engine, 'getStatus' ) . '</span>';

	$html[] = '</div>';

	// view page button
	ob_start();
?>
	<script>
	(function($) {
		var ajaxurl = '<?php echo admin_url('admin-ajax.php');?>',
		engine = '<?php echo $engine; ?>';

		$("body").on("click", "#psp-notify-"+engine, function(){

			$.post(ajaxurl, {
				'action' 		: 'pspAdminAjax',
				'sub_action'	: 'localseo_notify',
				'sitemap_type'	: 'xml',
				'engine'		: engine
			}, function(response) {
console.log( response  );
				var $box = $('.psp-notify-'+engine), $res = $box.find('.response');
				$res.html( response.msg_html );
				if ( response.status == 'valid' )
					return true;
				return false;
			}, 'json');
		});
   	})(jQuery);
	</script>
<?php
	$__js = ob_get_contents();
	ob_end_clean();
	$html[] = $__js;

	return implode( "\n", $html );
}
global $psp;
echo json_encode(
	array(
		$tryed_module['db_alias'] => array(
			/* define the form_messages box */
			'local_seo' => array(
				'title' 	=> __('Local SEO', 'psp'),
				'icon' 		=> '{plugin_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> true, // true|false
				'style' 	=> 'panel', // panel|panel-widget

				// create the box elements array
				'elements'	=> array(
					'xmlsitemap_html' => array(
						'type' 		=> 'html',
						'html' 		=> 
							'<div class="psp-form-row">
								<label for="site-items">' . __('Local SEO - locations', 'psp') . '</label>
						   		<div class="psp-form-item large">
									<a id="site-items" target="_blank" href="' . ( home_url('/sitemap-locations.xml') ) . '" style="position: relative;bottom: -6px;">' . ( home_url('/sitemap-locations.xml') ) . '</a>
								</div>
						   		<!--<div class="psp-form-item large">
									<a id="site-items" target="_blank" href="' . ( home_url('/sitemap-locations.kml') ) . '" style="position: relative;bottom: -6px;">' . ( home_url('/sitemap-locations.kml') ) . '</a>
								</div>-->
								
								<label for="site-items">' . __('Validators', 'psp') . '</label>
								<div class="psp-form-item large">
									<a id="site-items" target="_blank" href="http://www.google.com/webmasters/tools/richsnippets" style="position: relative;bottom: -6px;">Google Rich Snippets Testing Tool</a>
								</div>
								<div class="psp-form-item large">
									<a id="site-items" target="_blank" href="http://www.ebusiness-unibw.org/tools/goodrelations-validator/" style="position: relative;bottom: -6px;">GoodRelations Validator</a>
								</div>
							</div>'
					)

					/*'google_map_api_key' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '',
						'size' 		=> 'large',
						'force_width'=> '350',
						'title' 	=> __('Google Maps API Key:', 'psp'),
						'desc' 		=> __('Here you can enter Google Maps API Console Key, recommended by <a href="https://developers.google.com/maps/documentation/javascript/tutorial?hl=en#api_key" target="_blank">Google Tutorial</a>', 'psp')
					)*/
					,'slug' 	=> array(
						'type' 		=> 'text',
						'std' 		=> 'psplocation',
						'size' 		=> 'small',
						'force_width'=> '350',
						'title' 	=> __('Slug: ', 'psp'),
						'desc' 		=> __('Custom Slug for your Locations', 'psp')
					)
					
					,'notify_google' => array(
						'type' => 'html',
						'html' => __pspNotifyEngine_localseo( 'google' )
					)

					,'address_format' 	=> array(
						'type' 		=> 'text',
						'std' 		=> '{street} {city}, {state} {zipcode} {country}',
						'size' 		=> 'large',
						'force_width'=> '350',
						'title' 	=> __('Address Format: ', 'psp'),
						'desc' 		=> __('You can use the following tags: {street} {city}, {state} {zipcode} {country}. This format is used for kml sitemap generation and for address shortcode. <!--Also {street} is included first by default and {country} is included last by default in this format and you must not include them.-->', 'psp')
					)
				)
			)
			
		)
	)
);