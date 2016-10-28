<?php
/*
* Define class pspLinkRedirect
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspLinkRedirect') != true) {
    class pspLinkRedirect
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
		
		private $settings = array();

		static protected $_instance;
		
		/*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;
        	
        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/Link_Redirect/';
			$this->module = $this->the_plugin->cfg['modules']['Link_Redirect'];

			if ( $this->the_plugin->is_admin === true ) {
	            add_action('admin_menu', array( &$this, 'adminMenu' ));
				
				$this->settings = $this->the_plugin->getAllSettings( 'array', 'Link_Redirect' );

				// ajax handler
				add_action('wp_ajax_pspGetUpdateDataRedirect', array( &$this, 'ajax_request' ));
				add_action('wp_ajax_pspAddToRedirect', array( &$this, 'addToRedirect' ));
				add_action('wp_ajax_pspRemoveFromRedirect', array( &$this, 'removeFromRedirect' ));
				add_action('wp_ajax_pspUpdateToRedirect', array( &$this, 'updateToRedirect' ));
				
				//delete bulk rows!
				add_action('wp_ajax_pspLinkRedirect_do_bulk_delete_rows', array( &$this, 'delete_rows' ));
			}
			
			// init module!
			if ( $this->the_plugin->is_admin !== true ) {
				$this->init();
			}
        }
        
		private function init() {
			if ( !$this->the_plugin->verify_module_status( 'Link_Redirect' ) ) ; //module is inactive
			else {
				//if ( $this->the_plugin->capabilities_user_has_module('Link_Redirect') ) {
					$this->addFrontFilters();
				//}
			}
			//$this->createTable();
		}

		/**
	    * Singleton pattern
	    *
	    * @return pspLinkRedirect Singleton instance
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
    		if ( $this->the_plugin->capabilities_user_has_module('Link_Redirect') ) {
	    		add_submenu_page(
	    			$this->the_plugin->alias,
	    			$this->the_plugin->alias . " " . __('301 Link Redirect', 'psp'),
		            __('301 Link Redirect', 'psp'),
		            'read',
		            $this->the_plugin->alias . "_Link_Redirect",
		            array($this, 'display_index_page')
		        );
    		}

			return $this;
		}

		public function display_meta_box()
		{
			if ( $this->the_plugin->capabilities_user_has_module('Link_Redirect') ) {
				$this->printBoxInterface();
			}
		}

		public function display_index_page()
		{
			$this->printBaseInterface();
		}
		
		
		/**
		 * frontend methods: replace phrase with link!
		 *
		 */
		public function addFrontFilters() {
			add_action('wp', array( &$this, 'redirect_header' ), 0);
		}
		
		public function redirect_header(){
			global $wpdb, $wp;

			$currentUri = home_url(add_query_arg(array(), $wp->request));
			$currentUri = preg_replace('/\+{2,}/imu', '+', $currentUri);

			if ( !is_admin() ) {

				// get url redirect for current URI
				$__redirect = $this->getUrlRedirect( $currentUri );
				if ($__redirect===false || is_null($__redirect)) return true;
				
				// update hits!
				$this->updateUrlHits( $__redirect['id'] );
				
				$__redirect = $__redirect['url_redirect'];
				if ( preg_match('/^http|https:\/\//i', $__redirect) > 0 ) ;
				else 
					$__redirect = 'http://' . $__redirect;

				wp_redirect( $__redirect, 301 );
				exit();
			}
		}
		
		private function getUrlRedirect( $url='' ) {
			global $wpdb;
			
			if (trim($url)=='') return false;

			//$sql = "SELECT a.id, a.url_redirect from " . $wpdb->prefix . "psp_link_redirect as a WHERE 1=1 and a.url=%s;";
			//$sql = $wpdb->prepare( $sql, $url );
			$sql = "SELECT a.id, a.url_redirect from " . $wpdb->prefix . "psp_link_redirect as a WHERE 1=1 and a.url regexp '^".$url."/?$';";
			$res = $wpdb->get_row( $sql, ARRAY_A );
			return $res;
		}
		
		private function updateUrlHits( $id=0 ) {
			global $wpdb;
			
			$table_name = $wpdb->prefix . "psp_link_redirect";
			$query_update = "UPDATE " . ($table_name) . " set
						hits=hits+1
						where id='$id'";
			$wpdb->query($query_update);
		}
		
		
		/**
		 * backend methods: build the admin interface
		 *
		 */
		private function createTable() {
			global $wpdb;
			
			// check if table exist, if not create table
			$table_name = $wpdb->prefix . "psp_link_redirect";
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name) {

				$sql = "
					CREATE TABLE IF NOT EXISTS " . $table_name . " (
					  `id` int(10) NOT NULL AUTO_INCREMENT,
					  `hits` int(10) DEFAULT '0',
					  `url` varchar(150) DEFAULT NULL,
					  `url_redirect` varchar(150) DEFAULT NULL,
					  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  PRIMARY KEY (`id`),
					  UNIQUE INDEX `unique` (`url`,`url_redirect`)
					);
					";

				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

				dbDelta($sql);
			}
		}
		
		/*
		* addToRedirect, method
		* ---------------------
		*
		* add new row into link redirect table 
		*/
		public function addToRedirect( $info=array() )
		{
			global $wpdb;
			$request = array(
				'url' 			=> isset($_REQUEST['new_url']) ? trim($_REQUEST['new_url']) : '',
				'url_redirect'	=> isset($_REQUEST['new_url_redirect']) ? trim($_REQUEST['new_url_redirect']) : '',
				'hits' 			=> isset($_REQUEST['new_hits']) ? trim($_REQUEST['new_hits']) : '0',
				
				'itemid' 		=> isset($_REQUEST['itemid']) ? trim($_REQUEST['itemid']) : $itemid
			);

			if ($request['url']=='' || $request['url_redirect']=='') {
					die(json_encode(array(
						'status' => 'invalid',
						'data' => ''
					)));
			}

				$wpdb->insert( 
					$wpdb->prefix . "psp_link_redirect", 
					array( 
						'url' 			=> $request['url'],
						'url_redirect' 	=> $request['url_redirect'],
						'hits'			=> $request['hits']
					), 
					array( 
						'%s',
						'%s',
						'%d'
					)
				);
				$insert_id = $wpdb->insert_id;
				if ($insert_id<=0) {
					die(json_encode(array(
						'status' => 'invalid',
						'data' => $wpdb->last_query
					)));
				}
				
			//keep page number & items number per page
			$_SESSION['pspListTable']['keepvar'] = array('posts_per_page'=>true);

			// return for ajax
			die(json_encode( array(
				'status' => 'valid',
				'data' => $wpdb->last_query
			)));
		}
		
		/*
		* updateToRedirect, method
		* --------------------------
		*
		* update row from link redirect table
		*/
		public function updateToRedirect()
		{
			global $wpdb;
			
			$request = array(
				'itemid' 		=> isset($_REQUEST['itemid']) ? (int)$_REQUEST['itemid'] : 0,
				'subaction' 	=> isset($_REQUEST['subaction']) ? trim($_REQUEST['subaction']) : '',
				'url_redirect'	=> isset($_REQUEST['new_url_redirect2']) ? trim($_REQUEST['new_url_redirect2']) : ''
			);
			
			if( $request['itemid'] > 0 ) {
				$row = $wpdb->get_row( "SELECT * FROM " . ( $wpdb->prefix ) . "psp_link_redirect WHERE id = '" . ( $request['itemid'] ) . "'", ARRAY_A );
				
				$row_id = (int)$row['id'];

				if ($row_id>0) {
				
						// update row info!
						$wpdb->update( 
							$wpdb->prefix . "psp_link_redirect", 
							array( 
								'url_redirect'		=> $request['url_redirect']
							), 
							array( 'id' => $row_id ), 
							array( 
								'%s'
							), 
							array( '%d' ) 
						);
						
						//keep page number & items number per page
						$_SESSION['pspListTable']['keepvar'] = array('paged'=>true,'posts_per_page'=>true);
					
						die(json_encode(array(
							'status' => 'valid'
						)));
				
				}

			}
			
			die(json_encode(array(
				'status' => 'invalid'
			)));
		}
		
		/*
		* removeFromTable method
		* --------------------------
		*
		* remove (url,phrase) pair from table!
		*/
		public function removeFromRedirect()
		{
			global $wpdb;
			
			$request = array(
				'itemid' 	=> isset($_REQUEST['itemid']) ? (int)$_REQUEST['itemid'] : 0
			);
			
			if( $request['itemid'] > 0 ) {
				$wpdb->delete( 
					$wpdb->prefix . "psp_link_redirect", 
					array( 'id' => $request['itemid'] ) 
				);
				
				//keep page number & items number per page
				$_SESSION['pspListTable']['keepvar'] = array('posts_per_page'=>true);
				
				die(json_encode(array(
					'status' => 'valid'
				)));
			}
			
			die(json_encode(array(
				'status' => 'invalid'
			)));
		}
		
		/**
		 * delete Bulk rows!
		 */
		public function delete_rows() {
			global $wpdb; // this is how you get access to the database
			
			$request = array(
				'id' 			=> isset($_REQUEST['id']) && !empty($_REQUEST['id']) ? trim($_REQUEST['id']) : 0
			);

			if ($request['id']!=0) {
				$__rq2 = array();
				$__rq = explode(',', $request['id']);
				if (is_array($__rq) && count($__rq)>0) {
					foreach ($__rq as $k=>$v) {
						$__rq2[] = (int) $v;
					}
				} else {
					$__rq2[] = $__rq;
				}
				$request['id'] = implode(',', $__rq2);
			}

			$table_name = $wpdb->prefix . "psp_link_redirect";
			if ($wpdb->get_var("show tables like '$table_name'") == $table_name) {

				// delete record
				$query_delete = "DELETE FROM " . ($table_name) . " where 1=1 and id in (" . ($request['id']) . ");";
				$__stat = $wpdb->query($query_delete);
				
				/*$query_update = "UPDATE " . ($table_name) . " set
						deleted=1
						where id in (" . ($request['id']) . ");";
				$__stat = $wpdb->query($query_update);*/
				
				if ($__stat!== false) {
					//keep page number & items number per page
					$_SESSION['pspListTable']['keepvar'] = array('posts_per_page'=>true);

					die( json_encode(array(
						'status' => 'valid',
						'msg'	 => '' //$query_delete
					)) );
				}
			}
			
			die( json_encode(array(
				'status' => 'invalid',
				'msg'	 => ''
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
?>
		<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
		<link rel='stylesheet' href='<?php echo $this->module_folder;?>app.css' type='text/css' media='all' />
		<div id="psp-wrapper" class="fluid wrapper-psp">
			<?php
			// show the top menu
			pspAdminMenu::getInstance()->make_active('off_page_optimization|Link_Redirect')->show_menu();
			?>
			
			<div id="psp-lightbox-overlay">
				<div id="psp-lightbox-container">
					<h1 class="psp-lightbox-headline">
						<img class="psp-lightbox-icon" src="<?php echo $this->the_plugin->cfg['paths']['freamwork_dir_url'];?>images/light-bulb.png">
						<span id="link-title-add"><?php _e('Add new link:', 'psp');?></span>
						<span id="link-title-upd"><?php _e('Update link:', 'psp');?></span>
						<a href="#" class="psp-close-btn" title="<?php _e('Close Lightbox', 'psp'); ?>"></a>
					</h1>

					<div class="psp-seo-status-container">
						<div id="psp-lightbox-seo-report-response">
							<form class="psp-add-link-form">
								<table width="100%">
									<tr>
										<td width="80"><label><?php _e('URL:', 'psp');?></label></td>
										<td><input type="text" id="new_url" name="new_url" value="" class="psp-add-link-field" /></td>
									</tr>
									<tr>
										<td><label><?php _e('URL Redirect:', 'psp');?></label></td>
										<td><input type="text" id="new_url_redirect" name="new_url_redirect" value="" class="psp-add-link-field" /></td>
									</tr>
									<tr>
										<td></td>
										<td>
											<input type="button" class="psp-button green" value="<?php _e('Add this new link', 'psp'); ?>" id="psp-submit-to-builder">
										</td>
									</tr>
								</table>
								
							</form>
						</div>
						
						<div id="psp-lightbox-seo-report-response2">
							<form class="psp-update-link-form">
								<input type="hidden" id="upd-itemid" name="upd-itemid" value="" />
								<table width="100%">
									<tr>
										<td width="80"><label><?php _e('URL:', 'psp');?></label></td>
										<td><input type="text" id="new_url2" name="new_url2" value="" class="psp-add-link-field" readonly disabled="disabled" /></td>
									</tr>
									<tr>
										<td><label><?php _e('URL Redirect:', 'psp');?></label></td>
										<td><input type="text" id="new_url_redirect2" name="new_url_redirect2" value="" class="psp-add-link-field" /></td>
									</tr>
									<tr>
										<td></td>
										<td>
											<input type="button" class="psp-button green" value="<?php _e('Update link info', 'psp'); ?>" id="psp-submit-to-builder2">
										</td>
									</tr>
								</table>
								
							</form>
						</div>
						<div style="clear:both"></div>
					</div>
				</div>
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
					<?php echo $this->module['Link_Redirect']['menu']['title'];?>
					<span class="psp-section-info"><?php echo $this->module['Link_Redirect']['description'];?></span>
					<?php
					$has_help = isset($this->module['Link_Redirect']['help']) ? true : false;
					if( $has_help === true ){
						
						$help_type = isset($this->module['Link_Redirect']['help']['type']) && $this->module['Link_Redirect']['help']['type'] ? 'remote' : 'local';
						if( $help_type == 'remote' ){
							echo '<a href="#load_docs" class="psp-show-docs" data-helptype="' . ( $help_type ) . '" data-url="' . ( $this->module['Link_Redirect']['help']['url'] ) . '">HELP</a>';
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
											<?php /*<img src="<?php echo $this->the_plugin->cfg['paths']['plugin_dir_url'];?>/modules/Social_Stats/assets/menu_icon.png">*/ ?>
											<?php _e('301 Link Redirect', 'psp');?>
										</span>
									</div>
									<div class="psp-panel-content">
										<form class="psp-form" id="1" action="#save_with_ajax">
											<div class="psp-form-row psp-table-ajax-list" id="psp-table-ajax-response">
											<?php
											pspAjaxListTable::getInstance( $this->the_plugin )
												->setup(array(
													'id' 				=> 'pspLinkRedirect',
													'custom_table'		=> "psp_link_redirect",
													'custom_table_force_action' => true,
													//'deleted_field'		=> true,
													'force_publish_field'=> false,
													'show_header' 		=> true,
													'items_per_page' 	=> '10',
													'post_statuses' 	=> 'all',
													'columns'			=> array(
														'checkbox'	=> array(
															'th'	=>  'checkbox',
															'td'	=>  'checkbox',
														),

														'id'		=> array(
															'th'	=> __('ID', 'psp'),
															'td'	=> '%id%',
															'width' => '20'
														),

														'hits'		=> array(
															'th'	=> __('Hits', 'psp'),
															'td'	=> '%hits%',
															'width' => '15'
														),

														'url'		=> array(
															'th'	=> __('URL', 'psp'),
															'td'	=> '%linkred_url%',
															'align' => 'left'
														),

														'url_redirect'		=> array(
															'th'	=> __('URL Redirect', 'psp'),
															'td'	=> '%linkred_url_redirect%',
															'align' => 'left'
														),
														
														'created'		=> array(
															'th'	=> __('Creation Date', 'psp'),
															'td'	=> '%created%',
															'width' => '115'
														),
														
																'update_btn' => array(
																	'th'	=> __('Update', 'psp'),
																	'td'	=> '%button%',
																	'option' => array(
																		'value' => __('Update', 'psp'),
																		'action' => 'do_item_update',
																		'color'	=> 'blue',
																	),
																	'width' => '30'
																),
					
																'delete_btn' => array(
																	'th'	=> __('Delete', 'psp'),
																	'td'	=> '%button%',
																	'option' => array(
																		'value' => __('Delete', 'psp'),
																		'action' => 'do_item_delete',
																		'color'	=> 'red',
																	),
																	'width' => '30'
																)
													),
													'mass_actions' 	=> array(
														'add_new_link' => array(
															'value' => __('Add new link', 'psp'),
															'action' => 'do_add_new_link',
															'color' => 'blue'
														),
														'delete_all_rows' => array(
															'value' => __('Delete selected rows', 'psp'),
															'action' => 'do_bulk_delete_rows',
															'color' => 'red'
														)
													)
												))
												->print_html();
								            ?>
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
		
		public function ajax_request()
		{
			global $wpdb;

			$request = array(
				'itemid' 		=> isset($_REQUEST['itemid']) ? (int)$_REQUEST['itemid'] : 0
			);
			
			die( json_encode(array(
				'status' => 'valid',
				'data'	=> $wpdb->get_row( "SELECT * from " . $wpdb->prefix . "psp_link_redirect WHERE 1=1 and id=" . ( $request['itemid'] ) . ";" )
			)) );
		}
		
		private function prepareForInList($v) {
			return "'".$v."'";
		}

    }
}

// Initialize the pspLinkRedirect class
$pspLinkRedirect = pspLinkRedirect::getInstance();