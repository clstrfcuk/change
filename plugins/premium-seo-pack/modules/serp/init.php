<?php
/*
* Define class pspSERP
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspSERP') != true) {
    class pspSERP
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
		
		private $plugin_settings = array();
		
		private $search_engine = 'google'; //search engine used from plugin serp settings!
		private $serp_sleep = 0;
		
		private $__initialDate = array();
		private $__defaultClause = '';

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;
        	
        	$this->serp_sleep = rand(30,55); //in seconds: serp sleep between consecutive requests!

        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/serp/';
			$this->module = $this->the_plugin->cfg['modules']['serp'];

			$this->plugin_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . '_serp' );
			$this->search_engine .= ('.' . $this->plugin_settings['google_country']);

			if (is_admin()) {
	            add_action('admin_menu', array( &$this, 'adminMenu' ));
			}
			
			// ajax  helper
			if ( $this->the_plugin->is_admin === true ) {
				add_action('wp_ajax_pspGetFocusKW', array( &$this, '__getFocusKW' ));
				add_action('wp_ajax_pspAddToReporter', array( &$this, 'addToReporter' ));
				add_action('wp_ajax_pspRemoveFromReporter', array( &$this, 'removeFromReporter' ));
				add_action('wp_ajax_pspUpdateToReporter', array( &$this, 'updateToReporter' ));
				add_action('wp_ajax_pspGetSERPGraphData', array( &$this, 'getSERPGraphData' ));
				add_action('wp_ajax_pspSetSearchEngine', array( &$this, 'setSearchEngine' ));
				
				add_action('wp_ajax_pspGetEngineAccessTime', array( &$this, '__getEngineAccessTime' ));
			}

			if ( !$this->the_plugin->verify_module_status( 'serp' ) ) ; //module is inactive
			else {
				// visits!
				if ( $this->the_plugin->is_admin !== true )
					add_action('wp_head',  array( &$this, 'save_visits' ));
			}
			
			// cron to check all serp rows!
			// wp_schedule_event(time(), 'daily', 'psp_start_cron_serp_check'); //plugin activation daily|hourly
			// add_action('psp_start_cron_serp_check', array( &$this, 'check_reporter' ));
			// wp_clear_scheduled_hook('psp_start_cron_serp_check'); //plugin deactivation
			// add_filter( 'cron_schedules', array( &$this, 'cron_add_custom' ));
			
			if ( $this->the_plugin->is_admin === true ) {
			
			$this->__initialDate = $this->getInitialData(); //initial date!
			if ( empty($this->__initialDate) )
				$this->__initialDate = array( date( 'Y-m-d' ) => 1  );
			$this->__initialDate = array(
				'from' 	=> date( 'Y-m-d', strtotime( "-1 week", strtotime( key($this->__initialDate) ) ) ),
				'to' 	=> date( 'Y-m-d', strtotime( key($this->__initialDate) ) )
			);
			$engine = '';
			if (isset($_SESSION['psp_serp']['search_engine']) && !empty($_SESSION['psp_serp']['search_engine'])
			&& $_SESSION['psp_serp']['search_engine']!='--all--')
				$engine = $_SESSION['psp_serp']['search_engine'];

			$this->__defaultClause = $this->getDefaultClause(array(
				'engine'	=> $engine,
				'from_date'	=> $this->__initialDate['from'],
				'to_date'	=> $this->__initialDate['to'],
			));
			
			}
        }
        
		/**
	    * Singleton pattern
	    *
	    * @return pspSERP Singleton instance
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
    		if ( $this->the_plugin->capabilities_user_has_module('serp') ) {
	    		add_submenu_page(
	    			$this->the_plugin->alias,
	    			__('Search Engine Results Page Tracking', 'psp'),
		            __('SERP Tracking', 'psp'),
		            'read',
		            $this->the_plugin->alias . "_SERP",
		            array($this, 'display_index_page')
		        );
    		}

			return $this;
		}

		public function display_meta_box()
		{
			if ( $this->the_plugin->capabilities_user_has_module('serp') ) {
				$this->printBoxInterface();
			}
		}
		
		public function save_visits()
		{
			global $post, $wpdb;
			
			//Due to late-2011 Google security changes, this is no longer possible when the search was performed by a signed-in Google user!
			//referrer ex: http://www.google.fi/search?hl=en&q=http+header+referer&btnG=Google-search&meta=&aq=f&oq=

			$referrer = $_SERVER['HTTP_REFERER'];
			$searchEngines = $this->getSearchEngineUsed();
			$currentPage = $_SERVER['REQUEST_URI'];
			$search_engine = ''; $postId = 0;
			
			//not from admin!
			if (!is_user_logged_in() && count($searchEngines)>0) {
			//if (1) { //debug!
				$__referrer = parse_url($referrer); //search engine reffer url!
				//$__referrer['host'] = 'google.com'; //debug!
				foreach ($searchEngines as $k=>$v) {
					$search_engine = $v;
					if (preg_match("/".str_replace('.', '\.', $search_engine)."$/i", $__referrer['host'])) {
						parse_str($__referrer['query'], $__query);
						$__q = strtolower(trim($__query['q'])); // searched keyword!
						$__q = htmlspecialchars(stripslashes($__q), ENT_QUOTES);
						if ($__q!='') { // non empty keyword!
						//if (1) { //debug!
							if (preg_match("/post-([0-9]+)\//i", $currentPage, $__m)) $postId = $__m[1];
							else if (preg_match("/page-([0-9]+)\//i", $currentPage, $__m)) $postId = $__m[1];
						}
					}
				}
			}

			// update reported row!
			if ($search_engine!='' && $postId>0) {
				// check if you already have this info into DB
				$reporterSql = $wpdb->prepare( "SELECT a.id as rowid, a.* FROM " . ( $wpdb->prefix ) . "psp_serp_reporter as a WHERE 1=1 AND a.search_engine=%s AND a.post_id=%s LIMIT 1", $search_engine, $postId );

				$row = $wpdb->get_row( $reporterSql, ARRAY_A );
				$row_id = (int) $row['rowid'];

				// if not found
				if( $row_id > 0 ) {

					// update report - previous, worst, best rank!
					$wpdb->update(
						$wpdb->prefix . "psp_serp_reporter",
						array(
							'visits'		=> (int) ($row['visits'] + 1)
						),
						array( 'post_id' => $postId, 'search_engine' => $search_engine ),
						array(
							'%d'
						),
						array( '%d', '%s' )
					);
				}
			}
		}

		public function display_index_page()
		{
			$this->printBaseInterface();
		}
		
		public function moduleValidation() {
			$ret = array(
				'status'			=> false,
				'html'				=> ''
			);
			
			// find if user makes the setup
			$module_settings = $serp_settings = $this->the_plugin->get_theoption( $this->the_plugin->alias . "_serp" );

			$serp_mandatoryFields = array(
				'developer_key'			=> false,
				'custom_search_id'		=> false,
				'google_country'		=> false
			);
			if ( isset($serp_settings['developer_key']) && !empty($serp_settings['developer_key']) ) {
				$serp_mandatoryFields['developer_key'] = true;
			}
			if ( isset($serp_settings['custom_search_id']) && !empty($serp_settings['custom_search_id']) ) {
				$serp_mandatoryFields['custom_search_id'] = true;
			}
			if ( isset($serp_settings['google_country']) && !empty($serp_settings['google_country']) ) {
				$serp_mandatoryFields['google_country'] = true;
			}
			$mandatoryValid = true;
			foreach ($serp_mandatoryFields as $k=>$v) {
				if ( !$v ) {
					$mandatoryValid = false;
					break;
				}
			}
			if ( !$mandatoryValid ) {
				$error_number = 1; // from config.php / errors key
				
				$ret['html'] = $this->the_plugin->print_module_error( $this->module, $error_number, 'Error: Unable to use Google Serp module, yet!' );
				return $ret;
			}
			$ret['status'] = true;
			return $ret;
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
		<link rel='stylesheet' href='<?php echo $this->module_folder;?>app.css' type='text/css' media='screen' />
		<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
		<div id="psp-wrapper" class="fluid wrapper-psp">
			
			<?php
			// show the top menu
			pspAdminMenu::getInstance()->make_active('monitoring|serp')->show_menu();
			?>
			
			<div id="psp-lightbox-overlay">
				<div id="psp-lightbox-container">
					<h1 class="psp-lightbox-headline">
						<img class="psp-lightbox-icon" src="<?php echo $this->the_plugin->cfg['paths']['freamwork_dir_url'];?>images/light-bulb.png">
						<span><?php _e('Your focus keywords list', 'psp');?></span>
					<a href="#" class="psp-close-btn" title="<?php _e('Close Lightbox', 'psp'); ?>"></a>
					</h1>

					<div class="psp-seo-status-container">
						<div id="psp-lightbox-seo-report-response"></div>
						<div style="clear:both"></div>
					</div>
				</div>
			</div>
			
			<!-- Main loading box -->
			<div id="psp-main-loading" style="display:block;">
				<div id="psp-loading-overlay" style="display:block;"></div>
				<div id="psp-loading-box" style="display:block;">
					<div class="psp-loading-text"><?php _e('Loading', 'psp');?></div>
					<div class="psp-meter psp-animate" style="width:86%; margin: 34px 0px 0px 7%;"><span style="width:100%"></span></div>
				</div>
			</div>

			<!-- Content -->
			<div id="psp-content">
				
				<h1 class="psp-section-headline">
					<?php echo $this->module['serp']['menu']['title'];?>
					<span class="psp-section-info"><?php echo $this->module['serp']['description'];?></span>
					<?php
					$has_help = isset($this->module['serp']['help']) ? true : false;
					if( $has_help === true ){
						
						$help_type = isset($this->module['serp']['help']['type']) && $this->module['serp']['help']['type'] ? 'remote' : 'local';
						if( $help_type == 'remote' ){
							echo '<a href="#load_docs" class="psp-show-docs" data-helptype="' . ( $help_type ) . '" data-url="' . ( $this->module['serp']['help']['url'] ) . '">HELP</a>';
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
											<?php /*<img src="<?php echo $this->module_folder;?>assets/serp-icon.png">*/ ?>
											<?php _e('Search Engine Results Page Reporter', 'psp');?>
										</span>
									</div>
									<div class="psp-panel-content">
										<form class="psp-form" action="#save_with_ajax">
											<div class="psp-form-row" id="psp-serp-container">
												<div class="psp-top-filters">

													<span id="psp-select-engine-text"><?php _e('Google:', 'psp');?> </span>
													
													<select id="select-engine">
													<option value='--all--'><?php _e('all locations', 'psp');?></option>
													<?php
													$__seUsed = $this->getSearchEngineUsed();
													foreach ($__seUsed as $__k=>$__v) {
														echo '<option value="'.$__v.'" '.(isset($_SESSION['psp_serp']['search_engine']) && $__v==$_SESSION['psp_serp']['search_engine'] ? ' selected ' : '').'>'.$__v.'</option>';
													}
													?>
													</select>

													<?php /*<input type="button" class="psp-button blue" id="psp-cron-ckeck" value="<?php _e('Cron check now', 'psp');?>">*/ ?>
													
													<div id="psp-filter-by-date">
														<label for="psp-filter-by-date-from"><?php _e('From:', 'psp');?></label>
														<input type="text" id="psp-filter-by-date-from" name="psp-filter-by-date-from" value="<?php echo $this->__initialDate['from']; ?>" />
														<label for="psp-filter-by-date-to"><?php _e('to', 'psp');?></label>
														<input type="text" id="psp-filter-by-date-to" name="psp-filter-by-date-to" value="<?php echo $this->__initialDate['to']; ?>" />
														<input type="button" id="psp-toggle-ku" class="psp-button gray psp-select-fw" value="Customize view">
														<input type="button" class="psp-button blue" id="psp-filter-graph-data" value="<?php _e('Apply Filters', 'psp');?>">
													</div>
												</div>
												
												<!-- keywords, url filter -->
												<div class="psp-panel psp-serp-filter-keyurl" id="psp-serp-filter-keyurl">
					                        		<div class="psp-panel-header">
														<span class="psp-panel-title">
															<?php _e('Filter Focus Keywords and URLs', 'psp');?>
														</span>
													</div>
													<div class="psp-panel-content psp-serp-filter-keyurl-content">
														<div style="float:left; width:49%; height: 200px; overflow: auto; margin-right: 1%;">
														<table class="psp-table" style="border:  none;border-bottom:  1px solid #dadada;width:100%;  border-spacing:0;border-collapse:collapse;">
															<thead>
																<tr>
																	<th align="left" width="10"><input type="checkbox" id="psp-item-check-all-key"></th>
																	<th align="left"><?php _e('Focus Keywords', 'psp');?></th>
																</tr>
															</thead>
															
															<tbody>
															<?php
															$__keys = $this->getKeywordsList();
															if (count($__keys)>0) {
																$__theHtml = array();
																foreach ($__keys as $k=>$v) {
																	$__theHtml[] = '<tr>';
																	$__theHtml[] = 		'<td align="left">';
																	$__theHtml[] = 			'<input type="checkbox" class="psp-item-checkbox-key" name="psp-item-checkbox-key-'.$v['id'].'" value="'.$v['info'].'"' . (isset($_SESSION['psp_serp']['filter_keywords']) && isset($_SESSION['psp_serp']['filter_keywords'][$v['info']]) ? ' checked="checked" ' : '') . '>';
																	$__theHtml[] = 		'</td>';
																	$__theHtml[] = 		'<td align="left">';
																	$__theHtml[] = 			$v['info'];
																	$__theHtml[] = 		'</td>';
																	$__theHtml[] = '<tr>';
																}
																echo implode('', $__theHtml);
															}
															?>
															</tbody>
														</table>
														</div>
														<div style="float:left; width:50%; height: 200px; overflow: auto;">
														 <table class="psp-table" style="border: none;border-left: 1px solid #ededed;border-bottom: 1px solid #dadada;width:100%;border-spacing:0;border-collapse:collapse;">
															<thead>
																<tr>
																	<th align="left" width="10"><input type="checkbox" id="psp-item-check-all-url"></th>
																	<th align="left"><?php _e('URLs', 'psp');?></th>
																</tr>
															</thead>
															
															<tbody>
															<?php
															$__keys = $this->getUrlsList();
															if (count($__keys)>0) {
																$__theHtml = array();
																foreach ($__keys as $k=>$v) {
																	$__theHtml[] = '<tr>';
																	$__theHtml[] = 		'<td align="left">';
																	$__theHtml[] = 			'<input type="checkbox" class="psp-item-checkbox-url" name="psp-item-checkbox-url-'.$v['id'].'" value="'.$v['info'].'"' . (isset($_SESSION['psp_serp']['filter_urls']) && isset($_SESSION['psp_serp']['filter_urls'][$v['info']]) ? ' checked="checked" ' : '') . '>';
																	$__theHtml[] = 		'</td>';
																	$__theHtml[] = 		'<td align="left">';
																	$__theHtml[] = 			$v['info'];
																	$__theHtml[] = 		'</td>';
																	$__theHtml[] = '<tr>';
																}
																echo implode('', $__theHtml);
															}
															?>
															</tbody>
														</table>
														</div>
														<div style="clear:left;"></div>
													</div>
												</div>
												
												<div class="psp-serp-graph" id="psp-serp-graph"></div>
												
												<div class="psp-panel psp-serp-add-keyword" id="psp-serp-add-keyword">
					                        		<div class="psp-panel-header">
														<span class="psp-panel-title">
															<img src="<?php echo $this->module_folder;?>assets/new-kw.png">
															<?php _e('Add Keyword <i>(You can keep an eye on your competitors too)</i>', 'psp');?>
															<span id="search-engine-current-loc"><?php _e('Google location currently used: ', 'psp'); echo '<strong>'.$this->search_engine."</strong>"; ?></span>
														</span>
													</div>
													<div class="psp-panel-content">
														<div id="psp-add-keyword-block">
															<div style="float: left;width: 450px;">
																<label for="psp-new-keyword"><?php _e('Keyword:', 'psp');?></label>
																<input type="text" id="psp-new-keyword" name="psp-new-keyword" class="psp-new-keyword" value="" />
																<div style="clear:left;"></div>
																<label for="psp-new-keyword-link"><?php _e('URL:', 'psp');?></label>
																<input type="text" id="psp-new-keyword-link" name="psp-new-keyword-link" class="psp-new-keyword-link" value="" />
															</div>
															<div class="psp-or-block">
																<span class="line"></span>
																<h2><?php _e('OR', 'psp');?></h2>	
															</div>
															<div style="float: left;width: 300px; ">
																<label><?php _e('Select from your:', 'psp');?></label>
																<div style="clear:left;"></div>
																<input type="button" id="psp-select-fw" class="psp-button gray psp-select-fw" value="<?php _e('Focus keywords', 'psp');?>">
															</div>	
															<div style="clear:left;"></div>
															<input type="button" class="psp-button blue" id="psp-submit-to-reporter" value="<?php _e('Add to Reporter', 'psp');?>">
														</div>
														<div style="clear:left;"></div>
													</div>
												</div>
												
												
												<form class="psp-form" action="#save_with_ajax">
													<div class="psp-form-row psp-table-ajax-list" id="psp-table-ajax-response">
													<?php
													$html_rank_header = array();
													$html_rank_header[] = '<table class="serp-thead-rank">';
													$html_rank_header[] = 	'<thead>';
													$html_rank_header[] = 		'<tr>';
													$html_rank_header[] = 			'<th>' . ( __('Current', 'psp') ) . '</th>';
													$html_rank_header[] = 			'<th>' . ( __('Best', 'psp') ) . '</th>';
													$html_rank_header[] = 			'<th>' . ( __('Worst', 'psp') ) . '</th>';
													$html_rank_header[] = 		'</tr>';
													$html_rank_header[] = 	'</thead>';
													$html_rank_header[] = '</table>';

													pspAjaxListTable::getInstance( $this->the_plugin )
														->setup(array(
															'id' 				=> 'pspSERPKeywords',
															'custom_table'		=> "psp_serp_reporter",
															'show_header' 		=> true,
															'items_per_page' 	=> '10',
															'post_statuses' 	=> 'all',
															'columns'			=> array(
															
																'serp_focus_keyword' => array(
																	'th'	=> __('Focus Keyword', 'psp'),
																	'td'	=> '%serp_focus_keyword%',
																	'align' => 'left',
																	'width' => '150'
																),
																
																'serp_url'	=> array(
																	'th'	=> __('URL', 'psp'),
																	'td'	=> '%serp_url%',
																	'align' => 'left'
																),
																
																'serp_location'	=> array(
																	'th'	=> __('Location', 'psp'),
																	'td'	=> '%serp_location%',
																	'align' => 'center',
																	'width' => '80'
																),
																
																'serp_google'=> array(
																	'th'	=> __('Google Rank', 'psp') . implode("\n", $html_rank_header),
																	'td'	=> '%serp_google%',
																	'align' => 'center',
																	'width' => '120'
																),
																
																'serp_start_date' => array(
																	'th'	=> __('Start Date', 'psp'),
																	'td'	=> '%serp_start_date%',
																	'width' => '115'
																),
		
																'serp_visits'		=> array(
																	'th'	=> __('Visits', 'psp'),
																	'td'	=> '%serp_visits%',
																	'width' => '30'
																),
																
																'publish_btn' => array(
																	'th'	=> __('Status', 'psp'),
																	'td'	=> '%button_publish%',
																	'option' => array(
																		'value' => __('Unpublish', 'psp'),
																		'value_change' => __('Publish', 'psp'),
																		'action' => 'do_item_publish',
																		'color'	=> 'orange',
																	),
																	'width' => '40'
																),
																
																'update_btn' => array(
																	'th'	=> __('Update', 'psp'),
																	'td'	=> '%button%',
																	'option' => array(
																		'value' => __('Update', 'psp'),
																		'action' => 'do_item_update',
																		'color'	=> 'blue',
																	),
																	'width' => '35'
																),
					
																'delete_btn' => array(
																	'th'	=> __('Delete', 'psp'),
																	'td'	=> '%button%',
																	'option' => array(
																		'value' => __('Delete', 'psp'),
																		'action' => 'do_item_delete',
																		'color'	=> 'red',
																	),
																	'width' => '35'
																),
															)
														))
														->print_html();
										            ?>
										            </div>
									            </form>
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

		/*
		* getSERPGraphData, method
		* ------------------------
		*
		* this will create request to psp_serp_reporter table
		*/
		public function getSERPGraphData()
		{
			global $wpdb;
			
			$request = array(
				'engine' 	=> isset($_REQUEST['engine']) ? trim($_REQUEST['engine']) : '',
				'from_date' => isset($_REQUEST['from_date']) ? trim($_REQUEST['from_date']) : '',
				'to_date' 	=> isset($_REQUEST['to_date']) ? trim($_REQUEST['to_date']) : '',
				'keys' 		=> isset($_REQUEST['keys']) ? trim($_REQUEST['keys']) : '',
				'urls' 		=> isset($_REQUEST['urls']) ? trim($_REQUEST['urls']) : ''
			);

			//search engine
			$__dose = false;
			if ($request['engine']!='--all--') $__dose = true;

			//keys
			$request['keys_tmp'] = explode(',', $request['keys']);
			$q_key_clause = ($request['keys']!='' && is_array($request['keys_tmp']) && count($request['keys_tmp'])>0 ? ' a.focus_keyword in (' . implode(', ', array_map(array($this, 'prepareForInList'), $request['keys_tmp'])) . ') ' : '');

			//urls
			$request['urls_tmp'] = explode(',', $request['urls']);
			$q_url_clause = ($request['urls']!='' && is_array($request['urls_tmp']) && count($request['urls_tmp'])>0 ? ' a.url in (' . implode(', ', array_map(array($this, 'prepareForInList'), $request['urls_tmp'])) . ') ' : '');
			
			//keys + urls clause!
			$q_keyurl_clause = ($q_key_clause!='' && $q_url_clause!='' ? ' and (' . $q_key_clause . ' or ' . $q_url_clause . ') ' : ($q_key_clause!='' ? ' and ' . $q_key_clause : ($q_url_clause!='' ? ' and ' . $q_url_clause : '')));
			
			//default clause!
			if ($q_keyurl_clause=='') {
				$q_keyurl_clause .= $this->__defaultClause;
			}
			
			//Query
			$get_ranks_sql = $wpdb->prepare( "SELECT a.*, b.* FROM " . ( $wpdb->prefix ) . "psp_serp_reporter as a LEFT JOIN " . ( $wpdb->prefix ) . "psp_serp_reporter2rank as b ON a.id=report_id WHERE 1=1 " . ($__dose ? " AND a.search_engine='".$request['engine']."' " : " ") . $q_keyurl_clause . " AND b.report_day BETWEEN %s and %s order by b.report_day DESC;", $request['from_date'], $request['to_date'] );

			$results = $wpdb->get_results( $get_ranks_sql, ARRAY_A );
			
			// reorder array base on focus kw and link as key
			if( count($results) > 0 ){
				$serp_data = array();
				foreach ($results as $key => $value){
					unset($value['top100']);
					$serp_data[sanitize_text_field( $value['focus_keyword'] . '!!' . $value['url'] )][$value['report_day']] = $value;
				}
				
				if( count($serp_data) > 0 ){		
					$ret_data = array();
					foreach ($serp_data as $key => $value){
						
						// Alias 
						$alias = explode("!!", $key);
						$alias = $alias[0] . ' - ' . $alias[1];
						
						// rank per day
						$data = array();
						if( count($value) > 0 ){
							foreach ($value as $key2 => $value2) {
								$data[] = array( strtotime($value2['report_day']) * 1000, $value2['position']==999 ? 0 : $value2['position'] );
							}  
						}
						
						$ret_data[] = array(
							'label' => $alias,
							'data' 	=> $data
						);
					}
				}
				
				die( json_encode(
					array(
						'status' 	=> 'valid',
						'data'		=> $ret_data,
						'def_key'	=> isset($__latestKey2) ? $__latestKey2 : ''
						//,'sql'		=> $get_ranks_sql
					)
				));
			}
			
			die( json_encode(
				array(
					'status' 	=> 'invalid'
					//,'sql'		=> $get_ranks_sql
				)
			));
		}
		
		public function getDefaultClause( $request ) {
			global $wpdb;

			$q_keyurl_clause = '';

			$__dose = false;
			if ( $request['engine']!='' ) $__dose = true;

			//Error Code: 1235
			//This version of MySQL doesn't yet support 'LIMIT & IN/ALL/ANY/SOME subquery'
			//$q_keyurl_clause = " and a.focus_keyword in (select c.focus_keyword from " . ( $wpdb->prefix ) . "psp_serp_reporter as c where 1=1 group by c.focus_keyword order by c.created desc limit 5) ";
			/*$__latestKeyQuery = "select c.focus_keyword from " . ( $wpdb->prefix ) . "psp_serp_reporter as c where 1=1 " . ($__dose ? " AND c.search_engine='".$request['engine']."' " : " ") . " group by c.focus_keyword order by c.created desc limit 5;";

			$__latestKey = $wpdb->get_results( $__latestKeyQuery, ARRAY_A );
			$__latestKey2 = array();
			if (is_array($__latestKey) && count($__latestKey)>0) {
				foreach ($__latestKey as $k=>$v) {
					$__latestKey2[] = $v['focus_keyword'];
				}
			}
			$q_keyurl_clause = (is_array($__latestKey2) && count($__latestKey2)>0 ? ' a.focus_keyword in (' . implode(', ', array_map(array(self, 'prepareForInList'), $__latestKey2)) . ') ' : '');
			$q_keyurl_clause = ($q_keyurl_clause!='' ? ' and ' . $q_keyurl_clause  : '');*/

			$__latestKeyQuery = $wpdb->prepare( "SELECT a.focus_keyword, a.url FROM " . ( $wpdb->prefix ) . "psp_serp_reporter as a LEFT JOIN " . ( $wpdb->prefix ) . "psp_serp_reporter2rank as b ON a.id=report_id WHERE 1=1 " . ($__dose ? " AND a.search_engine='".$request['engine']."' " : " ") . " AND b.report_day BETWEEN %s and %s GROUP BY a.focus_keyword, a.url order by b.report_day DESC LIMIT 5;", $request['from_date'], $request['to_date'] );

			$__latestKey = $wpdb->get_results( $__latestKeyQuery, ARRAY_A );

			$__latestKey2 = array();
			if (is_array($__latestKey) && count($__latestKey)>0) {
				foreach ($__latestKey as $k=>$v) {
					$__latestKey2["{$v['url']}"] = $v['focus_keyword'];

					$_SESSION['psp_serp']['filter_keywords']["{$v['focus_keyword']}"] = true;
					$_SESSION['psp_serp']['filter_urls']["{$v['url']}"] = true;
				}
				$__tmp = array();
				foreach ($__latestKey2 as $kk=>$vv) {
					$__tmp[] = ( "('" . $kk . "', '" . $vv . "')");
				}
				$__tmp = implode(', ', $__tmp);
				$q_keyurl_clause = $__tmp!='' ? " (a.url, a.focus_keyword) in ( " . $__tmp . " ) " : "";
				$q_keyurl_clause = ($q_keyurl_clause!='' ? ' and ' . $q_keyurl_clause  : '');
			}
			return $q_keyurl_clause;
		}

		public function getInitialData() {
			global $wpdb;

			$sql = "
				SELECT COUNT(b.id) AS nb, b.report_day FROM " . ( $wpdb->prefix ) . "psp_serp_reporter AS a LEFT JOIN
				 " . ( $wpdb->prefix ) . "psp_serp_reporter2rank AS b ON a.id=report_id WHERE 1=1
				 AND b.position>0
				 GROUP BY b.report_day
				 HAVING nb>1
				 ORDER BY b.report_day DESC
				 limit 7;
			";
			$results = $wpdb->get_results( $sql, ARRAY_A );

			// reorder array
			$ret = array();
			if( count($results) > 0 ){
				foreach ($results as $kk=>$vv) {
					$ret[ $vv['report_day'] ] = $vv['nb'];
				}
			}
			return $ret;
		}

		/*
		* addToReporter, method
		* ---------------------
		*
		* this will create request to psp_serp_reporter table
		*/
		public function addToReporter( $keyword='', $link='', $itemid=0 )
		{
			$request = array(
				'action'	=> isset($_REQUEST['action']) ? trim($_REQUEST['action']) : 'pspAddToReporter',
				'subaction' => isset($_REQUEST['subaction']) ? trim($_REQUEST['subaction']) : '',
				'keyword' 	=> isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : $keyword,
				'link' 		=> isset($_REQUEST['link']) ? trim($_REQUEST['link']) : $link,
				'itemid' 	=> isset($_REQUEST['itemid']) ? trim($_REQUEST['itemid']) : $itemid,
				'return'	=> isset($_REQUEST['return']) ? trim($_REQUEST['return']) : ''
			); 

			$search_engine = $this->search_engine;
			
			// publish/unpublish
			if ( $request['subaction']=='publish' ) {

				//keep page number & items number per page
				$_SESSION['pspListTable']['keepvar'] = array('paged'=>true,'posts_per_page'=>true);
				
				// add to DB or update if is from new day
				$this->addToReportDB( array( 'keyword' => $request['keyword'], 'url' => $request['link'] ), 'default' );
				
				// return for ajax
				if ( $request['return'] == 'array' ) {
					return array(
						'status' => 'valid'
					);
				}
				die(json_encode( array(
					'status' => 'valid'
				)));
			}

			require_once( $this->the_plugin->cfg['paths']['scripts_dir_path'] . '/serp/serp.api.class.php' );
			$serp = pspSERPCheck::getInstance(); 
			
			$__spParams = array(
				'engine'		=> $search_engine,
				'keyword'		=> $request['keyword'],
				'link'			=> $request['link']
			);
			$serp->saveLog(true);
			$googleScoreInfo = $serp->__get_serp_score( $__spParams );
			if ( $googleScoreInfo===false || ( isset($googleScoreInfo['status']) 
				&& $googleScoreInfo['status']=='invalid' ) ) {
					
				if ( $request['action'] == 'pspUpdateToReporter' && $request['itemid'] > 0 ) { //update error message only for update!

					$this->googleAccessStatus( $request['itemid'], array(
						'status'	=> 'invalid',
						'msg'		=> $googleScoreInfo['msg']
					) );
				}
				
				$_SESSION['psp_engine_access_status'] = 'invalid';

				// return for ajax
				if ( $request['return'] == 'array' ) {
					return array(
						'status' => 'invalid'
					);
				}
				die(json_encode( array(
					'status' => 'invalid'
				)));
			}
			
			if ( $request['action'] == 'pspUpdateToReporter' ) {
				//keep page number & items number per page
				$_SESSION['pspListTable']['keepvar'] = array('paged'=>true,'posts_per_page'=>true);
			}
			
			// add to DB or update if is from new day
			$this->addToReportDB( $googleScoreInfo, 'default', $request['itemid'] );
			
			$_SESSION['psp_engine_access_status'] = 'valid';
				
			// return for ajax
			$retdata = $this->get_serp_scores( $request['keyword'], $request['link'], 'default' );
			if ( $request['return'] == 'array' ) {
				return array(
					'status' => 'valid',
					'data'	 => $retdata
				);
			}
			die(json_encode( array(
				'status' => 'valid',
				'data' 	 => $retdata
			)));
		}
		
		/*
		* get_serp_scores, method
		* -----------------------
		*
		* this will create request to psp_serp_reporter table
		*/
		public function get_serp_scores( $kw='', $link='', $se='default' )
		{
			global $wpdb;
			
			if ($se=='default')
				$se = $this->search_engine;
			
			$serpScoresSQL = $wpdb->prepare( "SELECT a.*, b.* FROM " . ( $wpdb->prefix ) . "psp_serp_reporter as a LEFT JOIN " . ( $wpdb->prefix ) . "psp_serp_reporter2rank as b ON a.id=report_id WHERE 1=1 AND a.focus_keyword=%s AND a.url=%s AND a.search_engine=%s;", $kw, $link, $se );
			return $wpdb->get_results( $serpScoresSQL, ARRAY_A );
		}
		
		/**
		 * update google access error status!
		 */
		public function googleAccessStatus( $row_id=0, $status=array() ) {
			global $wpdb;
			
			if ( $row_id == 0 ) return false;

			// update report - previous, worst, best rank!
			$wpdb->update(
				$wpdb->prefix . "psp_serp_reporter",
				array(
					'last_check_data'	=> date("Y-m-d H:i:s"),
					'last_check_status' => $status['status'],
					'last_check_msg'	=> $status['msg']
				),
				array( 'id' => $row_id ),
				array(
					'%s',
					'%s',
					'%s'
				),
				array( '%d' )
			);
		}
		
		
		/*
		* updateToReporter, method
		* --------------------------
		*
		* this will create request to psp_serp_reporter table
		*/
		public function updateToReporter()
		{
			global $wpdb;
			
			$request = array(
				'itemid' 	=> isset($_REQUEST['itemid']) ? (int)$_REQUEST['itemid'] : 0
			);
			
			if( $request['itemid'] > 0 ){
				$row = $wpdb->get_row( "SELECT * FROM " . ( $wpdb->prefix ) . "psp_serp_reporter WHERE id = '" . ( $request['itemid'] ) . "'", ARRAY_A );
				 
				// this function will automaticaly detect if already have this item and just update the score
				$this->addToReporter( $row['focus_keyword'], $row['url'] );
				
				die(json_encode(array(
					'status' => 'valid'
				)));
			}
			
			die(json_encode(array(
				'status' => 'invalid'
			)));
		}
		
		
		/*
		* removeFromReporter, method
		* --------------------------
		*
		* this will create request to psp_serp_reporter table
		*/
		public function removeFromReporter()
		{
			global $wpdb;
			
			$request = array(
				'itemid' 	=> isset($_REQUEST['itemid']) ? (int)$_REQUEST['itemid'] : 0
			);
			
			if( $request['itemid'] > 0 ){
				$wpdb->delete( 
					$wpdb->prefix . "psp_serp_reporter", 
					array( 'id' => $request['itemid'] ) 
				);
				
				$wpdb->delete( 
					$wpdb->prefix . "psp_serp_reporter2rank", 
					array( 'report_id' => $request['itemid'] ) 
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
		
		/*
		* addToReportDB, method
		* ---------------------
		*
		* this will create request to psp_serp_reporter table
		*/
		public function addToReportDB( $scoreArray=array(), $search_engine='default', $itemid=0 )
		{
			global $wpdb;
			
			// helper today date
			$today = date("Y-m-d");
			
			if ($search_engine=='default')
				$search_engine = $this->search_engine;

			// check if you already have this info into DB 
			$checkSQL = $wpdb->prepare( "SELECT a.id as rowid, a.*, b.* FROM " . ( $wpdb->prefix ) . "psp_serp_reporter as a LEFT JOIN " . ( $wpdb->prefix ) . "psp_serp_reporter2rank as b ON a.id=b.report_id WHERE 1=1 AND a.focus_keyword=%s AND a.url=%s AND a.search_engine=%s order by b.report_day DESC LIMIT 1", $scoreArray['keyword'], $scoreArray['url'], $search_engine );

			$row = $wpdb->get_row( $checkSQL, ARRAY_A );
			$row_id = (int)$row['rowid'];
  
			if ( $row_id > 0 ) {
  
					$request = array(
						'subaction' => isset($_REQUEST['subaction']) ? trim($_REQUEST['subaction']) : ''
					);
				
					// publish/unpublish
					if ( $request['subaction']=='publish' ) {
						$wpdb->update( 
							$wpdb->prefix . "psp_serp_reporter", 
							array( 
								'publish'		=> $row['publish']=='Y' ? 'N' : 'Y'
							), 
							array( 'id' => $row_id ), 
							array( 
								'%s'
							), 
							array( '%d' ) 
						);
						return true;
					}
			}

			// if not found
			if( $row_id == 0 ){
  
				// add new row into report table
				$wpdb->insert( 
					$wpdb->prefix . "psp_serp_reporter", 
					array( 
						'focus_keyword' => $scoreArray['keyword'], 
						'url' 			=> $scoreArray['url'],
						'search_engine' => $search_engine,
						'post_id'		=> $itemid,
						'position' 		=> $scoreArray['pos'],
						'position_prev' => $scoreArray['pos'],
						'position_worst'=> $scoreArray['pos'],
						'position_best' => $scoreArray['pos'],
						'last_check_status'	=> 'valid',
						'last_check_data'	=> date("Y-m-d H:i:s"),
						'last_check_msg'	=> ''
					), 
					array( 
						'%s',
						'%s',
						'%s',
						'%d',
						'%d',
						'%d',
						'%d',
						'%d',
						'%s',
						'%s',
						'%s'
					) 
				);
				$insert_id = $wpdb->insert_id;
				
				// add row into rank table
				$insert_id2 = $wpdb->insert( 
					$wpdb->prefix . "psp_serp_reporter2rank", 
					array( 
						'report_id' 	=> $insert_id, 
						'position' 		=> $scoreArray['pos'],
						'top100' 		=> @serialize($scoreArray['top100']),
						'report_day' 	=> date("Y-m-d")
					), 
					array( 
						'%d',
						'%d',
						'%s',
						'%s'
					) 
				);
			}
			
			// new rank for the same report row!
			elseif( $row['report_day'] < $today ){
  
				// add row into rank table
				$insert_id = $wpdb->insert( 
					$wpdb->prefix . "psp_serp_reporter2rank", 
					array( 
						'report_id' 	=> $row_id, 
						'position' 		=> $scoreArray['pos'],
						'top100' 		=> @serialize($scoreArray['top100']),
						'report_day' 	=> date("Y-m-d")
					), 
					array( 
						'%d',
						'%d',
						'%s',
						'%s',
					) 
				);
				
				// best & worst ranks!
				$__ranks = $this->getCustomRanks($row_id);
				
				// update report - previous, worst, best rank!
				$wpdb->update( 
					$wpdb->prefix . "psp_serp_reporter", 
					array( 
						'position' 		=> $scoreArray['pos'],
						'position_prev'	=> $row['position'],
						'position_worst'=> $__ranks['rank_worst'],
						'position_best' => $__ranks['rank_best'],
						'last_check_status'	=> 'valid',
						'last_check_data'	=> date("Y-m-d H:i:s"),
						'last_check_msg'	=> ''
					), 
					array( 'id' => $row_id ), 
					array( 
						'%d',
						'%d',
						'%d',
						'%d',
						'%s',
						'%s',
						'%s'
					), 
					array( '%d' ) 
				);
			}
			
			// update rank for the same report row - same day!
			else{
  
				$row2 = $wpdb->get_row( "SELECT * FROM " . ( $wpdb->prefix ) . "psp_serp_reporter2rank WHERE report_id = '" . ( $row_id ) . "' and report_day='" . ( $today ) . "'", ARRAY_A );
  
				// update rank
				$wpdb->update( 
					$wpdb->prefix . "psp_serp_reporter2rank", 
					array( 
						'position' 		=> $scoreArray['pos'],
						'top100' 		=> @serialize($scoreArray['top100'])
					), 
					array( 'id' => $row2['id'] ), 
					array( 
						'%d',
						'%s'
					), 
					array( '%d' ) 
				);
  
				// best & worst ranks!
				$__ranks = $this->getCustomRanks($row_id);
				
				// update report - previous, worst, best rank!
				$wpdb->update( 
					$wpdb->prefix . "psp_serp_reporter", 
					array( 
						'position' 		=> $scoreArray['pos'],
						'position_prev'	=> $row['position'],
						'position_worst'=> $__ranks['rank_worst'],
						'position_best' => $__ranks['rank_best'],
						'last_check_status'	=> 'valid',
						'last_check_data'	=> date("Y-m-d H:i:s"),
						'last_check_msg'	=> ''
					), 
					array( 'id' => $row_id ), 
					array( 
						'%d',
						'%d',
						'%d',
						'%d',
						'%s',
						'%s',
						'%s'
					), 
					array( '%d' ) 
				);
			}
  
			$request['wait_time'] = isset($_REQUEST['wait_time']) ? (int) $_REQUEST['wait_time'] : 0;  //(int) value in seconds!
			if ( $request['wait_time'] > 0 )
				$_SESSION['psp_engine_access_time'] = $request['wait_time'];

			return true;
		}
		
		public function getCustomRanks($report_id) {
			global $wpdb;
			
			// get best rank
			$best_rank_data = $this->the_plugin->db->get_row( "SELECT position FROM " . ( $this->the_plugin->db->prefix ) . "psp_serp_reporter2rank where 1=1 and report_id='" . ( $report_id ) . "' order by position asc limit 1;", ARRAY_A );
			/* and position>0
			if ( is_null($best_rank_data) || empty($best_rank_data) )
				$best_pos = 0; // assume not in top 100!
			else
				$best_pos = (int) $best_rank_data['position'];*/
			$best_pos = (int) $best_rank_data['position'];

			// get worst
			$worst_rank_data = $this->the_plugin->db->get_row( "SELECT position FROM " . ( $this->the_plugin->db->prefix ) . "psp_serp_reporter2rank where 1=1 and report_id='" . ( $report_id ) . "' order by position desc limit 1;", ARRAY_A );
			/*$worst_rank_data = $this->the_plugin->db->get_row( "SELECT position FROM " . ( $this->the_plugin->db->prefix ) . "psp_serp_reporter2rank where 1=1 and report_id='" . ( $report_id ) . "' and position=0 limit 1;", ARRAY_A );
			if ( is_null($worst_rank_data) || empty($worst_rank_data) ) {
				$worst_rank_data = $this->the_plugin->db->get_row( "SELECT position FROM " . ( $this->the_plugin->db->prefix ) . "psp_serp_reporter2rank where 1=1 and report_id='" . ( $report_id ) . "' order by position desc limit 1;", ARRAY_A );
				$worst_pos = (int) $worst_rank_data['position'];
			} else {
				$worst_pos = 0; // assume not in top 100!
			}*/
			$worst_pos = (int) $worst_rank_data['position'];
			
			return array(
				'rank_best' => $best_pos,
				'rank_worst'=> $worst_pos
			);
		}
		
		public function __getEngineAccessTime()
		{
			$last_msg = '';
			if ( isset($_SESSION['psp_engine_access_status']) ) {
				
				if ( $_SESSION['psp_engine_access_status']=='valid' ) {
					$last_msg = '<div class="psp-message psp-success">';
					$last_msg .= __('<span class="engine-access-msg-success">' . 'Response received from Google API.' . '</span>', 'psp');
					
				} else {
					$last_msg = '<div class="psp-message psp-error">';
					$last_msg .= __('<span class="engine-access-msg-error">' . 'Could not retrieve response from Google API - you might have used all your available requests for this day!' . '</span>', 'psp');
				}
			}
			
			$settings = $this->the_plugin->getAllSettings( 'array', 'serp' );
			$nbReqMax = $settings['nbreq_max_limit'];
			
			$currentReqInfo = get_option('psp_serp_nbrequests');
			$currentNbReq = (int) $currentReqInfo['nbreq'];
			$currentData = $currentReqInfo['data'];

			die( json_encode(array(
				'status' 	=> 'valid',
				'data' 		=> isset($_SESSION['psp_engine_access_time']) && $_SESSION['psp_engine_access_time']>0 ? $_SESSION['psp_engine_access_time'] : 0,
				'last_msg'	=> $last_msg,
				'nb_req'	=> sprintf( __('<span class="engine-access-msg-info">' . 'The number of requests made is <strong>%s</strong> (of maximum %s per day).' . '</span></div>', 'psp'), $currentNbReq, $nbReqMax )
			)) );
		}

		/*
		* __getFocusKW, method
		* --------------------
		*
		* this will create requesto to 404 table
		*/
		public function __getFocusKW()
		{
			global $wpdb;
			$html = array();

			$html[] = '<table class="psp-table" style="width: 100%;border: 1px solid #dadada;background: #fff;margin: -10px 0px 0px 0px;" cellspacing="0" cellpadding="0">'; 
			$html[] = 	'<thead>'; 
			$html[] = 		'<tr>'; 
			$html[] = 			'<th>' . __('ID', 'psp') . '</th>'; 
			$html[] = 			'<th>' . __('Focus Keywords', 'psp') . '</th>'; 
			$html[] = 			'<th align="left">' . __('Permalink', 'psp') . '</th>';
			$html[] = 			'<th></th>';  
			$html[] = 		'</tr>'; 
			$html[] = 	'</thead>'; 
			
			// get all focus keywords from post_meta table 
			$results = $wpdb->get_results( "SELECT  * FROM " . ( $wpdb->prefix ) . "postmeta WHERE `meta_key` = 'psp_kw' and meta_value != '' ", ARRAY_A);
			$html[] = '<tbody>'; 
			if( count($results) > 0 ){
				foreach ($results as $key => $value){
					$permalink = get_permalink($value['post_id']);
					$html[] = '<tr>'; 
					$html[] = 	'<td width="50" style="text-align: center;">' . ( $value['post_id'] ). '</td>';
					$html[] = 	'<td width="120" style="text-align: center;">' . ( $value['meta_value'] ). '</td>';
					$html[] = 	'<td>' . ( $permalink ). '</td>';
					$html[] = 	'<td><input type="button" data-itemid="' . ( $value['post_id'] ) . '" data-permalink="' . ( $permalink ) . '" data-keyword="' . ( $value['meta_value'] ) . '" value="' . __('Add to Reporter', 'psp') . '" class="psp-button blue psp-this-select-fw"></td>';
					$html[] = '</tr>';
				}
			}else{
				$html[] = '<tr><td rowspan="3">' . __('No focus keywords for you posts', 'psp') . '</td></tr>';
			}
			
			$html[] = '<tbody>';
			
			$html[] = '</table>'; 
			
			// die(implode("\n", $html)); // debug
			die( json_encode(array(
				'status' => 'valid',
				'html'	=> implode("\n", $html)
			)) );
		}
		
		/*
		* check_reporter, method
		* --------------------
		*
		* this will check search engine rank for all rows in psp_serp_reporter
		*/
		public function check_reporter() {
			@ini_set('max_execution_time', 0);
			@set_time_limit(0); // infinte
	
			global $wpdb;

			$__tasks = array();

			//retrives (url, keyword) pairs which have common keywords!
			$sql = "SELECT a.id, a.focus_keyword, a.search_engine, COUNT(a.id) AS nb FROM " . ( $wpdb->prefix ) . "psp_serp_reporter as a WHERE 1=1 and a.publish='Y' GROUP BY a.focus_keyword, a.search_engine HAVING nb>1 ORDER BY a.id ASC;";
			$res = $wpdb->get_results( $sql, ARRAY_A );

			// exit if no tasks to be run
			if(count($res) > 0){
				$__tasks[0] = $res;
			}
			
			//retrives (url, keyword) pairs which don't have common keywords!
			$sql2 = "SELECT a.url, a.id, a.focus_keyword, a.search_engine, COUNT(a.id) AS nb FROM " . ( $wpdb->prefix ) . "psp_serp_reporter as a WHERE 1=1 and a.publish='Y' GROUP BY a.focus_keyword, a.search_engine HAVING nb<=1 ORDER BY a.id ASC;";
			$res2 = $wpdb->get_results( $sql2, ARRAY_A );

			// exit if no tasks to be run
			if(count($res2) > 0){
				$__tasks[1] = $res2;
			}

			require_once( $this->the_plugin->cfg['paths']['scripts_dir_path'] . '/serp/serp.api.class.php' );
			$serp = pspSERPCheck::getInstance();

			// loop tasks
			foreach ($__tasks as $__k => $__v) {
				foreach ($__v as $key => $value) {
					if ($__k==0) { //use cache!

						$sql_url = $wpdb->prepare( "SELECT a.id, a.focus_keyword, a.url, a.search_engine FROM " . ( $wpdb->prefix ) . "psp_serp_reporter as a WHERE 1=1 and a.publish='Y' and a.focus_keyword=%s and a.search_engine=%s ORDER BY a.id ASC;", $value['focus_keyword'], $value['search_engine'] );
						$res_url = $wpdb->get_results( $sql_url, ARRAY_A );

						if(count($res_url) > 0){
							foreach ($res_url as $ku=>$vu) {
								$__spParams = array(
									'engine'		=> $value['search_engine'],
									'keyword'		=> $value['focus_keyword'],
									'link'			=> $vu['url'],
									'dopause'		=> $this->serp_sleep
								);
								$googleScoreInfo = $serp->__get_serp_score( $__spParams );

								if ($googleScoreInfo===false || ( isset($googleScoreInfo['status'])
									&& $googleScoreInfo['status']=='invalid' )) {

									$this->googleAccessStatus( $vu['id'], array(
										'status'	=> 'invalid',
										'msg'		=> $googleScoreInfo['msg']
									) );
								} else {
									// add to DB or update if is from new day
									$this->addToReportDB( $googleScoreInfo, $value['search_engine'] );
								}
							}
						}
					} else {
						$__spParams = array(
							'engine'		=> $value['search_engine'],
							'keyword'		=> $value['focus_keyword'],
							'link'			=> $value['url'],
							'dopause'		=> $this->serp_sleep
						);
						$googleScoreInfo = $serp->__get_serp_score( $__spParams );
						
						if ($googleScoreInfo===false || ( isset($googleScoreInfo['status'])
							&& $googleScoreInfo['status']=='invalid' )) {

							$this->googleAccessStatus( $value['id'], array(
								'status'	=> 'invalid',
								'msg'		=> $googleScoreInfo['msg']
							) );
						} else {
							// add to DB or update if is from new day
							$this->addToReportDB( $googleScoreInfo, $value['search_engine'] );
						}
					}
				}
			}

			//send email!
			$this->cron_reporter_email();
			
			// return for ajax
			die(json_encode( array(
				'status' => 'valid',
				'msg' => ''
			)));
		}
		
		public function cron_add_custom( $schedules ) {
			// Adds to the existing schedules.
			$schedules['daily'] = array(
				'interval' => 86400, //that's how many seconds are in 1 day, for the unix timestamp
				'display' => __('Once Daily', 'psp')
			);
			return $schedules;
		}
		
		/*
		* cron_reporter_email, method
		* --------------------
		*
		* this will send and email with ranks!
		*/
		public function cron_reporter_email() {
			global $wpdb;
			
			// select from DB
			$myQuery = "SELECT a.* FROM " . ( $wpdb->prefix . "psp_serp_reporter" ) . " as a WHERE 1=1 ";
			$myQuery .= " and a.publish = 'Y' ";
		    $myQuery .= " and a.position != a.position_prev ";
			$result_query = $myQuery;
		    $result_query .= " ORDER BY a.focus_keyword DESC;";

		    $query_res = $wpdb->get_results( $result_query, ARRAY_A);
		    
		    $items = array();
		    foreach ($query_res as $key => $myrow){
		    	//if( $opt["custom_table"] == 'psp_serp_reporter' ) {
		    		$pages[$myrow['id']] = array(
			    		'id' 			=> $myrow['id'],
			    		'focus_keyword' => $myrow['focus_keyword'],
			    		'url' 			=> $myrow['url'],
			    		'position' 		=> $myrow['position'],
			    		'position_prev'	=> $myrow['position_prev'],
			    		'position_worst'=> $myrow['position_worst'],
			    		'position_best'	=> $myrow['position_best'],
			    		'visits' 		=> $myrow['visits'],
			    		'created' 		=> $myrow['created']
		    		);
		    	//}
		    }
		    $items = $pages;

		    $items_nr = 0;
		    $items_nr = $wpdb->get_var( str_replace("a.*", "count(a.id) as nbRow", $myQuery) );

		    if ($items_nr<=0) {
		    	return false;
		    }


			// get the email template
			ob_start();
			require_once( $this->the_plugin->cfg['paths']["design_dir_path"] . '/serp_email.html' );
			$output = ob_get_contents();
			ob_end_clean();

			
			//html body - rows
			foreach ($items as $post){
				$html[] = '<tr data-itemid="' . ( $post['id'] ) . '">';

				$rank_data = $post;

				$html[] = '<td style="text-align: left;">';
				$html[] = '' . ( $post['focus_keyword'] ) . '';
				$html[] = '</td>';
				
				$html[] = '<td style="text-align: left;">';
				$html[] = '' . ( $post['url'] ) . '';
				$html[] = '</td>';

				$html[] = '<td style="text-align: left;">';
				if( isset($rank_data) && is_array($rank_data) && count($rank_data) > 0 ){
					// get best rank
					$best_pos = (int) $post['position_best'];

					// get worst
					$worst_pos = (int) $post['position_worst'];

					// current rank
					$current_pos = (int) $rank_data['position'];

					// previous rank
					$previous_pos = (int) $rank_data['position_prev'];

					//direction icon!
					$icon = 'icon_same.png';
					if( $current_pos > $previous_pos ){
						$icon = 'icon_down.png';
					}
					if( $current_pos < $previous_pos ){
						$icon = 'icon_up.png';
					}

					$__notInTop100 = __('Not in top 100', 'psp');
					$__icon_not100 = '<img src="' . ($this->the_plugin->cfg['paths']['plugin_dir_url']) . 'modules/serp/assets/icon_not100.png" width="" height="" title="' . $__notInTop100 . '">';

					$__icon = '<img src="' . ($this->the_plugin->cfg['paths']['plugin_dir_url']) . 'modules/serp/assets/' . ($icon) . '" width="" height="">';
					$__iconExtra = '';
					if (preg_match("/up/i", $icon)) {
						$__iconExtra .= '('.($previous_pos==999 ? '~' : '').'&#43;' . ( $previous_pos==999 ? (int) (100 - $current_pos) : (int) ($previous_pos - $current_pos) ) . ')';
					}
					else if(preg_match("/down/i", $icon)) {
						$__iconExtra .= '('.($current_pos==999 ? '~' : '').'&minus;' . ($current_pos==999 ? (int) (100 - $previous_pos) : (int) ($current_pos - $previous_pos) ) . ')';
					}
					$__icon .= $__iconExtra;
						
					$html[] = '<div style="position: relative; margin: -8px -10px 0px -10px; width: 100%;">';
					$html[] = 	'<table style="width: 200px; position: absolute; top: -14px; left: 0px; height: 43px; font-weight: bold;">';
					$html[] = 		'<tbody>';
					$html[] = 			'<tr>';
					$html[] = 					'<td width="90" align="center">' . ( $current_pos==999? $__icon_not100 . '&nbsp;&nbsp;' . $__iconExtra : '#'.$current_pos . '&nbsp;&nbsp;' . $__icon ) . '</td>';
					$html[] = 					'<td width="90" align="center">' . ( $previous_pos==999 ? $__icon_not100 : '#'.$previous_pos ) . '</td>';
					$html[] = 			'</tr>';
					$html[] = 		'</tbody>';
					$html[] = 	'</table>';
					$html[] = '</div>';
				}
				$html[] = '</td>';

				$html[] = '<td style="text-align: left;">';
				$html[] = '' . ( $post['created'] ) . '';
				$html[] = '</td>';

				$html[] = '<td style="text-align: left;">';
				$html[] = '' . ( $post['visits'] ) . '';
				$html[] = '</td>';
				
				$html[] = '</tr>';
			} //end foreach
			
            $__html_res = implode("\n", $html);
            
            
			// start make the replacements 
			$output = str_replace("{website_name}", get_bloginfo('name'), $output);
			$output = str_replace("{plugin_name}", 'Premium SEO pack - Wordpress Plugin', $output);
			$output = str_replace("{website_address}", get_bloginfo('url'), $output);
			$output = str_replace("{serp_email_title}", __('SERP Keywords Ranking Changes', 'psp'), $output);
			
			$output = str_replace("{table_title}", __('Keywords with ranking positions changes on Google since the last rank check on', 'psp') . ' (' . ($items_nr) . ' ' . __('items', 'psp') . ')', $output);

			$output = str_replace("{Focus Keyword}", __('Focus Keyword', 'psp'), $output);
			$output = str_replace("{URL}", __('URL', 'psp'), $output);
			$output = str_replace("{Google Rank}", __('Google Rank', 'psp'), $output);
			$output = str_replace("{Current Rank}", __('Current Rank', 'psp'), $output);
			$output = str_replace("{Previous Rank}", __('Previous Rank', 'psp'), $output);
			$output = str_replace("{Start Date}", __('Start Date', 'psp'), $output);
			$output = str_replace("{Visits}", __('Visits', 'psp'), $output);

			$output = str_replace("{table_content}", $__html_res, $output);


            //send mail!
            if (isset($this->plugin_settings['cron_email']) && trim($this->plugin_settings['cron_email'])!='') {
            	//$subject = __('Alert | Keywords Ranking Changes | ', 'psp') . str_replace('http://', '', get_bloginfo('url'));
            	$subject = '[' . ( get_bloginfo('name') ) . ']' . __(' Alert | Keywords Ranking Changes | ', 'psp');
            	
            	$headers = array();
            	$headers[] = __('From: '.$this->the_plugin->details['plugin_name'].' SERP module | ', 'psp') . get_bloginfo('name') . " <" . get_bloginfo('admin_email') . ">";
            	$headers[] = "MIME-Version: 1.0";
		
            	add_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );
            	wp_mail(
            		$this->plugin_settings['cron_email'],
            		$subject,
            		$output,
            		$headers
            	);
            	// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
				remove_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );
            }
		}
		
		public function set_html_content_type() {
			return 'text/html';
		}
		
		private function getSearchEngineUsed() {
			global $wpdb;
			
			$serpScoresSQL = "SELECT a.search_engine FROM " . ( $wpdb->prefix ) . "psp_serp_reporter as a WHERE 1=1 GROUP BY a.search_engine;";
			$ret = $wpdb->get_results( $serpScoresSQL, ARRAY_A );
			$__ret = array();
			if ($ret!==false && count($ret)>0) {
				foreach ($ret as $__k=>$__v) {
					$__ret[] = $__v['search_engine'];
				}
			}
			return $__ret;
		}
		
		public function setSearchEngine() {
			global $wpdb;
			
			$request = array(
				'search_engine' 	=> isset($_REQUEST['search_engine']) ? trim($_REQUEST['search_engine']) : '',
			);
			if ($request['search_engine']!='') {
				$_SESSION['psp_serp']['search_engine'] = $request['search_engine'];
			}
			
			// return for ajax
			die(json_encode( array(
				'status' => 'valid'
			)));
		}
		
		private function getKeywordsList() {
			global $wpdb;
			
			$serpScoresSQL = "SELECT a.id, a.focus_keyword as info FROM " . ( $wpdb->prefix ) . "psp_serp_reporter as a WHERE 1=1 ";

			if (isset($_SESSION['psp_serp']['search_engine']) && !empty($_SESSION['psp_serp']['search_engine'])
			&& $_SESSION['psp_serp']['search_engine']!='--all--') {
				$serpScoresSQL = str_replace("1=1 ", " 1=1 and a.search_engine='".$_SESSION['psp_serp']['search_engine']."' ", $serpScoresSQL);
			}
			$serpScoresSQL .= " GROUP BY a.focus_keyword;";
			$ret = $wpdb->get_results( $serpScoresSQL, ARRAY_A );
			return $ret;
		}
		
		private function getUrlsList() {
			global $wpdb;
			
			$serpScoresSQL = "SELECT a.id, a.url as info FROM " . ( $wpdb->prefix ) . "psp_serp_reporter as a WHERE 1=1 ";

			if (isset($_SESSION['psp_serp']['search_engine']) && !empty($_SESSION['psp_serp']['search_engine'])
			&& $_SESSION['psp_serp']['search_engine']!='--all--') {
				$serpScoresSQL = str_replace("1=1 ", " 1=1 and a.search_engine='".$_SESSION['psp_serp']['search_engine']."' ", $serpScoresSQL);
			}
			$serpScoresSQL .= " GROUP BY a.url;";
			$ret = $wpdb->get_results( $serpScoresSQL, ARRAY_A );
			return $ret;
		}
		
		private function prepareForInList($v) {
			return "'".$v."'";
		}
    }
}

function pspSERP_cronReporter_event() {
	// Initialize the pspSERP class
	$pspSERP = new pspSERP();
	$pspSERP->check_reporter();
}

// Initialize the pspSERP class
//$pspSERP = new pspSERP();
$pspSERP = pspSERP::getInstance();