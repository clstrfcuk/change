<?php
/*
* Define class pspTinyCompress
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspTinyCompress') != true) {
    class pspTinyCompress
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_plugin = null;

		private $module_folder = '';
        private $module_folder_path = '';
		private $module = '';

		static protected $_instance;
		
		private $settings = array();


		const TC_URL_BASE =  'https://tinypng.com/';
		const TC_URL_API = 'https://api.tinypng.com/shrink';
		const TC_IMG_MAXSIZE = 2097152; // 1 megabyte = 1048576 bytes
		const TC_MAX_ALLOWED = 500; // maximum allowed monthly image compress

		private static $SMUSHIT_ACTION_URL = '<a href="admin.php?action=psp_tiny_compress&amp;id=%s" class="psp-smushit-action" data-itemid="%s">%s</a>';
		

		/*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;

        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/tiny_compress/';
            $this->module_folder_path = $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/tiny_compress/';
			$this->module = $this->the_plugin->cfg['modules']['tiny_compress'];

			$this->settings = $this->the_plugin->get_theoption( 'psp_tiny_compress' );
			
			$this->init();
        }
        
        
		/**
	    * Singleton pattern
	    *
	    * @return pspFileEdit Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }
	        
	        // admin media custom smushit columns
	        //add_action( 'admin_init', array( self::$_instance, 'custom_media' ) );
	        
	        return self::$_instance;
	    }
	    
	    
        public function init() {

			if (is_admin()) {
	            add_action('admin_menu', array( &$this, 'adminMenu' ));

		        // smushit action per row!
				//add_action( 'admin_action_psp_smushit', array( $this, 'smushit' ) );
				
				// smushit bulk action!
				//add_action( 'admin_menu', array( $this, 'add_page_smushit_bulk' ) );
				//add_action( 'admin_action_psp_smushit_bulk', array( $this, 'goto_page_smushit_bulk' ) );
        	}

			// smushit on media upload!
        	$do_upload = isset($this->settings['do_upload']) && $this->settings['do_upload']=='yes' ? true : false;
			if ( $do_upload ) {
				if ( $this->the_plugin->capabilities_user_has_module('tiny_compress') ) {
					add_filter( 'wp_generate_attachment_metadata', array( &$this, 'generate_metadata_smushit' ), 10, 2 );
				}
			}

			// ajax helper - ajax smushit
			if ( $this->the_plugin->is_admin === true )
				add_action('wp_ajax_psp_tiny_compress', array( $this, 'ajax_request' ));
        }


		/**
	    * Hooks
	    */
	    static public function adminMenu()
	    {
	       self::getInstance()
	    		->_registerAdminPages();
	    }

	    /**
	    * Register plug-in module admin pages and menus
	    */
		protected function _registerAdminPages()
    	{
    		if ( $this->the_plugin->capabilities_user_has_module('tiny_compress') ) {
	    		add_submenu_page(
	    			$this->the_plugin->alias,
	    			$this->the_plugin->alias . " " . __('Tiny Compress', 'psp'),
		            __('Tiny Compress', 'psp'),
		            'read',
		            $this->the_plugin->alias . "_tiny_compress",
		            array($this, 'display_index_page')
		        );
    		}

			return $this;
		}

		public function display_meta_box()
		{
			if ( $this->the_plugin->capabilities_user_has_module('tiny_compress') ) {
			$this->printBoxInterface();
			}
		}

		public function display_index_page()
		{
	    	if( !wp_script_is('psp-media-tiny_compress-js') ) {
	    		wp_enqueue_style( 'psp-media-tiny_compress-js', $this->module_folder .  'app.css', false, '1.0', 'all' );
	    	}
	    	if( !wp_script_is('psp-media-tiny_compress-css') ) {
				wp_enqueue_script( 'psp-media-tiny_compress-css', $this->module_folder . 'app.class.js', array('jquery'), '1.0', false );
	    	}

			$this->printBaseInterface();
		}
        
        public function moduleValidation() {
            $ret = array(
                'status'            => false,
                'html'              => ''
            );
            
            // find if user makes the setup
            $module_settings = $pagespeed_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . "_tiny_compress" );

            $pagespeed_mandatoryFields = array(
                'tiny_key'         => false,
                //'image_sizes'      => false,
            );
            if ( isset($pagespeed_settings['tiny_key']) && !empty($pagespeed_settings['tiny_key']) ) {
                $pagespeed_mandatoryFields['tiny_key'] = true;
            }
            //if ( isset($pagespeed_settings['image_sizes']) && !empty($pagespeed_settings['image_sizes']) ) {
            //    $pagespeed_mandatoryFields['image_sizes'] = true;
            //}
            $mandatoryValid = true;
            foreach ($pagespeed_mandatoryFields as $k=>$v) {
                if ( !$v ) {
                    $mandatoryValid = false;
                    break;
                }
            }
            if ( !$mandatoryValid ) {
                $error_number = 1; // from config.php / errors key
                
                $ret['html'] = $this->the_plugin->print_module_error( $this->module, $error_number, 'Error: Unable to use Tiny Compress module, yet!' );
                return $ret;
            }
            //|| !$this->is_fopen_available()
            if ( !$this->is_curl_available() ) {  
                $error_number = 2; // from config.php / errors key

                $ret['html'] = $this->the_plugin->print_module_error( $this->module, $error_number, 'Error: Unable to use Tiny Compress module, yet!' );
                return $ret;
            }
            $ret['status'] = true;
            return $ret;
        }

        
        /**
         * smushit
         * return: posible values: return | redirect
         */
        public function generate_metadata_smushit( $meta, $attachment_id ) {
        	
        	return $this->meta_smushit_media_sizes( $attachment_id, $meta, true );
        }
        
        public function smushit( $id=null, $force=false, $return='return' ) {
        	
			$ret = array('status' => 'invalid', 'msg' => '');
			
			if ( !current_user_can('upload_files') ) {

				return array_merge( $ret, array('msg' => __('you don\'t have the mandatory permissions for the uploaded files!', 'psp')) );
			}
	
			if ( is_null($id) || (int) $id < 1 ) {
				
				return array_merge( $ret, array('msg' => __('invalid ID from the media file!', 'psp')) );
			}

			$meta = wp_get_attachment_metadata( $id, true ); // get meta for media file
			if ( $meta === false || empty($meta) ) {

				return array_merge( $ret, array('msg' => __('could not retrieve meta data for media file!', 'psp')) );
			}

			if ( wp_attachment_is_image( $id ) === false ) { // media file is an image?

				return array_merge( $ret, array('msg' => __('media file isn\'t an image!', 'psp')) );
			}

			$meta_new = $this->meta_smushit_media_sizes( $id, $meta, $force ); // smushit for all meta sizes of this media file
			$updStat = wp_update_attachment_metadata( $id, $meta_new ); // update meta for media file

			$msg = (array) $this->the_plugin->smushit_show_sizes_msg_details( $meta_new ); $__msg = array();
			if ( isset($meta_new['psp_smushit_errors']) && ( (int) $meta_new['psp_smushit_errors'] ) > 0 ) {
				$status = 'invalid';
				$msg_cssClass = 'error';
				$__msg = array( __('errors occured during smushit operation!', 'psp') );
			}
			else if ( $updStat === true || (int) $updStat > 0 ) {
				$status = 'valid';
				$msg_cssClass = 'success';
			} else {
				$status = 'valid';
				$msg_cssClass = 'success';
			}
			$msg = implode('<br />', array_merge($__msg, $msg));

			if ( $return == 'return' ) {

				return array_merge( $ret, array('status' => $status, 'msg' => $msg) );
			} else {

				wp_safe_redirect( wp_get_referer() );
			}
			die();
        }
        
        private function meta_smushit_media_sizes( $id=null, $meta=array(), $force=false ) {
        	
        	if ( is_null($id) ) return $meta;
        	
			if ( wp_attachment_is_image( $id ) === false ) // media file is an image?
				return $meta;

        	$__meta = $meta;
        	$__meta['psp_smushit_errors'] = 0;
            
            // get only selected sizes!
            $selected_sizes = $this->the_plugin->smushit_tinify_option('image_sizes', (array) $this->settings);

            if ( 1 ) {
                $mediaAtts = array(
                    'path'          => get_attached_file( $id ),
                    'url'           => wp_get_attachment_url( $id )
                );
                $time = time();
                $msghead = $this->msg_header( array('file' => $meta['file'], 'id' => $id, 'time' => $time) );
            }

            // original file should be smushed
            if ( in_array('__original', $selected_sizes) ) {

    			// force smushit or resmushit necessary!
    			$ms = isset($meta['psp_smushit']) ? $meta['psp_smushit'] : '';
    			if ( $force || $this->make_resmush( $ms ) ) {
    
    				$__meta['psp_smushit'] = $this->execute_smushit( $mediaAtts['path'], $mediaAtts['url'], $msghead, $time );
    				update_post_meta( $id, 'psp_smushit_status', $__meta['psp_smushit']['status'] );
    
    				$alreadySmushit = $this->already_smushed( $ms, $__meta['psp_smushit'], $msghead );
    				if ( $alreadySmushit['status'] )
    					$__meta['psp_smushit']['msg'] = $alreadySmushit['msg'];
    				
    				if ( $__meta['psp_smushit']['status']=='invalid' ) // errors occurred!
    					$__meta['psp_smushit_errors']++;
    			}
			}
			
			// no media sizes
			if ( !isset( $meta['sizes']) || empty($meta['sizes']) )
				return $__meta;

			foreach ( $meta['sizes'] as $key => $val ) {

                // current size should be smushed
                if ( !in_array($key, $selected_sizes) ) continue 1;

				$ms_size = '';
				if ( isset($val['psp_smushit']) )
					$ms_size = $val['psp_smushit'];

				if ( !$force && $this->make_resmush( $ms_size ) === false )	continue 1;
				
				$mediaAtts[$key] = array(
					'path'			=> trailingslashit( dirname( $mediaAtts['path'] ) ) . $val['file'],
					'url'			=> trailingslashit( dirname( $mediaAtts['url'] ) ) . $val['file']
				);
                $time = time();
				$msghead = $this->msg_header( array('file' => $meta['file'], 'id' => $id, 'size' => $key, 'time' => $time) );
				
				$__meta['sizes'][$key]['psp_smushit'] = $this->execute_smushit( $mediaAtts[$key]['path'], $mediaAtts[$key]['url'], $msghead, $time );

				$alreadySmushit = $this->already_smushed( $ms_size, $__meta['sizes'][$key]['psp_smushit'], $msghead );
				if ( $alreadySmushit['status'] )
					$__meta['sizes'][$key]['psp_smushit']['msg'] = $alreadySmushit['msg'];

				if ( $__meta['sizes'][$key]['psp_smushit']['status']=='invalid' )
					$__meta['psp_smushit_errors']++;
			}
			
			return $__meta;
        }

        private function msg_header( $pms=array() ) {

			// message header!
			$msghead = '';
			if ( !empty($pms)) {
				
				$__msgKey = array();
				//if ( isset($pms['id']) ) $__msgKey[] = __('id: ', 'psp') . $pms['id'];
				//if ( isset($pms['size']) ) $__msgKey[] = __('size: ', 'psp') . $pms['size'];
				//$msghead = '(' . implode(', ', $__msgKey) . '): ';

				if ( isset($pms['size']) ) $__msgKey[] = '<strong>' . $pms['size'] . '</strong>';
				else if ( isset($pms['file']) ) $__msgKey[] = '<strong>' . $pms['file'] . '</strong>';
                $__msgKey[] = ':';
                if ( isset($pms['time']) ) $__msgKey[] = '(UTC: ' . date('Y.m.d h:i:s', $pms['time']) . ') ';
				$msghead = implode(' ', $__msgKey);
			}
			return $msghead;
        }
        
        private function make_resmush( $status_prev='' ) {
        	
        	if ( empty($status_prev) )
        		return true;
        		
        	$status_prev = $status_prev['status'];
        	if ( in_array($status_prev, array('nosave', 'reduced')) ) // smush action already done & successfull!
        		return false;
        		
        	return true;
        }
        
        private function already_smushed( $status_prev, $status, $msghead='' ) {
        
        	$ret = array('status' => false, 'msg' => '');

        	// verify if already smushed! must be the same message & valid operation status
        	if ( isset($status_prev) && isset($status_prev['status']) && isset($status) && isset($status['status'])
        		&& $status_prev['status'] == $status['status'] && $status['status']!='invalid' )
        		return array_merge( $ret, array(
        			'status' 	=> true,
        			'msg' 		=> $msghead . __('already smushed!', 'psp')
        		));
        	
        	return $ret;
        }
        
        private function execute_smushit( $filepath='', $fileurl='', $msghead='', $time='' ) {
			$ret = array('status' => 'invalid', 'msg' => '', 'time' => $time);
			
			// empty file path!
			if ( empty($filepath) ) {
                $msg = $msghead . __('empty file path!', 'psp');
                $this->set_last_status( 'error', $msg );
				return array_merge( $ret, array('msg' => $msg) );
            }

			// empty file url!
			if (empty($fileurl)) {
                $msg = $msghead . __('empty file url!', 'psp');
                $this->set_last_status( 'error', $msg );
				return array_merge( $ret, array('msg' => $msg) );
            }

			// verify if file exists and is readable
			if ( !$this->the_plugin->verifyFileExists($filepath) ) {
                $msg = $msghead . __('file not found or not readable!', 'psp');
                $this->set_last_status( 'error', $msg );
				return array_merge( $ret, array('msg' => $msg) );
            }
	
			// verify if file is writable
			clearstatcache();
			if ( !is_writable( dirname( $filepath)) ) {
                $msg = $msghead . __('file not writable!', 'psp');
                $this->set_last_status( 'error', $msg );
				return array_merge( $ret, array('msg' => $msg) );
            }
	
			// verify if file size exceed limit!
			$filesize = @filesize($filepath);
			if ( $filesize > self::TC_IMG_MAXSIZE ) {
                $msg = $msghead . __('file size exceed allowed image size limit!', 'psp');
                $this->set_last_status( 'error', $msg );
				return array_merge( $ret, array('msg' => $msg) );
            }
			
			// only http images work with api service
			//$fileurl = str_replace('https://', 'http://', $fileurl);
			
			// verify same domain is activate & validity!
			$same_domain = isset($this->settings['same_domain_url']) && $this->settings['same_domain_url'] == 'yes' ? true : false;
			if ( $same_domain ) {
				$home_url = str_replace('https://', 'http://', get_option('home'));
	
				if (stripos(str_replace('https://', 'http://', $fileurl), $home_url) !== 0) {
                    $msg = $msghead . __('same domain rule is activate in options and not respected on this image!', 'psp');
                    $this->set_last_status( 'error', $msg );
					return array_merge( $ret, array('msg' => $msg) );
                }
			}
			
			// get api service response
			$_getdata = $this->remote_get( $filepath );
 
			if ( !isset($_getdata) || $_getdata['status'] === 'invalid' ) {
			    $msg = $msghead . sprintf( __('curl error; http code: %s; details: %s', 'psp'), $_getdata['http_code'], $_getdata['data'] );
			    $this->set_last_status( 'error', $msg );
				return array_merge( $ret, array('msg' => $msg) );
            }
            
            // compress limits
            $this->set_compress_limits($_getdata);

			// decode api service json response
			$getdata = json_decode( $_getdata['data'] );
            
            if ( !isset($getdata->input) && !isset($getdata->error) ) {
                $msg = $msghead . __('api service wrong response!', 'psp');
                $this->set_last_status( 'error', $msg );
                return array_merge( $ret, array('msg' => $msg) );
            }

            if ( isset($getdata->error) && $getdata->error ) {
                $msg = isset($getdata->error) ? $msghead . __('api service response error: ', 'psp') . $getdata->error . ' - ' . $getdata->message : $msghead . __('api service response unknown error!', 'psp');
                $this->set_last_status( 'error', $msg );
                return array_merge( $ret, array('msg' => $msg) );
            }
            else if ( !isset($_getdata['headers'], $_getdata['headers']["Location"])
                || $_getdata['headers']["Location"] === null ) {
                $msg = $msghead . __('could not find output url!', 'psp');
                $this->set_last_status( 'error', $msg );
                return array_merge( $ret, array('msg' => $msg) );
            }

            // no smushit necessary
            //if ( intval( $getdata->dest_size ) === -1 )
            //    return array_merge( $ret, array('status' => 'nosave', 'msg' => $msghead . __('no smushit necessary!', 'psp')) );

			// json response body - response URL!
			$resp_newurl = $_getdata['headers']["Location"];
	
			// add domain if it's not already there!
			if ( stripos($resp_newurl, 'http://') !== 0 && stripos($resp_newurl, 'https://') !== 0 )
				$resp_newurl = self::TC_URL_BASE . $resp_newurl;

			// get json processed file - temporary location!
			$file_tmp = download_url( $resp_newurl );

			// error during file retrieving!
			if ( is_wp_error( $file_tmp ) ) {

                $msg = sprintf( $msghead . __('could not download output processed file (%s)!', 'psp'), $file_tmp->get_error_message() );
                $this->set_last_status( 'error', $msg );

				@unlink($file_tmp); // delete temporary file!
				return array_merge( $ret, array('msg' => $msg) );
			}
			
			// verify if file exists and is readable
			if ( !$this->the_plugin->verifyFileExists($file_tmp) ) {
                $msg = $msghead . __('output processed temporary file not found or not readable!', 'psp');
                $this->set_last_status( 'error', $msg );
				return array_merge( $ret, array('msg' => $msg) );
            }
				
			// temporary file become new image file!
			@unlink( $filepath );
			$isTmpMoved = @rename( $file_tmp, $filepath );
			if ( !$isTmpMoved ) {
				@copy($file_tmp, $filepath);
				@unlink($file_tmp);
			}

			// new image file - reduced!
			$bytes_reduced = intval( $getdata->input->size ) - intval( $getdata->output->size );
            $percent = ( $bytes_reduced * 100 ) / $getdata->input->size;
			$bytes_reduced = $this->the_plugin->formatBytes( $bytes_reduced, 2 );
            
            // success
            $msg = sprintf( $msghead . __('reduced by %01.2f%% (%s)', 'psp'), $percent, $bytes_reduced );
            $this->set_last_status( 'success', $msg );

			return array_merge( $ret, array('status' => 'reduced', 'msg' => $msg) );
        }

        
		/**
		 * smushit Bulk rows!
		 *
		 */
        /*
	    public function add_page_smushit_bulk( ) {

	    	// here we have the page where the smushit bulk action is executed!
	    	add_media_page( 'PSP Smushit bulk', 'PSP Smushit bulk', 'edit_others_posts', 'psp-smushit-bulk-page', array( $this, 'smushit_bulk' ) );
	    }
	    
	    public function goto_page_smushit_bulk() {

			check_admin_referer( 'bulk-media' );
	
			if ( !is_array( $_REQUEST['media'] ) || empty($_REQUEST['media']) )
				return false;

			$ids = implode( ',', array_map( 'intval', $_REQUEST['media'] ) );
	
			// go to smushit bulk action page!
			wp_redirect( 
				add_query_arg( '_wpnonce', wp_create_nonce( 'psp-smushit-bulk-nonce' ), admin_url( 'upload.php?page=psp-smushit-bulk-page&goback=1&ids=' . $ids ) )
			);
			die();
	    }

		public function smushit_bulk() {
			$output = true;

			if ( $output ) {

				if ( function_exists( 'apache_setenv' ) ) @apache_setenv('no-gzip', 1);

				@ini_set('output_buffering','on');
				@ini_set('zlib.output_compression', 0);
				@ini_set('implicit_flush', 1);
			}
			
			$ret = array();
			$mediaList = null;
			
			$req = array(
				'ids'			=> isset($_REQUEST['ids']) ? $_REQUEST['ids'] : array(),
				'_wpnonce' 		=> isset($_REQUEST['_wpnonce']) ? $_REQUEST['_wpnonce'] : ''
			);

			if ( isset($req['ids']) ) {

				$mediaList = get_posts( array(
					'numberposts' 		=> -1,
					'post_type' 		=> 'attachment',
					'post_mime_type' 	=> 'image',
					'include' 			=> explode(',', $req['ids'])
				) );

			} else {

				$mediaList = get_posts( array(
					'numberposts' 		=> -1,
					'post_type' 		=> 'attachment',
					'post_mime_type' 	=> 'image'
				));

			}
			
			if ( $output ) {

				@ob_implicit_flush( true );
				@ob_end_flush();
			}

			// verify smushit action nonce & user rights!
			if ( !wp_verify_nonce( $req['_wpnonce'], 'psp-smushit-bulk-nonce' )
				|| !current_user_can( 'edit_others_posts' ) ) {
					
				$ret = array_merge( $ret, array('msg' => __('Invalid request!', 'psp')) );
				wp_die( $ret['msg'], 'psp' );
				return;
			}
			
			// verify if there are media files!
			if ( !is_array($mediaList) || empty($mediaList) ) {

				$ret = array_merge( $ret, array('msg' => __('There are no media images uploaded!', 'psp')) );
				if ( $output ) {
					_e( $ret['msg'], 'psp' );
				}

				@ob_flush();
				flush();
				return;
			}

			if ( $output ) {
				printf( "<div style='margin:0px 0px 10px 0px;'>" . __("The number of attachements to be processed is <strong>%s</strong>.", 'psp') . '<br />', count($mediaList) );
			}
			foreach( $mediaList as $media ) {
				
				if ( $output ) {
					printf( "<div style='padding:10px 0px 0px 20px;'>" . __("Media file: <a href='%s' target='_blank'><strong>%s</strong></a> | id: <em>%s</em>", 'psp') . "<br />", esc_html( $media->guid ), esc_html( $media->post_name ), esc_html( $media->ID ) );
				}

				$media_id = (int) $media->ID;

				$ret[$media_id] = $this->smushit($media_id, false, 'return');
				
				if ( $output )
					echo $ret[$media_id]['msg'];
				
				if ( $output )
					echo '</div>';
					
				sleep(0.7);
				@ob_flush();
				flush();
			}
			if ( $output )
				echo '</div>';
				
			return;
		}
        */
		
		
        /**
         * Media custom smushit columns
         *
         */
        /*
	    public function custom_media() {

	    	if( !wp_script_is('psp-media-smushit-js') ) {
	    		wp_enqueue_style( 'psp-media-smushit-js', $this->module_folder .  'app.css', false, '1.0', 'all' );
	    	}
	    	if( !wp_script_is('psp-media-smushit-css') ) {
				wp_enqueue_script( 'psp-media-smushit-css', $this->module_folder . 'app.class.js', array('jquery'), '1.0', false );
	    	}

	    	$screens = array('media');
		    foreach ($screens as $screen) {

				//add_filter( 'manage_edit-' . $screen . '_columns', array( $this, 'media_columns_head' ), 10, 1 );
				add_filter( 'manage_' . $screen . '_columns', array( $this, 'media_columns_head' ), 10, 1 );
				add_action( 'manage_' . $screen . '_custom_column', array( $this, 'media_columns_body' ), 10, 2 );
		    }
	    }
	    
		public function media_columns_head($columns) {
			$new_columns = $columns;
		    $new_columns['psp_smushit'] = 'PSP Smushit';
		    return $new_columns;
		}

		public function media_columns_body($column_name, $id) {
		    global $id;
			
		    // verify that it's smushit column and we have an image media file!
			if ( $column_name=='psp_smushit' && wp_attachment_is_image( $id ) ) {

				echo '<div class="psp-smushit-wrapper">';
				echo 	'<span class="psp-smushit-loading"></span>';

				// retrieve the existing value(s) for this meta field. This returns an array
				$meta_new = wp_get_attachment_metadata( $id );

				if ( isset($meta_new['psp_smushit']) && !empty($meta_new['psp_smushit']) ) {

					$msg = (array) $this->the_plugin->smushit_show_sizes_msg_details( $meta_new ); $__msg = array();
					if ( isset($meta_new['psp_smushit_errors']) && ( (int) $meta_new['psp_smushit_errors'] ) > 0 ) {
						$status = 'invalid';
						$msg_cssClass = 'error';
						$__msg = array( __('errors occured during smushit operation!', 'psp') );
					} else {
						$status = 'valid';
						$msg_cssClass = 'success';
					}
					$msg = implode('<br />', array_merge($__msg, $msg));

					echo '<span id="' . ('psp-smushit-resp-'.$id) . '" class="' . $msg_cssClass . '">' . $msg . '</span><br />';
					printf( self::$SMUSHIT_ACTION_URL, $id, $id, __( 'smushit again!', 'psp' ) );
				} else {
					
					echo '<span id="' . ('psp-smushit-resp-'.$id) . '" class="info">' . __( 'not processed!', 'psp' ) . '</span><br />';
					printf( self::$SMUSHIT_ACTION_URL, $id, $id, __( 'smushit Now!', 'psp' ) );
				}
				echo '</div>';
			}
		}
        */


		/*
		* ajax_request, method
		* --------------------
		*
		*/
		public function ajax_request()
		{
			$req = array(
				'id' 			=> isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0
			);
			
			$ret = $this->smushit($req['id'], true, 'return');

			die( json_encode(array(
				'status' 	=> $ret['status'],
				'data'	 	=> $ret['msg'],
				'data_dbg'	=> 'id response: ' . $req['id']
			)) );
		}
		
		
		/*
		* printBaseInterface, method
		* --------------------------
		*
		* this will add the base DOM code for you options interface
		*/
		private function printBaseInterface()
		{
			
	    	if( !wp_script_is('psp-media-tiny_compress-js') ) {
	    		wp_enqueue_style( 'psp-media-tiny_compress-js', $this->module_folder .  'app.css', false, '1.0', 'all' );
	    	}
	    	if( !wp_script_is('psp-media-tiny_compress-css') ) {
				wp_enqueue_script( 'psp-media-tiny_compress-css', $this->module_folder . 'app.class.js', array('jquery'), '1.0', false );
	    	}
?>
		<?php /*
		<link rel='stylesheet' href='<?php echo $this->module_folder;?>app.css' type='text/css' media='screen' />
		<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
		*/ ?>
		<div id="psp-wrapper" class="fluid wrapper-psp">
			<?php
			// show the top menu
			pspAdminMenu::getInstance()->make_active('advanced_setup|tiny_compress')->show_menu();
			?>
			
			<!-- Page detail -->
			<div id="psp-pagespeed-detail">
				<div id="psp-pagespeed-ajaxresponse"></div>
			</div>
				
			<!-- Main loading box -->
			<div id="psp-main-loading">
				<div id="psp-loading-overlay"></div>
				<div id="psp-loading-box">
					<div class="psp-loading-text"><?php _e('Loading', 'psp');?></div>
					<div class="psp-meter psp-animate" style="width:86%; margin: 34px 0px 0px 7%;"><span style="width:100%"></span></div>
				</div>
			</div>

			<!-- Content -->
			<div id="psp-content">
				
				<h1 class="psp-section-headline">
					<?php echo $this->module['tiny_compress']['menu']['title'];?>
					<span class="psp-section-info"><?php echo $this->module['tiny_compress']['description'];?></span>
					<?php
					$has_help = isset($this->module['tiny_compress']['help']) ? true : false;
					if( $has_help === true ){
						
						$help_type = isset($this->module['tiny_compress']['help']['type']) && $this->module['tiny_compress']['help']['type'] ? 'remote' : 'local';
						if( $help_type == 'remote' ){
							echo '<a href="#load_docs" class="psp-show-docs" data-helptype="' . ( $help_type ) . '" data-url="' . ( $this->module['tiny_compress']['help']['url'] ) . '">HELP</a>';
						} 
					} 
					?>
				</h1>

				<!-- Container -->
				<div class="psp-container clearfix">

					<!-- Main Content Wrapper -->
					<div id="psp-content-wrap" class="clearfix">

						<!-- Content Area -->
						<div id="psp-content-area">
                            <?php 
                            // find if user makes the setup
                            $moduleValidateStat = $this->moduleValidation();
                            if ( !$moduleValidateStat['status'] )
                                echo $moduleValidateStat['html'];
                            else{ 
                            ?>
							<div class="psp-grid_4">
	                        	<div class="psp-panel">
	                        		<div class="psp-panel-header">
										<span class="psp-panel-title">
											<?php _e('Media files Tiny Compress!', 'psp');?>
										</span>
									</div>
									<div class="psp-panel-content">
									    
									    <?php echo $this->show_connection_status(); ?>
									    
										<form class="psp-form" id="1" action="#save_with_ajax">
											<div class="psp-form-row psp-table-ajax-list" id="psp-table-ajax-response">
											<?php
											//$settings = $this->the_plugin->getAllSettings( 'array', 'psp_smushit' );
											$settings = $this->settings;
											$attrs = array(
												'id' 				=> 'pspTinyCompress',
												'show_header' 		=> true,
												'items_per_page' 	=> '10',
												'post_statuses' 	=> 'all',
												'show_header_buttons' => true,
												'columns'			=> array(
													'checkbox'	=> array(
														'th'	=>  'checkbox',
														'td'	=>  'checkbox',
													),

													'id'		=> array(
														'th'	=> __('ID', 'psp'),
														'td'	=> '%ID%',
														'width' => '40'
													),
													
													'thumbnail'		=> array(
														'th'	=> __('', 'psp'),
														'td'	=> '%thumbnail%',
														'align' => 'left',
														'width' => '60'
													),

													'title'		=> array(
														'th'	=> __('File', 'psp'),
														'td'	=> '%title%',
														'align' => 'left',
														'width' => '250'
													),

													'smushit'		=> array(
														'th'	=> __('Smushit Status', 'psp'),
														'td'	=> '%smushit_status%',
														'align' => 'left'
													),
													
													'date'		=> array(
														'th'	=> __('Date', 'psp'),
														'td'	=> '%date%',
														'width' => '120'
													),
													
													'optimize_btn' => array(
														'th'	=> __('Action', 'psp'),
														'td'	=> '%button%',
														'option' => array(
															'value' => __('Smushit', 'psp'),
															'action' => 'do_item_smushit',
															'color' => 'orange'
														),
														'width' => '80'
													),
												),
												'mass_actions' 	=> array(
														'speed_test_mass' => array(
															'value' => __('Mass Smushit', 'psp'),
															'action' => 'do_mass_smushit',
															'color' => 'blue'
														)
												)
											);
											
											pspAjaxListTable::getInstance( $this->the_plugin )
												->setup( $attrs )
												->print_html();
								            ?>
								            </div>
							            </form>
				            		</div>
								</div>
							</div>
                            <?php
                            } 
                            ?>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

<?php
		}

        private function show_connection_status() {
            global $psp;
            
            $html = array();
            
            $connection_status = $this->get_connection_status();
            $compress_limits = $this->get_compress_limits();
            
            ob_start();
            ?>
                <div class="psp-form-row">
                    <div class="psp-message psp-<?php echo $connection_status['status'] == 'valid' ? 'success' : 'error'; ?>">
                        <p><?php echo __('Connection status: ', 'psp') . $connection_status['msg']; ?></p>
                    </div>
                </div>
                
                <div class="psp-form-row">
                    <div class="psp-message psp-<?php echo $compress_limits['status'] == 'valid' ? 'success' : 'error'; ?>">
                        <p><?php echo __('Monthly limit: ', 'psp') . $compress_limits['msg']; ?></p>
                    </div>
                </div>
            <?php
            $content = ob_get_contents();
            ob_end_clean();
            $html[] = $content;
            
            return implode( "\n", $html );
    }


        /**
         * cURL / Send http requests with curl
         */
        public function remote_get($file=null, $api_url=null) {
            $ret = array('status' => 'invalid', 'http_code' => 0);
            
            if ( empty($api_url) ) $api_url = self::TC_URL_API;

            $tiny_api_key = $this->the_plugin->smushit_tinify_option('tiny_key');
            $tiny_api_key = trim($tiny_api_key);
            if ( empty($tiny_api_key) ) {
                return array_merge($ret, array(
                    'data'   => 'tinypng.com api key is not defined!'
                ));
            }
            
            // build binary file
            $file_content = empty($file) ? null : file_get_contents($file);

            $input_params = array(
                'userpwd'                       => 'api:'.$tiny_api_key,
                'post'                          => true,
                'postfields'                    => $file_content, //array('file' => '@'.$file),
                'binarytransfer'                => true,
                'header'                        => true,
                'ssl_verifypeer'                => true,
                'cainfo'                        => $this->get_cacert_file(),
                'useragent'                     => 'Premium SEO Pack Tiny Compress Wordpress Plugin',
                'timeout'                       => isset($this->settings['resp_timeout']) && (int) $this->settings['resp_timeout'] > 0 ? (int) $this->settings['resp_timeout'] : 60,
            );
            $output_params = array(
                'parse_headers'                 => true,
                'resp_is_json'                  => true,
                'resp_add_http_code'            => true,
            );
            $curl_resp = $this->the_plugin->curl( $api_url, $input_params, $output_params, true );

            return array_merge($ret, $curl_resp);
        }
        public function download_url($file_url) {
            $ret = array('status' => 'invalid', 'http_code' => 0);

            $input_params = array(
                'ssl_verifypeer'                => true,
                'cainfo'                        => $this->get_cacert_file(),
            );
            $output_params = array(
            );
            $curl_resp = $this->the_plugin->curl( $api_url, $input_params, $output_params, true );

            return array_merge($ret, $curl_resp);
        }
        

        /**
         * Utils
         */
        public function get_connection_status() {
            $ret = array('status' => 'invalid', 'msg' => '', 'time' => time());

            // get api service response
            $_getdata = $this->remote_get( null );
 
            if ( !isset($_getdata) || $_getdata['status'] === 'invalid' ) {
                $msg = $msghead . sprintf( __('curl error; http code: %s; details: %s', 'psp'), $_getdata['http_code'], $_getdata['data'] );
                //$this->set_last_status( 'error', $msg );
                return array_merge( $ret, array('msg' => $msg) );
            }
            
            // compress limits
            $this->set_compress_limits($_getdata);

            // decode api service json response
            $getdata = json_decode( $_getdata['data'] );
            
            //!isset($getdata->input) && 
            if ( !isset($getdata->error) ) {
                $msg = $msghead . __('api service wrong response!', 'psp');
                //$this->set_last_status( 'error', $msg );
                return array_merge( $ret, array('msg' => $msg) );
            }
  
            //if ( isset($getdata->error) && $getdata->error ) {
            if ( in_array($getdata->error, array( 'InputMissing', 'TooManyRequests' )) ) {
                // success
                $msg = __('API connection successful.', 'psp');
                //$this->set_last_status( 'success', $msg );
                return array_merge( $ret, array('status' => 'valid', 'msg' => $msg) );
            }
            
            // error
            $msg = sprintf( __('API connection unsuccessful (%s - %s).', 'psp'), $getdata->error, $getdata->message );
            //$this->set_last_status( 'error', $msg );
            return array_merge( $ret, array('msg' => $msg) );
        }

        public function set_compress_limits( $response ) {
            // update current monthly count
            if ( isset($response['headers'], $response['headers']['Compression-Count']) ) {
                $current_limits = get_option('psp_tiny_compress_limits', array());
                $current_limits['current_count'] = (int) $response['headers']['Compression-Count'];
                update_option('psp_tiny_compress_limits', $current_limits);
            }
            
            // decode api service json response
            $_response = json_decode( $response['data'] );
            
            if ( isset($_response->error) && $_response->error == 'TooManyRequests' ) {
                $current_limits = get_option('psp_tiny_compress_limits', array());
                $current_limits['TooManyRequests'] = 'yes';
                update_option('psp_tiny_compress_limits', $current_limits);
            }
        }
        
        public function get_compress_limits() {
            $ret = array('status' => 'invalid', 'msg' => '');

            $current_limits = get_option('psp_tiny_compress_limits', array());
            $current_count = isset($current_limits, $current_limits['current_count']) ? (int) $current_limits['current_count'] : 0;
            $TooManyRequests = isset($current_limits, $current_limits['TooManyRequests']) && $current_limits['TooManyRequests'] == 'yes' ? true : false; 
            $limit_reached = $current_count == self::TC_MAX_ALLOWED || 1 ? true : false;

            $msg = sprintf( __('You have made %s compressions this month.', 'psp'), $current_count );
            if ( $current_count >= self::TC_MAX_ALLOWED || $TooManyRequests ) { // limit reached
                $link = '<a href="https://tinypng.com/developers" target="_blank">' . __('TinyPNG API subscription', 'psp') . '</a>';
                $msg = sprintf( __('You have reached your limit of <strong>%s</strong> compressions this month.', 'psp'), $current_count );
                $msg .= '<br />';
                $msg .= sprintf( __('If you need to compress more images you can change your %s.', 'psp'), $link );
            } else {
                $msg = sprintf( __('You have made <strong>%s</strong> compressions this month.', 'psp'), $current_count );
                $ret['status'] = 'valid';
            }
            $msg .= '<br />';
            $msg .= __('You only pay for what you use. The first 500 compression each month are free. You will only be billed if you compress more than 500 images in a month. <a href="https://tinypng.com/developers" target="_blank">More details here</a> - see bottom "Pricing" section.', 'psp');
            
            $ret = array_merge($ret, array('msg' => $msg));
            return $ret;
        }

        public function set_last_status($status, $msg, $step='request') {
            $last_status = array('last_status' => array('status' => $status, 'step' => $step, 'data' => date("Y-m-d H:i:s"), 'msg' => $msg));
            $this->the_plugin->save_theoption( $this->the_plugin->alias . '_tiny_compress_last_status', $last_status );
            $this->the_plugin->save_theoption( $this->the_plugin->alias . '_tiny_compress', array_replace_recursive( (array) $this->settings, $last_status ) );
        }

        public function get_cacert_file() {
            return $this->module_folder_path . 'assets/cacert.pem';
        }

        public static function is_curl_available() {
            return extension_loaded('curl') && function_exists( 'curl_init' );
        }
    
        public static function is_fopen_available() {
            return ini_get('allow_url_fopen');
        }
    }
}

// Initialize the pspTinyCompress class
//$pspTinyCompress = new pspTinyCompress();
$pspTinyCompress = pspTinyCompress::getInstance();