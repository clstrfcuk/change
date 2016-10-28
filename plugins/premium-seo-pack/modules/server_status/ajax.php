<?php
/*
* Define class pspServerStatus
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspServerStatusAjax') != true) {
    class pspServerStatusAjax extends pspServerStatus
    {
    	public $the_plugin = null;
		private $module_folder = null;
		private $file_cache_directory = '/psp-page-speed';
		private $cache_lifetime = 60; // in seconds
		
		/*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct( $the_plugin=array() )
        {
        	$this->the_plugin = $the_plugin;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/server_status/';
			
			// ajax  helper
			add_action('wp_ajax_pspServerStatusRequest', array( $this, 'ajax_request' ));
			add_action('wp_ajax_pspServerStatusVerify', array( $this, 'verify_step' ));
		}
		
		/*
		* ajax_request, method
		* --------------------
		*
		* this will create requests to 404 table
		*/
		public function ajax_request()
		{
			$return = array();
			$actions = isset($_REQUEST['sub_action']) ? explode(",", $_REQUEST['sub_action']) : '';
			
			// Check Memory Limit 
			if( in_array( 'check_memory_limit', array_values($actions)) ){
				
				$memory = $this->let_to_num( WP_MEMORY_LIMIT );
				$html = array();
            	if ( $memory < 127108864 ) {
            		$html[] = '<div class="psp-message psp-error">' . sprintf( __( '%s - We recommend setting memory to at least 128MB. See: <a href="%s">Increasing memory allocated to PHP</a>', 'psp' ), size_format( $memory ), 'http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP' ) . '</div>';
            	} else {
            		$html[] = '<div class="psp-message psp-success">' . size_format( $memory ) . '</div>';
            	}

				$return = array(
					'status'	=> 'valid',
					'html' 		=> implode("\n", $html)
				);
			}
			
			// Export LOG
			if( in_array( 'export_log', array_values($actions)) ){
				
				$log = isset($_REQUEST['log']) ? $_REQUEST['log'] : '';
				$temp_file = tmpfile();
				fwrite( $temp_file, $log );
				fseek( $temp_file, 0 );
				
				header( 'Content-Type: application/octet-stream' );
				header( 'Content-Disposition: attachment; filename="psp-logs.html"' );
				header( 'Content-Length: ' . strlen($log) );
				
				echo fread( $temp_file, strlen($log) );
				
				 // this removes the file
				fclose( $temp_file );
				
				die;
			}
			
			// Remote GET
			if( in_array( 'remote_get', array_values($actions)) ){
				
				$status = false;
				$msg = '';
				// WP Remote Get Check
				$params = array(
					'sslverify' 	=> false,
		        	'timeout' 		=> 20,
		        	'body'			=> array()
				);
				$response = wp_remote_post( 'http://webservices.amazon.com/AWSECommerceService/AWSECommerceService.wsdl', $params );
	 
				if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
	        		$msg = __('wp_remote_get() was successful', 'psp' );
	        		$status = true;
	        	} elseif ( is_wp_error( $response ) ) {
	        		$msg = __( 'wp_remote_get() failed. Webservices Amazon won\'t work with your server. Contact your hosting provider. Error:', 'psp' ) . ' ' . $response->get_error_message();
	        		$status = false;
	        	} else {
	            	$msg = __( 'wp_remote_get() failed. Webservices Amazon may not work with your server.', 'psp' );
	        		$status = false;
	        	}
				
				$return = array(
					'status'	=> ( 1/*$status == true*/ ? 'valid' : 'invalid' ),
					'html' 		=> ( $status == true ? '<div class="psp-message psp-success">' : '<div class="psp-message psp-error">' ) . $msg . '</div>' 
				);
        	}

			// check SOAP
			if( in_array( 'check_soap', array_values($actions)) ){
				
				$status = false;
				$msg = '';
				
				if ( class_exists( 'SoapClient' ) ) {
					$msg = __('Your server has the SOAP Client class enabled.', 'psp' );
					$status = true;
				} else {
	        		$msg = sprintf( __( 'Your server does not have the <a href="%s">SOAP Client</a> class enabled - some gateway plugins which use SOAP may not work as expected.', 'psp' ), 'http://php.net/manual/en/class.soapclient.php' ) . '</mark>';
	        		$status = false;
	        	}

				$return = array(
					'status'	=> ( 1/*$status == true*/ ? 'valid' : 'invalid' ),
					'html' 		=> ( $status == true ? '<div class="psp-message psp-success">' : '<div class="psp-message psp-error">' ) . $msg . '</div>' 
				);
			}
			
			// active plugins
			if( in_array( 'active_plugins', array_values($actions)) ){
				$active_plugins = (array) get_option( 'active_plugins', array() );
									
     			if ( is_multisite() )
					$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );

				$wc_plugins = array();

				foreach ( $active_plugins as $plugin ) {

					$plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
					$dirname        = dirname( $plugin );
					$version_string = '';

					if ( ! empty( $plugin_data['Name'] ) ) {

						if ( strstr( $dirname, 'psp' ) ) {

							if ( false === ( $version_data = get_transient( $plugin . '_version_data' ) ) ) {
								$changelog = wp_remote_get( 'http://dzv365zjfbd8v.cloudfront.net/changelogs/' . $dirname . '/changelog.txt' );
								$cl_lines  = explode( "\n", wp_remote_retrieve_body( $changelog ) );
								if ( ! empty( $cl_lines ) ) {
									foreach ( $cl_lines as $line_num => $cl_line ) {
										if ( preg_match( '/^[0-9]/', $cl_line ) ) {

											$date         = str_replace( '.' , '-' , trim( substr( $cl_line , 0 , strpos( $cl_line , '-' ) ) ) );
											$version      = preg_replace( '~[^0-9,.]~' , '' ,stristr( $cl_line , "version" ) );
											$update       = trim( str_replace( "*" , "" , $cl_lines[ $line_num + 1 ] ) );
											$version_data = array( 'date' => $date , 'version' => $version , 'update' => $update , 'changelog' => $changelog );
											set_transient( $plugin . '_version_data', $version_data , 60*60*12 );
											break;
										}
									}
								}
							}

							if ( ! empty( $version_data['version'] ) && version_compare( $version_data['version'], $plugin_data['Version'], '!=' ) )
								$version_string = ' &ndash; <strong style="color:red;">' . $version_data['version'] . ' ' . __( 'is available', 'psp' ) . '</strong>';
						}

						$wc_plugins[] = $plugin_data['Name'] . ' ' . __( 'by', 'psp' ) . ' ' . $plugin_data['Author'] . ' ' . __( 'version', 'psp' ) . ' ' . $plugin_data['Version'] . $version_string;

					}
				}

				if ( sizeof( $wc_plugins ) > 0 ){
					$return = array(
						'status'	=> 'valid',
						'html' 		=> implode( ', <br/>', $wc_plugins ) 
					);
				}
			}

			// active modules of the plugin
			if( in_array( 'active_modules', array_values($actions)) ){
				$active_modules = (array) $this->the_plugin->cfg['activate_modules'];
  
				$__modules = array();
				foreach ( $active_modules as $module => $status ) {

					$tryed_module = $this->the_plugin->cfg['modules'][ "$module" ];
					$moduleInfo = array();
					if( isset($tryed_module) && count($tryed_module) > 0 ) {
						$moduleInfo = array(
							'title'			=> $tryed_module["$module"]['menu']['title'],
							'version'		=> $tryed_module["$module"]['version'],
							'icon'			=> $tryed_module["$module"]['menu']['icon'],
							'description'	=> isset($tryed_module["$module"]['description']) ? $tryed_module["$module"]['description'] : '',
							'url'			=> isset($tryed_module["$module"]['in_dashboard']['url']) ? $tryed_module["$module"]['in_dashboard']['url'] : ''
						);
						
						$title = '<span class="title">' . $moduleInfo['title'] . '</span>';
						if ( isset($moduleInfo['url']) && !empty($moduleInfo['url']) ) {
							$title = '<a href="' . $moduleInfo['url'] . '" class="title">' . $title . '</a>';
						}
						$iconUrl = $this->the_plugin->cfg['paths']['plugin_dir_url'] . "modules/$module/" . $moduleInfo['icon']; 
						
						$__modules[] = '<div class="active_modules">
							<img src="' . $iconUrl . '" />'
							. $title
							. ',<span class="version">' . $moduleInfo['version'] . '</span>
							<span class="description">(' . $moduleInfo['description'] . ')</span>
						</div>';
					}
				}

				if ( sizeof( $__modules ) > 0 ){
					$return = array(
						'status'	=> 'valid',
						'html' 		=> implode( '', $__modules ) 
					);
				}
			}

            // Remote GET
            if( in_array( 'smushit_remote_get', array_values($actions)) ){
                
                define('SMUSHIT_URL_BASE', 'http://www.smushit.com/');
                define('SMUSHIT_URL', 'http://www.smushit.com/ysmush.it/ws.php?img=%s');
        
                $status = false;
                $msg = '';
                // WP Remote Get Check
                $params = array(
                    'sslverify'     => false,
                    'timeout'       => 20,
                    'body'          => array()
                );
                $response = wp_remote_post( SMUSHIT_URL_BASE, $params );
     
                if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
                    $msg = __('Connection to smushit.com server was successful', 'psp' );
                    $status = true;
                } elseif ( is_wp_error( $response ) ) {
                    $msg = __( 'Connection to smushit.com server failed. Contact your hosting provider. Error:', 'psp' ) . ' ' . $response->get_error_message();
                    $status = false;
                } else {
                    $msg = __( 'Connection to smushit.com server failed.', 'psp' );
                    $status = false;
                }
                
                $return = array(
                    'status'    => ( 1/*$status == true*/ ? 'valid' : 'invalid' ),
                    'html'      => ( $status == true ? '<div class="psp-message psp-success">' : '<div class="psp-message psp-error">' ) . $msg . '</div>' 
                );
            }

            // Remote GET
            if( in_array( 'tiny_compress_remote_get', array_values($actions)) ){
                
                define('TINYCOMPRESS_URL_BASE', 'https://api.tinypng.com/shrink');
                //define('SMUSHIT_URL', 'http://www.smushit.com/ysmush.it/ws.php?img=%s');
                define('TINYCOMPRESS_SERVER', 'TinyPNG.com');
        
                $status = false;
                $msg = '';
                // WP Remote Get Check
                $params = array(
                    'sslverify'     => false,
                    'timeout'       => 20,
                    'body'          => array()
                );
                $response = wp_remote_post( TINYCOMPRESS_URL_BASE, $params );
                //var_dump('<pre>', $response, '</pre>'); die('debug...'); 
     
                if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
                    $msg = __('Connection to '.TINYCOMPRESS_SERVER.' server was successful', 'psp' );
                    $status = true;
                } elseif ( is_wp_error( $response ) ) {
                    $msg = __( 'Connection to '.TINYCOMPRESS_SERVER.' server failed. Contact your hosting provider. Error:', 'psp' ) . ' ' . $response->get_error_message();
                    $status = false;
                } else {
                    $msg = __( 'Connection to '.TINYCOMPRESS_SERVER.' server failed. Error:', 'psp' ) . ' ' . $response['response']['code'] . ' - ' . $response['response']['message'];
                    $status = false;
                }
                
                $return = array(
                    'status'    => ( 1/*$status == true*/ ? 'valid' : 'invalid' ),
                    'html'      => ( $status == true ? '<div class="psp-message psp-success">' : '<div class="psp-message psp-error">' ) . $msg . '</div>' 
                );
            }
			
			die(json_encode($return));
		}
		
		public function verify_step() {
			$module = isset($_REQUEST['module']) ? trim($_REQUEST['module']) : '';
			$action = isset($_REQUEST['sub_action']) ? trim($_REQUEST['sub_action']) : '';
			
			$start = microtime(true);

			$return = array(
				'status'	=> 'invalid',
				'log' 		=> ''
			);

			// google analytics
			if ( $module == 'google_analytics' ) {
				
				$analytics_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_google_analytics' );
				$analytics_mandatoryFields = array(
					'client_id'			=> false,
					'client_secret'		=> false,
					'redirect_uri'		=> false
				);
					
				// get the module init file
				require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/Google_Analytics/init.php' );
				// Initialize the pspGoogleAnalytics class
				$pspGoogleAnalytics = new pspGoogleAnalytics();

				if ( $action == 'step1' ) {
				
					if ( isset($analytics_settings['client_id'])
						&& !empty($analytics_settings['client_id']) )
						$analytics_mandatoryFields['client_id'] = true;
						
					if ( isset($analytics_settings['client_secret'])
						&& !empty($analytics_settings['client_secret']) )
						$analytics_mandatoryFields['client_secret'] = true;
		
					if ( isset($analytics_settings['redirect_uri'])
						&& !empty($analytics_settings['redirect_uri']) )
						$analytics_mandatoryFields['redirect_uri'] = true;
		
					$mandatoryValid = true;
					foreach ($analytics_mandatoryFields as $k=>$v) {
						if ( !$v ) {
							$mandatoryValid = false;
							break;
						}
					}
					
					$return = array(
						'status'	=> $mandatoryValid ? 'valid' : 'invalid',
						'log' 		=> $mandatoryValid ? __('all mandatory fields: client id, client secret, redirect uri are set!', 'psp') : __('some of the mandatory fields: client id, client secret, redirect uri are NOT set!', 'psp'),
						'execution_time' => number_format( microtime(true) - $start, 2)
					);
				} // end step 1

				if ( $action == 'step2' ) {
					
					$auth = $pspGoogleAnalytics->makeoAuthLogin();
					
					$msg = isset($analytics_settings['last_status']) ? $analytics_settings['last_status'] : 'no message logged yet!';
					
					$return = array(
						'status'	=> $auth ? 'valid' : 'invalid',
						'log' 		=> $msg,
						'execution_time' => number_format( microtime(true) - $start, 2)
					);
				} // end step 2
				
				if ( $action == 'step3' ) {
					
					$getProfile = $pspGoogleAnalytics->get_profiles();
					
					$isProfile = false;
					if ( isset($analytics_settings['profile_id']) && !empty($analytics_settings['profile_id']) && count($getProfile) > 0 && !isset($getProfile['0']) ) {
						$isProfile = true;
					}

					$msg = isset($analytics_settings['profile_last_status']) ? $analytics_settings['profile_last_status'] : 'no message logged yet!';
					
					$return = array(
						'status'	=> $isProfile ? 'valid' : 'invalid',
						'log' 		=> $msg,
						'execution_time' => number_format( microtime(true) - $start, 2)
					);
				} // end step 3
				
				if ( $action == 'step4' ) {
					
					$today = date( 'Y-m-d' );

					$_REQUEST['return']			= 'array';
					$_REQUEST['sub_action'] 	= 'getAudience';
					$_REQUEST['from_date'] 		= date( 'Y-m-d', strtotime( "-1 week", strtotime( $today ) ) );
					$_REQUEST['to_date'] 		= date( 'Y-m-d', strtotime( $today ) );
					
					$getGraph = $pspGoogleAnalytics->ajax_request();
  
					$isGraph = false;
					if ( isset($getGraph['getAudience']['status']) ) {
						$isGraph = true;
						
						if ( isset($getGraph['getAudience']['data']) ) {
							$msg = $getGraph['getAudience']['data'];
						} else {
							$msg = $getGraph['getAudience']['reason'];
						}
					} else {
						$msg = $getGraph['__access']['msg'];
					}

					$return = array(
						'status'	=> $isGraph ? 'valid' : 'invalid',
						'log' 		=> $msg,
						'execution_time' => number_format( microtime(true) - $start, 2)
					);
				} // end step 4
				
			}

			// SERP
			if ( $module == 'serp' ) {
				
				// Google SERP
				$serp_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_serp' );
				$serp_mandatoryFields = array(
					'developer_key'			=> false,
					'custom_search_id'		=> false,
					'google_country'		=> false
				);
				
				// get the module init file
				require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/serp/init.php' );
				// Initialize the pspGoogleAnalytics class
				$pspSERP = new pspSERP();

				if ( $action == 'step1' ) {
				
					if ( isset($serp_settings['developer_key'])
						&& !empty($serp_settings['developer_key']) )
						$serp_mandatoryFields['developer_key'] = true;
						
					if ( isset($serp_settings['custom_search_id'])
						&& !empty($serp_settings['custom_search_id']) )
						$serp_mandatoryFields['custom_search_id'] = true;
		
					if ( isset($serp_settings['google_country'])
						&& !empty($serp_settings['google_country']) )
						$serp_mandatoryFields['google_country'] = true;
		
					$mandatoryValid = true;
					foreach ($serp_mandatoryFields as $k=>$v) {
						if ( !$v ) {
							$mandatoryValid = false;
							break;
						}
					}
					
					$return = array(
						'status'	=> $mandatoryValid ? 'valid' : 'invalid',
						'log' 		=> $mandatoryValid ? __('all mandatory fields: developer key, custom search id, google country are set!', 'psp') : __('some of the mandatory fields: developer key, custom search id, google country are NOT set!', 'psp'),
						'execution_time' => number_format( microtime(true) - $start, 2)
					);
				} // end step 1

				if ( $action == 'step2' ) {
					
					$today = date( 'Y-m-d' );

					$_REQUEST['return']		= 'array';
					$_REQUEST['keyword'] 	= 'test';
					$_REQUEST['link'] 		= 'www.test.com';
					
					$getGraph = $pspSERP->addToReporter();
  
					$isGraph = false;
					if ( isset($getGraph['status']) && $getGraph['status']=='valid' ) {
						$isGraph = true;
						$msg = $getGraph['status'];
					} else {
						$msg = $getGraph['status'];
					}
					
					// refresh to get new log!
					$serp_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_serp' );
					$msg = isset($serp_settings['last_status']) ? $serp_settings['last_status'] : 'no message logged yet!';

					$return = array(
						'status'	=> $isGraph ? 'valid' : 'invalid',
						'log' 		=> $msg,
						'execution_time' => number_format( microtime(true) - $start, 2)
					);
				} // end step 2
				
			}

			// Pagespeed
			if ( $module == 'pagespeed' ) {
				
				// Google Pagespeed
				$pagespeed_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_pagespeed' );
				$pagespeed_mandatoryFields = array(
					'developer_key'			=> false,
					'google_language'		=> false
				);
				
				// get the module init file
				require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/google_pagespeed/ajax.php' );
				// Initialize the pspPageSpeedInsightsAjax class
				$pspPagespeed = new pspPageSpeedInsightsAjax($this->the_plugin);

				if ( $action == 'step1' ) {
				
					if ( isset($pagespeed_settings['developer_key'])
						&& !empty($pagespeed_settings['developer_key']) )
						$pagespeed_mandatoryFields['developer_key'] = true;
						
					if ( isset($pagespeed_settings['google_language'])
						&& !empty($pagespeed_settings['google_language']) )
						$pagespeed_mandatoryFields['google_language'] = true;
		
					$mandatoryValid = true;
					foreach ($pagespeed_mandatoryFields as $k=>$v) {
						if ( !$v ) {
							$mandatoryValid = false;
							break;
						}
					}
					
					$return = array(
						'status'	=> $mandatoryValid ? 'valid' : 'invalid',
						'log' 		=> $mandatoryValid ? __('all mandatory fields: developer key, google language are set!', 'psp') : __('some of the mandatory fields: developer key, google language are NOT set!', 'psp'),
						'execution_time' => number_format( microtime(true) - $start, 2)
					);
				} // end step 1

				if ( $action == 'step2' ) {
					
					$today = date( 'Y-m-d' );

					$_REQUEST['return']		= 'array';
					$_REQUEST['sub_actions'] = 'checkPage,viewSpeedRaportById';
					
					// get homepage
					$postid = 0;
					$postid = get_option( 'page_for_posts' );
					if ( !$postid ) {
						$postid = get_option( 'page_on_front' );
					}
					if ( !$postid ) {
						$args = array( 'posts_per_page' => 1, 'offset'=> 0 );
						$myposts = get_posts( $args );
						$postid = $myposts[0]->ID;
					}
					$_REQUEST['postid'] = $postid;
					$_REQUEST['link'] = get_permalink( (int) $_REQUEST['postid'] );
					$getGraph = $pspPagespeed->check_page( $_REQUEST['link'], $_REQUEST['postid'] );
  
					$isGraph = false;
					if ( isset($getGraph['status']) && $getGraph['status']=='valid' ) {
						$isGraph = true;
						$msg = $getGraph['status'];
					} else {
						$msg = $getGraph['status'];
					}
					
					// refresh to get new log!
					$pagespeed_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_pagespeed' );
					$msg = isset($pagespeed_settings['last_status']) ? $pagespeed_settings['last_status'] : 'no message logged yet!';

					$return = array(
						'status'	=> $isGraph ? 'valid' : 'invalid',
						'log' 		=> $msg,
						'execution_time' => number_format( microtime(true) - $start, 2)
					);
				} // end step 2
				
			}

			// facebook planner
			if ( $module == 'facebook_planner' ) {
				
				$facebook_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_facebook_planner' );
				$facebook_mandatoryFields = array(
					'app_id'			=> false,
					'app_secret'		=> false,
					'language'			=> false,
					'redirect_uri'		=> false
				);
					
				// get the module init file
				require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/facebook_planner/init.php' );
				// Initialize the pspGoogleAnalytics class
				$pspFacebook_Planner = new pspFacebook_Planner();

				if ( $action == 'step1' ) {
				
					if ( isset($facebook_settings['app_id'])
						&& !empty($facebook_settings['app_id']) )
						$facebook_mandatoryFields['app_id'] = true;
						
					if ( isset($facebook_settings['app_secret'])
						&& !empty($facebook_settings['app_secret']) )
						$facebook_mandatoryFields['app_secret'] = true;
						
					if ( isset($facebook_settings['redirect_uri'])
						&& !empty($facebook_settings['redirect_uri']) )
						$facebook_mandatoryFields['redirect_uri'] = true;
		
					if ( isset($facebook_settings['language'])
						&& !empty($facebook_settings['language']) )
						$facebook_mandatoryFields['language'] = true;
		
					$mandatoryValid = true;
					foreach ($facebook_mandatoryFields as $k=>$v) {
						if ( !$v ) {
							$mandatoryValid = false;
							break;
						}
					}
					
					$return = array(
						'status'	=> $mandatoryValid ? 'valid' : 'invalid',
						'log' 		=> $mandatoryValid ? __('all mandatory fields: app id, app secret, language are set!', 'psp') : __('some of the mandatory fields: app id, app secret, language are NOT set!', 'psp'),
						'execution_time' => number_format( microtime(true) - $start, 2)
					);
				} // end step 1

				if ( $action == 'step2' ) {
					
					$auth = $pspFacebook_Planner->makeoAuthLogin();
					
					$msg = isset($facebook_settings['last_status']) ? $facebook_settings['last_status'] : 'no message logged yet!';
					
					$return = array(
						'status'	=> $auth ? 'valid' : 'invalid',
						'log' 		=> $msg,
						'execution_time' => number_format( microtime(true) - $start, 2)
					);
				} // end step 2
				
				if ( $action == 'step3' ) {
					
					$today = date( 'Y-m-d' );

					$_REQUEST['return']			= 'array';
					$_REQUEST['sub_action'] 	= 'getAudience';
					$_REQUEST['from_date'] 		= date( 'Y-m-d', strtotime( "-1 week", strtotime( $today ) ) );
					$_REQUEST['to_date'] 		= date( 'Y-m-d', strtotime( $today ) );
					
					$getGraph = $pspGoogleAnalytics->ajax_request();
  
					$isGraph = false;
					if ( isset($getGraph['getAudience']['status']) ) {
						$isGraph = true;
						
						if ( isset($getGraph['getAudience']['data']) ) {
							$msg = $getGraph['getAudience']['data'];
						} else {
							$msg = $getGraph['getAudience']['reason'];
						}
					} else {
						$msg = $getGraph['__access']['msg'];
					}

					$return = array(
						'status'	=> $isGraph ? 'valid' : 'invalid',
						'log' 		=> $msg,
						'execution_time' => number_format( microtime(true) - $start, 2)
					);
				} // end step 3
				
			}

            // Tiny Compress
            if ( $module == 'tinycompress' ) {
                
                // Tiny Compress
                $tinycompress_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_tiny_compress' );
                $tinycompress_mandatoryFields = array(
                    'tiny_key'         => false,
                    'image_sizes'      => false,
                );
                
                // get the module init file
                require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/tiny_compress/init.php' );
                // Initialize the pspTinyCompress class
                $pspTinyCompress = new pspTinyCompress();

                if ( $action == 'step1' ) {
                
                    if ( isset($tinycompress_settings['tiny_key'])
                        && !empty($tinycompress_settings['tiny_key']) )
                        $tinycompress_mandatoryFields['tiny_key'] = true;
                        
                    if ( isset($tinycompress_settings['image_sizes'])
                        && !empty($tinycompress_settings['image_sizes']) )
                        $tinycompress_mandatoryFields['image_sizes'] = true;
        
                    $mandatoryValid = true;
                    foreach ($tinycompress_mandatoryFields as $k=>$v) {
                        if ( !$v ) {
                            $mandatoryValid = false;
                            break;
                        }
                    }
                    
                    $return = array(
                        'status'    => $mandatoryValid ? 'valid' : 'invalid',
                        'log'       => $mandatoryValid ? __('all mandatory fields: tiny api key, image sizes are set!', 'psp') : __('some of the mandatory fields: tiny api key, image sizes are NOT set!', 'psp'),
                        'execution_time' => number_format( microtime(true) - $start, 2)
                    );
                } // end step 1

                if ( $action == 'step2' ) {
                    
                    $today = date( 'Y-m-d' );

                    $connection_status = $pspTinyCompress->get_connection_status();
  
                    $isLastStatus = false;
                    if ( isset($connection_status['status']) && $connection_status['status']=='valid' ) {
                        $isLastStatus = true;
                        $msg = $connection_status['msg'];
                    } else {
                        $msg = $connection_status['msg'];
                    }
                    
                    // refresh to get new log!
                    /*
                    $tinycompress_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_tiny_compress' );
                    $lt = isset($tinycompress_settings['last_status']) ? $tinycompress_settings['last_status'] : '';
                    $isLastStatus = empty($lt) || preg_match('/^\'+$/imu', $lt) ? false : true;
                    $msg = $isLastStatus ? $lt : 'no message logged yet!';
                    */

                    $return = array(
                        'status'    => $isLastStatus ? 'valid' : 'invalid',
                        'log'       => $msg,
                        'execution_time' => number_format( microtime(true) - $start, 2)
                    );
                } // end step 2
                
            }

			die(json_encode($return));
		}

		private function let_to_num( $size ) 
		{
		     $l      = substr( $size, -1 );
		     $ret    = substr( $size, 0, -1 );
		     switch( strtoupper( $l ) ) {
		         case 'P':
		             $ret *= 1024;
		         case 'T':
		             $ret *= 1024;
		         case 'G':
		             $ret *= 1024;
		         case 'M':
		             $ret *= 1024;
		         case 'K':
		             $ret *= 1024;
		     }
		     return $ret;
		}
    }
}