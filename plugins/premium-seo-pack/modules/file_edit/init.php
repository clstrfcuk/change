<?php
/*
* Define class pspFileEdit
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspFileEdit') != true) {
    class pspFileEdit
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
		private $module = '';

		static protected $_instance;
		
		private $settings = array();
		private $settings_orig = array();

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;
        	
        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/file_edit/';
			$this->module = $this->the_plugin->cfg['modules']['file_edit'];

			if (is_admin()) {
	            add_action('admin_menu', array( &$this, 'adminMenu' ));
	            
				//notice on the Settings / Reading / Search Engine Visibility
				//add_action('admin_notices', array( &$this, 'robotstxt_notice' ));
			}
			
			// ajax  helper
			add_action('wp_ajax_pspFileEdit', array( &$this, 'ajax_request' ));
			
			$this->settings = $this->the_plugin->get_theoption( 'psp_file_edit' );
			$this->settings_orig = $this->the_plugin->get_theoption( 'psp_file_edit_orig' );
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

	        return self::$_instance;
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
    		if ( $this->the_plugin->capabilities_user_has_module('file_edit') ) {
	    		add_submenu_page(
	    			$this->the_plugin->alias,
	    			$this->the_plugin->alias . " " . __('Files Edit', 'psp'),
		            __('Files Edit', 'psp'),
		            'read',
		            $this->the_plugin->alias . "_massFileEdit",
		            array($this, 'display_index_page')
		        );
    		}

			return $this;
		}

		public function display_index_page()
		{
			$this->printBaseInterface();
		}
		
		public function robotstxt_notice() {
			global $pagenow;
			if ( $pagenow == 'options-reading.php' ) {
				_e('<div class="updated">Notice: Because you\'re using a custom robots.txt file, the "Discourage search engines from indexing this site" setting won\'t have any effect.</div>', 'psp');
			}
		}


		/*
		* printBaseInterface, method
		* --------------------------
		*
		* this will add the base DOM code for you options interface
		*/
		private function printBaseInterface()
		{
			global $wpdb;
?>
		<link rel='stylesheet' href='<?php echo $this->module_folder;?>app.css' type='text/css' media='screen' />
		<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
		<div id="psp-wrapper" class="fluid wrapper-psp">
			<?php
			// show the top menu
			pspAdminMenu::getInstance()->make_active('advanced_setup|file_edit')->show_menu();
			?>
			
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
					<?php echo $this->module['file_edit']['menu']['title'];?>
					<span class="psp-section-info"><?php echo $this->module['file_edit']['description'];?></span>
					<?php
					$has_help = isset($this->module['file_edit']['help']) ? true : false;
					if( $has_help === true ){
						
						$help_type = isset($this->module['file_edit']['help']['type']) && $this->module['file_edit']['help']['type'] ? 'remote' : 'local';
						if( $help_type == 'remote' ){
							echo '<a href="#load_docs" class="psp-show-docs" data-helptype="' . ( $help_type ) . '" data-url="' . ( $this->module['file_edit']['help']['url'] ) . '">HELP</a>';
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
							<div class="psp-grid_4">
	                        	<div class="psp-panel">
	                        		<div class="psp-panel-header">
										<span class="psp-panel-title">
											<?php _e('Files Edit', 'psp');?>
										</span>
									</div>
									<div class="psp-panel-content">
										<form class="psp-form" id="frm-save-changes" action="#save_with_ajax" method="post">
											<?php if (function_exists('wp_nonce_field')) { wp_nonce_field('psp-file-edit-changes'); } ?>
											<input type="hidden" name="savechanges" value="ok">
											<div class="psp-form-row psp-table-ajax-list" id="psp-table-ajax-response" style="padding: 0px 0px 0px 0px;">

<?php
	//save changes on form submit!
	$__saveRes = $this->saveChanges();

	$__result = array(
		'robotstxt'	=> false,
		'htaccess'	=> false
	);
	$__result['robotstxt'] = $this->getFile('robots.txt');
				
	if ( $this->verify_htaccess() )
		$__result['htaccess'] =  $this->getFile('.htaccess');
	else
		$__result['htaccess']['msg'] = __('You\'re not on a Apache hosting', 'psp');
	
	//make short aliases
	$rt = $__result['robotstxt'];
	$ht = $__result['htaccess'];
	$showBtnSave = (bool) ($rt['status']=='active' || $ht['status']=='active');
	
	$__msg = array('rt' => array(), 'ht' => array());
	//msg: get files
	$rt!==false ? $__msg['rt'][] = $rt['msg'] : '';
	$ht!==false ? $__msg['ht'][] = $ht['msg'] : '';
	//msg: save changes!
	$__saveRes['robotstxt']!==false ? $__msg['rt'][] = $__saveRes['robotstxt']['msg'] : '';
	$__saveRes['htaccess']!==false ? $__msg['ht'][] = $__saveRes['htaccess']['msg'] : '';

	if ( !empty($__saveRes['msg']) ) {
		$__msg['rt'][] = $__saveRes['msg']['rt'];
		$__msg['ht'][] = $__saveRes['msg']['ht'];
	}
	$__msg = array_filter($__msg, array( $this, 'removeEmptyItems')); //filter empty messages!
	if ( empty($__msg) ) $__msg = array('rt' => array(), 'ht' => array());
?>

												<table class="psp-table" style="border: none;border-bottom: 1px solid #dadada;width:100%;border-spacing:0; border-collapse:collapse;">
													<thead>
														<tr>
															<th colspan="2" align="left"><?php _e('
															<ul>
																<li>Here you can edit the robots.txt and .htaccess files.</li>
																<li><a href="http://www.robotstxt.org/robotstxt.html" target="_blank">robots.txt file help</a> (incorrectly editing your robots.txt file could block search engines from targeting your site)</li>
																<li><a href="http://httpd.apache.org/docs/2.4/howto/htaccess.html" target="_blank">.htaccess file help</a> (.htaccess file is static and it is possible that WordPress or another plugin may overwrite this file, also if you\'ve inserted code that your web server can\'t understand, you can disable your entire website in this way, <span style="color: blue;">so make a backup of this file, found on the root of your website, before making changes with this module</span>)</li>
															</u>', 'psp');?></th>
														</tr>
														<?php if ($showBtnSave) { ?>
														<tr>
															<td colspan="2" align="left"><input type="button" class="psp-button blue psp-fe-save" value="Save changes"><input type="button" class="psp-button red psp-fe-create-robots-txt" style="margin-left: 10px;" value="Create Robots.txt file"></td>
														</tr>
														<?php } ?>
														<tr>
															<td width="50%">
																<span><?php _e('robots.txt file', 'psp'); ?></span><br />
																<?php 
																	if ($rt!==false) { 
																		if ( $rt['status'] != 'hidden' ) {
																?>
																<textarea <?php echo $rt['status']=='disabled' ? 'disabled="disabled"' : ''; ?> style="height:300px;" rows="40" name="robotstxt" id="robotstxt"><?php echo $rt['content']; ?></textarea>
																<?php
																		}
																	}
																?>
																<span id="psp-fe-rt-wrap"><?php echo implode('<br />', $__msg['rt']); ?></span>
															</td>
															<td width="50%">
																<span><?php _e('.htaccess file', 'psp'); ?></span><br />
																<?php 
																	if ($ht!==false) {
																		if ( $ht['status'] != 'hidden' ) {
																?>
																<textarea <?php echo $ht['status']=='disabled' ? 'disabled="disabled"' : ''; ?> style="height:300px;" rows="40" name="htaccess" id="htaccess"><?php echo $ht['content']; ?></textarea>
																<?php
																		}
																	}
																?>
																<span id="psp-fe-ht-wrap"><?php echo implode('<br />', $__msg['ht']); ?></span>
															</td>
														</tr>
														<?php if ($showBtnSave) { ?>
														<tr>
															<td colspan="2" align="left"><input type="button" class="psp-button blue psp-fe-save" value="Save changes"><input type="button" class="psp-button red psp-fe-create-robots-txt" style="margin-left: 10px;" value="Create Robots.txt file"></td>
														</tr>
														<?php } ?>
													</thead>
															
													<tbody>
															
													</tbody>
												</table>
											
								            </div>
							            </form>
				            		</div>
								</div>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

<?php
		}
		
		private function saveChanges() {
			$__ret = array(
				'robotstxt'	=> false,
				'htaccess'	=> false,
				'msg' 		=> array()
			);
			
			$__defaults = array(
				'robotstxt'				=> null,
				'robotstxt_saved_time'	=> null,
				'htaccess'				=> null,
				'htaccess_saved_time'	=> null
			);
			$saveDb = $__defaults; $saveDb_orig = $__defaults;
			if ( isset($this->settings) && !empty($this->settings) )
				$saveDb = array_merge($__defaults, $this->settings);
			if ( isset($this->settings_orig) && !empty($this->settings_orig) )
				$saveDb_orig = array_merge($__defaults, $this->settings_orig);

			//form submited!
			if ( isset($_POST['savechanges']) && $_POST['savechanges']=='ok' ) {
				// $is_mange_options = function_exists('current_user_can') && current_user_can( 'manage_options' );
				$is_mange_options = $this->the_plugin->capabilities_user_has_module('file_edit');

				//have rights!
				if (!$is_mange_options) {
					if ( isset($_POST['robotstxt']) )
						$__ret['msg']['rt'] = '<span class="psp-fe-err">' . sprintf( __('Insufficient rights to update %s file!', 'psp'), 'robots.txt' ) . '</span>';
					if ( isset($_POST['htaccess']) )
						$__ret['msg']['ht'] = '<span class="psp-fe-err">' . sprintf( __('Insufficient rights to update %s file!', 'psp'), '.htaccess' ) . '</span>';
					return $__ret;
				}
				check_admin_referer('psp-file-edit-changes');

				$__current_time = time();
				if ( isset($_POST['robotstxt']) ) {
					$__rt = $this->saveFile('robots.txt', stripslashes($_POST['robotstxt']));

					if ( $__rt ) {
						$saveDb['robotstxt'] = stripslashes($_POST['robotstxt']);
						$saveDb['robotstxt_saved_time'] = $__current_time;
						if ( is_null($saveDb_orig['robotstxt']) ) {
							$saveDb_orig['robotstxt'] = stripslashes($_POST['robotstxt']);
							$saveDb_orig['robotstxt_saved_time'] = $__current_time;
						}
					}
				}
					
				if ( isset($_POST['htaccess']) ) {
					$__ht = $this->saveFile('.htaccess', stripslashes($_POST['htaccess']));

					if ( $__ht ) {
						$saveDb['htaccess'] = stripslashes($_POST['htaccess']);
						$saveDb['htaccess_saved_time'] = $__current_time;
						if ( is_null($saveDb_orig['htaccess']) ) {
							$saveDb_orig['htaccess'] = stripslashes($_POST['htaccess']);
							$saveDb_orig['htaccess_saved_time'] = $__current_time;
						}
					}
				}
				
				$this->the_plugin->save_theoption( 'psp_file_edit', $saveDb );
				$this->the_plugin->save_theoption( 'psp_file_edit_orig', $saveDb_orig );
			}
			$__ret = array_merge(array(
				'robotstxt'	=> isset($__rt) ? $__rt : false,
				'htaccess'	=> isset($__ht) ? $__ht : false
			));
			return $__ret;
		}
		private function createRobotsTxt() {
			$__ret = array(
				'status'	=> false,
				'msg'		=> ''
			);
			$__fileFullPath = $this->get_home_path() . 'robots.txt';
					$__fileHandler = fopen($__fileFullPath, 'w+b'); //open with binary safe
					$content = 'User-Agent: *
Disallow: /wp-content/plugins/';
					$__fileContent = fwrite($__fileHandler, $content);
					fclose($__fileHandler);
					
					$__ret = array_merge($__ret, array(
						'status'	=> true,
						'msg'		=> '<span class="psp-fe-msg">' . sprintf( __('The file %s was updated successfully!', 'psp'), $file ) . '</span>'
					));
				return $__ret;
		}
		
		private function saveFile($file, $content) {
			$__ret = array(
				'status'	=> false,
				'msg'		=> ''
			);
			$__fileFullPath = $this->get_home_path() . $file;

			//verify file existance!
			if ($this->verifyFileExists($__fileFullPath)) {
				//verify file is writable!
				clearstatcache();
				if (is_writable($__fileFullPath)) {
					$__fileHandler = fopen($__fileFullPath, 'w+b'); //open with binary safe
					$__fileContent = fwrite($__fileHandler, $content);
					fclose($__fileHandler);
					
					$__ret = array_merge($__ret, array(
						'status'	=> true,
						'msg'		=> '<span class="psp-fe-msg">' . sprintf( __('The file %s was updated successfully!', 'psp'), $file ) . '</span>'
					));
				} else {
					$__ret['msg'] = '<span class="psp-fe-err">' . sprintf( __('The file %s is unwritable!', 'psp'), $file ) . '</span>';
				}
				return $__ret;
			}
			$__ret['msg'] = '<span class="psp-fe-err">' . sprintf( __('The file %s does not exist or it\'s unreadable!', 'psp'), $file ) . '</span>';
			return $__ret;
		}
		
		private function verify_htaccess() {
			global $is_apache;
			if ($is_apache) {
				return $this->getFile('.htaccess');
			}
			return false;
		}
		
		private function getFile($file) {
			$__ret = array(
				'status'	=> 'hidden',
				'content'	=> '',
				'msg'		=> ''
			);
			$__fileFullPath = $this->get_home_path() . $file;
  
			//verify file existance!
			if ($this->verifyFileExists($__fileFullPath)) {
				$__fileSize = @filesize($__fileFullPath);

				$__fileContent = '';
				$__ret['status'] = 'disabled';
				if ($__fileSize>0) {
					$__fileHandler = fopen($__fileFullPath, 'rb'); //open with binary safe
					$__fileContent = fread($__fileHandler, $__fileSize);
					fclose($__fileHandler);
					$__fileContent = esc_textarea($__fileContent);
					
					$__ret['content'] = $__fileContent;
				}
			} else {
				$__ret['msg'] = '<span class="psp-fe-err">' . sprintf( __('The file %s does not exist or it\'s unreadable!', 'psp'), $file ) . '</span>';
				return $__ret;
			}

			//verify file is writable!
			clearstatcache();
			if (is_writable($__fileFullPath)) {
				$__ret['status'] = 'active';
			}
			else {
				$__ret['msg'] = '<span class="psp-fe-err">' . sprintf( __('The file %s is unwritable!', 'psp'), $file ) . '</span>';
			}
			return $__ret;
		}
		
		//verify if file exists!
		private function verifyFileExists($file, $type='file') {
			clearstatcache();
			if ($type=='file') {
				if (!file_exists($file) || !is_file($file) || !is_readable($file)) {
					return false;
				}
				return true;
			} else if ($type=='folder') {
				if (!is_dir($file) || !is_readable($file)) {
					return false;
				}
				return true;
			}
			// invalid type
			return 0;
		}
		
		//remove empty entries of an array recursively
		private function removeEmptyItems(&$item) {
			if (is_array($item) && $item) {
				$item = array_filter( $item, array( $this, 'removeEmptyItems' ));
			}
			return !!$item;
		}

		/*
		* ajax_request, method
		* --------------------
		*
		* this will create requests to 404 table
		*/
		public function ajax_request()
		{
			//echo __FILE__ . ":" . __LINE__;die . PHP_EOL;   
			global $wpdb;
			$request = array(
				'rt' 			=> isset($_REQUEST['rt']) ? trim( $_REQUEST['rt'] ) : '',
				'ht' 			=> isset($_REQUEST['ht']) ? trim( $_REQUEST['ht'] ) : '',
				'rtCreate' 		=> isset($_REQUEST['rtCreate']) ? $this->createRobotsTxt() : '',
				
			);
			
			die( json_encode(array(
				'status' => 'valid',
				'data'	=> $request
			)) );
		}
		
		private function get_home_path() {
			$home = get_option( 'home' );
			$siteurl = get_option( 'siteurl' );
			
			$home = preg_replace('/^.*?:\/\//','',get_option( 'home' ));
			$siteurl = preg_replace('/^.*?:\/\//','',get_option( 'siteurl' ));
			if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {
				$wp_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */
				$pos = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );
				$home_path = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );
				$home_path = trailingslashit( $home_path );
			} else {
				$home_path = ABSPATH;
			}
			return str_replace( '\\', '/', $home_path );
		}
    }
}

// Initialize the pspFileEdit class
//$pspFileEdit = new pspFileEdit();
$pspFileEdit = pspFileEdit::getInstance();