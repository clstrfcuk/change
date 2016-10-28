<?php
/**
 * module return as json_encode
 * http://www.aa-team.com
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the "id" fields, make sure to use all lowercase and no spaces.
 *  
 */
if ( !function_exists( 'psp_wplanner_fb_options' ) ) {
	
	function __doRange( $arr ) {
		$newarr = array();
		if ( is_array($arr) && count($arr)>0 ) {
			foreach ($arr as $k => $v) {
				$newarr[ $v ] = $v;
			}
		}
		return $newarr;
	}
		
	function psp_wplanner_fb_options() 
	{
		global $wpdb, $psp;
		
		/* Here we define the different drop downs for our option page */
		
		// Facebook language
		$select_fblanguage = array( "af_ZA" => "Afrikaans","sq_AL" => "Albanian","ar_AR" => "Arabic","hy_AM" => "Armenian","eu_ES" => "Basque","be_BY" => "Belarusian","bn_IN" => "Bengali","bs_BA" => "Bosanski","bg_BG" => "Bulgarian","ca_ES" => "Catalan","zh_CN" => "Chinese","cs_CZ" => "Czech","da_DK" => "Danish","fy_NL" => "Dutch","en_US" => "English","eo_EO" => "Esperanto","et_EE" => "Estonian","et_EE" => "Estonian","fi_FI" => "Finnish","fo_FO" => "Faroese","tl_PH" => "Filipino","fr_FR" => "French","gl_ES" => "Galician","ka_GE" => "Georgian","de_DE" => "German","zh_CN" => "Greek","he_IL" => "Hebrew","hi_IN" => "Hindi","hr_HR" => "Hrvatski","hu_HU" => "Hungarian","is_IS" => "Icelandic","id_ID" => "Indonesian","ga_IE" => "Irish","it_IT" => "Italian","ja_JP" => "Japanese","ko_KR" => "Korean","ku_TR" => "Kurdish","la_VA" => "Latin","lv_LV" => "Latvian","fb_LT" => "Leet Speak","lt_LT" => "Lithuanian","mk_MK" => "Macedonian","ms_MY" => "Malay","ml_IN" => "Malayalam","nl_NL" => "Nederlands","ne_NP" => "Nepali","nb_NO" => "Norwegian","ps_AF" => "Pashto","fa_IR" => "Persian","pl_PL" => "Polish","pt_PT" => "Portugese","pa_IN" => "Punjabi","ro_RO" => "Romanian","ru_RU" => "Russian","sk_SK" => "Slovak","sl_SI" => "Slovenian","es_LA" => "Spanish","sr_RS" => "Srpski","sw_KE" => "Swahili","sv_SE" => "Swedish","ta_IN" => "Tamil","te_IN" => "Telugu","th_TH" => "Thai","tr_TR" => "Turkish","uk_UA" => "Ukrainian","vi_VN" => "Viettitlese","cy_GB" => "Welsh" );
		
		// Facebook OpenGraph Post Types
		$select_fb_og_type = array(
			"Activities" => array("activity", "sport"),
			"Businesses" => array("bar", "company", "cafe", "hotel", "restaurant"),
			"Groups" => array("cause", "sports_league", "sports_team"),
			"Organizations" => array("band", "government", "non_profit", "school", "university"),
			"People" => array("actor", "athlete", "author", "director", "musician", "politician", "public_figure"),
			"Places" => array("city", "country", "landmark", "state_province"),
			"Products and Entertainment" => array("album", "book", "drink", "food", "game", "product", "song", "movie", "tv_show"),
			"Websites" => array("blog", "website", "article")
		);
		
		// Inputs available for posting to Facebook displayed on each post/page
		$inputs_available = array(
			"message" => __( "Message", 'psp' ), 
			"caption" => __( "Caption", 'psp' ), 
			"image" => __( "Image", 'psp' )
		);
							
		// Facebook privacy options
		$wplannerfb_post_privacy_options = array(
			"EVERYONE" => __( 'Everyone', 'psp' ), 
			"EVERYONE" => __( 'Everyone', 'psp' ), 
			"ALL_FRIENDS" => __( 'All Friends', 'psp' ), 
			"NETWORKS_FRIENDS" => __( 'Networks Friends', 'psp' ), 
			"FRIENDS_OF_FRIENDS" => __( 'Friends of Friends', 'psp' ),
			"CUSTOM" => __( 'Private (only me)', 'psp' )
		);
		
		// all alias of post types 
		$select_post_types = array();
		$exclude_post_types = array('attachment', 'revision', 'wplannertw', 'wptw2fbfeed_fb', 'wplannerfb', 'wplannerlin', 'wpsfpb');
		
		// Facebook available user Pages / Groups
		$fb_user_pages_groups = get_option('psp_fb_planner_user_pages');
		if(trim($fb_user_pages_groups) != "") {
				$fb_all_user_pages_groups = @json_decode($fb_user_pages_groups);
		}
			
		// create query string
		$querystr = "SELECT DISTINCT($wpdb->posts.post_type) FROM $wpdb->posts WHERE 1=1";
		$pageposts = $wpdb->get_results($querystr, ARRAY_A);
		if(count($pageposts) > 0 ) {
			foreach ($pageposts as $key => $value){
				if( !in_array($value['post_type'], $exclude_post_types) ) {
					$select_post_types[$value['post_type']] = ucfirst($value['post_type']);
				}
			}
		}
			
		$options = array(
		
			'inputs_available' => array(
				"title" => __( "Publish on facebook optional fields", 'psp' ),
				"desc" => __( "What inputs do you want to be available for posting to facebook? It will appear on page/post details", 'psp' ),
				'size' 	=> 'large',
				'force_width'=> '130',
				"type" => "multiselect",
				"options" => $inputs_available 
			),
			
			'featured_image_size' => array(
				"title" => __( "Custom Image size", 'psp' ),
				'std' => '450x320',
				"desc" => __( "WIDTH x HEIGHT (Without measuring units. Example: 450x320)", 'psp' ),
				'force_width'=> '180',
				'size' 	=> 'large',
				"type" => "text" 
			),
			
			'featured_image_size_crop' => array(
				"title" => __( "Crop image?", 'psp' ),
				"desc" => __( "If yes, the image will crop to fit the above desired size. If no, the image will just resize with the dimensions provided.", 'psp' ),
				"type" => 'select',
				'size' 	=> 'large',
				'force_width'=> '140',
				"options" => array(
					'true' => __('Yes', 'psp'), 
					'false' => __('No', 'psp')
				)
			),
			
			'default_privacy_option' => array(
				"title" => __( "Publish on facebook default privacy option", 'psp' ),
				"desc" => __( "What privacy option would you like to be default when you're posting to facebook? This option can also be adjusted manually when setting the scheduler for each post/page.", 'psp' ),
				'size' 	=> 'large',
				'force_width'=> '150',
				"type" => "select",
				"options" => $wplannerfb_post_privacy_options 
			),
			
			'email' => array(
				"title" => __( "Admin email", 'psp' ),
				'size' 	=> 'large',
				'force_width'=> '200',
				"desc" => __( "Notify this email adress each time you post something on facebook.", 'psp' ),
				"type" => "text"
			),
			
			'email_subject' => array(
				"title" => __( "Admin email subject", 'psp' ),
				'size' 	=> 'large',
				'force_width'=> '240',
				"desc" => __( "Subject for plugin email notification.", 'psp' ),
				"type" => "text"
			),
			
			
			'email_message' => array(
				"title" => __( "Admin email message", 'psp' ),
				'size' 	=> 'large',
				'force_width'=> '340',
				"desc" => __( "Email content for plugin notification.", 'psp' ),
				"type" => "textarea"
			),
			
			'timezone' => array(
				"title" => __( "Cron timezone", 'psp' ),
				"desc" => __( "Use valid timezone format from <a href='http://php.net/manual/en/timezones.php' target='_blank'>php.net</a>. E.g: America/Detroit", 'psp' ),
				"type" => "select",
				'size' 	=> 'large',
				'force_width'=> '150',
				'options' => __doRange( timezone_identifiers_list() )
			),
			
			
			'info' => array(
				"html" => __( "
					<h2>Important Information</h2>
					<p>You need to create a Facebook App. You can do that <a href='http://developers.facebook.com' target='_blank'>here.</a> and enter its details in to the fields below.</p>", 'psp' ),
				"type" => "message"
			),
			
			'auth' => array(
				"desc" => __( "Facebook Application authorization for cron job.", 'psp' ),
				"type" => "authorization_button",
				'size' 	=> 'large',
				'value' =>  __( "Authorization facebook app", 'psp' )
			),
			
			'app_id' => array(
				"title" => __( "Facebook App ID", 'psp' ),
				"desc" => __( "Insert your Facebook App ID here.", 'psp' ),
				"std" => "",
				'size' 	=> 'large',
				'force_width'=> '250',
				"type" => "text"
			),
			
			'app_secret' => array(
				"title" => __( "Facebook App Secret.", 'psp' ),
				"desc" => __( "Insert your Facebook App Secret here.", 'psp' ),
				"std" => "",
				'size' 	=> 'large',
				'force_width'=> '350',
				"type" => "text"
			),
			
			'redirect_uri' 	=> array(
				'type' 		=> 'text',
				'std' 		=> home_url( '/psp_seo_fb_oauth' ),
				'size' 		=> 'large',
				'readonly'	=> true,
				'title' 	=> __('Redirect URI:', 'psp'),
				'desc' 		=> __('Url to your app.', 'psp')
			),
			
			'language' => array(
				"title" => __( "Facebook Language", 'psp' ),
				"desc" => __( "Select the language for Facebook. More Information about the languages can be found <a target='_blank' href='http://developers.facebook.com/docs/internationalization/'>here</a>.", 'psp' ),
				"std" => "en_US",
				"type" => "select",
				'size' 	=> 'large',
				'force_width'=> '150',
				"options" => $select_fblanguage
			),
			
			'last_status' 	=> array(
				'type' 		=> 'textarea-array',
				'std' 		=> '',
				'size' 		=> 'large',
				'force_width'=> '400',
				'title' 	=> __('Authorize Last Status:', 'psp'),
				'desc' 		=> __('Last Status retrieved from Facebook, for the Authorize operation', 'psp')
			)
			
			,'cronjob_setup_help' =>	array(
						'type' 		=> 'message',
						'html' 		=> '<div style="margin: 20px 0px 20px 20px;">
							<h2>How to setup the Cron Job</h2>
							<p>WordPress comes with its own cron job that allows you to schedule your posts and events. However, in many situations, the WP-Cron is not working well and leads to posts missed their publication schedule and/or scheduled events not executed.<br>
							<span id="more-74"></span><br>
							To understand why this happen, we need to know that the WP-Cron is not a real cron job. It is in fact a virtual cron that only works when a page is loaded. In short, when a page is requested on the frontend/backend, WordPress will first load WP-Cron, follow by the necessary page to display to your reader. The loaded WP-Cron will then check the database to see if there is any thing that needs to be done.</p>
							<p>Reasons for WP-Cron to fail could be due to:</p>
							<ul>
								<li>DNS issue in the server.</li>
								<li>Plugins conflict</li>
								<li>Heavy load in the server which results in WP-Cron not executed fully</li>
								<li>WordPress bug</li>
								<li>Using of cache plugins that prevent the WP-Cron from loading</li>
								<li>And many other reasons</li>
							</ul>
							<p>There are many ways to solve the WP-Cron issue, but the one that I am going to propose here is to disable the virtual WP-Cron and use a real cron job instead.</p>
							<h3>Why use a real cron job?</h3>
							<p>By using a real cron job, you can be sure that all your scheduled items are executed. For popular blogs with high traffic, using a real cron job can also reduce the server bandwidth and reduce the chances of your server crashing, especially when you are experiencing Digg/Slashdot effect.</p>
							<h3>Scheduling a real cron job</h3>
							<p>To configure a real cron job, you will need access to your cPanel or Admin panel (we will be using cPanel in this tutorial).</p>
							<p>1. Log into your cPanel.</p>
							<p>2. Scroll down the list of applications until you see the “<em>cron jobs</em>�? link. Click on it.</p>
							<p><img width="510" height="192" class="aligncenter size-full wp-image-81" alt="wpcron-cpanel" src="{plugin_folder_uri}assets/wpcron-cpanel.png"></p>
							<p>3. Under the <em>Add New Cron Job</em> section, choose the interval that you want it to run the cron job. I have set it to run every 15minutes, but you can change it according to your liking.</p>
							<p><img width="470" height="331" class="aligncenter size-full wp-image-82" alt="wpcron-add-new-cron-job" src="{plugin_folder_uri}/assets/wpcron-add-new-cron-job.png"></p>
							<p>4. In the Command field, enter the following:</p>
						
							<div class="wp_syntax"><div class="code"><pre style="font-family:monospace;" class="bash"><span style="color: #c20cb9; font-weight: bold;">wget</span> <span style="color: #660033;">-q</span> <span style="color: #660033;">-O</span> - </span>' . ( $psp->cfg["paths"]["plugin_dir_url"] ) . '<span style="color: #000000; font-weight: bold;"></span>do-cron.php <span style="color: #000000; font-weight: bold;">&gt;/</span>dev<span style="color: #000000; font-weight: bold;">/</span>null <span style="color: #000000;">2</span><span style="color: #000000; font-weight: bold;">&gt;&amp;</span><span style="color: #000000;">1</span></pre></div></div>
						
							<p>5. Click the “Add New Cron Job�? button. You should now see a message like this:</p>
							<p><img width="577" height="139" class="aligncenter size-full wp-image-83" alt="wpcron-current-cron-job" src="{plugin_folder_uri}/assets/wpcron-current-cron-job.png"></p>
							<p>6. Next, using a FTP program, connect to your server and download the <code>wp-config.php</code> file.</p>
							<p>7. Open the <code>wp-config.php</code> file with a text editor and paste the following line:</p>
						
							<div class="wp_syntax"><div class="code"><pre style="font-family:monospace;" class="php"><span style="color: #990000;">define</span><span style="color: #009900;">(</span><span style="color: #0000ff;">\'DISABLE_WP_CRON\'</span><span style="color: #339933;">,</span> <span style="color: #009900; font-weight: bold;">true</span><span style="color: #009900;">)</span><span style="color: #339933;">;</span></pre></div></div>
						
							<p>8. Save and upload (and replace) this file back to the server. This will disable WordPress internal cron job.</p>
							<p>That’s it.</p>
						
						
							<a href="http://wpdailybits.com/blog/replace-wordpress-cron-with-real-cron-job/74"> Credits </a></div>'
					)
		);
		
		// Facebook available user pages / groups
		if( isset($fb_all_user_pages_groups) && count($fb_all_user_pages_groups) > 0 ) {
			// Facebook available user pages
			if(count($fb_all_user_pages_groups->pages) > 0) {
				$fb_all_user_pages = array();
				foreach($fb_all_user_pages_groups->pages as $key => $value) {
					$fb_all_user_pages[ "{$value->id}" ] = $value->name;
				}
				
				$options['page_filter'] = array( 
					"title" => __( "Activate \"Filter Pages\"", 'psp' ),
					"desc" => __( "Select \"Yes\" if you want to limit the pages shown when publishing and then select from above only what you wish to be shown. <i><strong>This is usefull if you have a lot of pages and/or you have a master facebook account and you wish to limit specific users to see other pages.</strong></i>", 'psp' ),
					"type" => "select",
					'size' 	=> 'large',
					'force_width'=> '80',
					"options" => array('No', 'Yes') 
				);
									
				$options['available_pages'] = array( 
					"title" => __( "What pages do you want to be available when publishing?", 'psp' ),
					"desc" => __( "<strong>This option only works if the \"Filter Pages\" option from above is \"Yes\"</strong>", 'psp' ),
					"type" => "multiselect",
					'size' 	=> 'large',
					'force_width'=> '350',
					"options" => $fb_all_user_pages 
				);
			}

			// Facebook available user groups
			if(count($fb_all_user_pages_groups->groups) > 0) {
				$fb_all_user_groups = array();
				foreach($fb_all_user_pages_groups->groups as $key => $value) {
					$fb_all_user_groups[ "{$value->id}" ] = $value->name;
				}

				$options['group_filter'] = array( 
					"title" => __( "Activate \"Filter Groups\"", 'psp' ),
					"desc" => __( "Select \"Yes\" if you want to limit the groups shown when publishing and then select from above only what you wish to be shown. <i><strong>This is usefull if you have a lot of groups and/or you have a master facebook account and you wish to limit specific users to see other groups.</strong></i>", 'psp' ),
					"type" => "select",
					'size' 	=> 'large',
					'force_width'=> '80',
					"options" => array('No', 'Yes') 
				);

				$options['available_groups'] = array( 
					"title" => __( "What groups do you want to be available when publishing?", 'psp' ),
					"desc" => __( "<strong>This option only works if the \"Filter Groups\" option from above is \"Yes\"</strong>", 'psp' ),
					"type" => "multiselect",
					'size' 	=> 'large',
					'force_width'=> '350',
					"options" => $fb_all_user_groups 
				);
			}
		}

		return $options;
	}
}
global $psp;
echo json_encode(
	array(
		$tryed_module['db_alias'] => array(
			/* define the form_messages box */
			'facebook_planner' => array(
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'toggler' 	=> false, // true|false
				'header' 	=> false, // true|false
				'buttons' 	=> true, // true|false
				'style' 	=> 'panel', // panel|panel-widget
				
				// tabs
				'tabs'	=> array(
					'__tab1'	=> array(__('General setup', 'psp'), 'inputs_available,default_privacy_option,timezone'),
					'__tab2'	=> array(__('Facebook Settings', 'psp'), 'info,auth,app_id,app_secret,redirect_uri,language,last_status'),
					'__tab3'	=> array(__('Facebook - Pages', 'psp'), 'page_filter,available_pages'),
					'__tab4'	=> array(__('Facebook - Groups', 'psp'), 'group_filter,available_groups'),
					'__tab5'	=> array(__('Email Notification', 'psp'), 'email,email_subject,email_message'),
					'__tab6'	=> array(__('Facebook Image size', 'psp'), 'featured_image_size,featured_image_size_predefined,featured_image_size_crop'),
					'__tab7'	=> array(__('Cron Jobs Setup', 'psp'), 'cronjob_setup_help')
				),
				
				
				// create the box elements array
				'elements'	=> psp_wplanner_fb_options()
			)
		)
	)
);